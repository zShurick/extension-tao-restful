<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model\http\Response;


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\model\http\Response;

/**
 * Class Paginate
 * Paging in Http request
 *
 * Allow partial content, with possible HTTP responses:
 * # 200 Ok - All resources data fit in a response
 * # 206 Partial Content - Response only part of the resources data
 * # 400 Bad Request - Invalid requested range
 *
 * Headers
 * "Content-Range" - 0-24/48 - [offset - limit / count]
 * "Accept-Range" - resource 50 - [resource] - type of the resources, [50] - maximum number of resources that allowed
 *                  to get for the single request
 *
 * Also provide pagination
 * "Link" - navigation links such as next page, previous page and last page
 *          &lt;https://api.example.com/v1/items?range=0-7&gt;; rel="first",
 *          &lt;https://api.example.com/v1/items?range=40-47&gt;; rel="prev",
 *          &lt;https://api.example.com/v1/itemss?range=56-64&gt;; rel="next",
 *          &lt;https://api.example.com/v1/items?range=56-64&gt;; rel="last"
 *
 *
 * @package oat\taoRestAPI\model\http\Response
 */
class Paginate
{

    /**
     * @var array
     */
    private $options = [
        'acceptRange' => 50,
        'offset' => 0,
        'limit' => 0,
        'total' => 0,
        'paginationUrl' => '', // optional
    ];

    /**
     * @var Response
     */
    private $response;

    public function __construct(Response &$response, $options = [])
    {
        $this->response = &$response;
        
        $this->options = array_merge($this->options, $options);
        array_walk($this->options, function (&$value) {
            $value = intval($value);
        });

        if (isset($options['paginationUrl'])) {
            $this->options['paginationUrl'] = $options['paginationUrl'];
        }

        $this->validate();

        if ($this->options['limit'] <= 0) {
            $this->options['limit'] = $this->options['total'] < $this->options['acceptRange']
                ? $this->options['total']
                : $this->options['acceptRange'];

            --$this->options['limit'];
        }

        $this->setSuccessResponse();
    }

    /**
     * @throws HttpRequestException
     */
    private function validate()
    {
        if ($this->options['acceptRange'] <= 0) {
            $this->response = $this->response->withAddedHeader('Accept-Range', 'resource ' . $this->options['acceptRange']);
            throw new HttpRequestException('Invalid acceptRange', 400);
        } elseif ($this->options['total'] <= 0) {
            $this->response = $this->response->withAddedHeader('Accept-Range', 'resource ' . $this->options['acceptRange']);
            throw new HttpRequestException('Empty response data', 400);
        } elseif (
            // enormous range
            ($this->options['limit'] - $this->options['offset']) >= $this->options['total']
            // bad range
            || $this->options['offset'] > $this->options['limit']
            // invalid range
            || $this->options['offset'] >= $this->options['total']
            || $this->options['offset'] < 0

        ) {
            $this->response = $this->response->withAddedHeader('Accept-Range', 'resource ' . $this->options['acceptRange']);
            throw new HttpRequestException('Invalid range', 400);
        }

    }

    private function setSuccessResponse()
    {
        // all data in response
        if ($this->options['offset'] == 0 && $this->options['limit'] >= ($this->options['total'] - 1)
            && $this->options['limit'] < $this->options['acceptRange']
        ) {

            $this->response = $this->response->withStatus(200, 'OK');
            $this->response = $this->response->withAddedHeader('Content-Range', $this->options['offset'] . '-' . $this->options['limit'] . '/' . $this->options['total']);
            $this->response = $this->response->withAddedHeader('Accept-Range', 'resource ' . $this->options['acceptRange']);
        } // range is smaller than count records
        else {

            $limit = $this->options['limit'];

            if ($limit >= $this->options['total']) {
                $limit = $this->options['total'] - 1;
            }

            $this->response = $this->response->withStatus(206, 'Partial Content');
            $this->response = $this->response->withAddedHeader('Content-Range', $this->options['offset'] . '-' . $limit . '/' . $this->options['total']);
            $this->response = $this->response->withAddedHeader('Accept-Range', 'resource ' . $this->options['acceptRange']);

            if ($this->options['paginationUrl']) {
                $this->paginationLinks();
            }
        }
    }

    /** From router get link and generate new pagination Http header
     * Example:
     *  $link = http://api.taotesting.com/v1/items?range=
     *  $link = http://api.taotesting.com/v1/items?fields=field1,field2&sort=field1,field2&range=
     */
    private function paginationLinks()
    {
        $length = $this->options['limit'] - $this->options['offset'] + 1;
        
        $pages['first'] = '0-' . ($length-1);
        $pages['last'] = ($this->options['total'] - $length) . '-' . ($this->options['total'] - 1);
        
        $pages['prev'] = $this->options['offset'] - $length < 0
            ? $pages['last']
            : ($this->options['offset'] - $length) . '-' . ($this->options['offset'] - 1);
        
        $pages['next'] = $this->options['limit'] + 1 >= $this->options['total']
            ? $pages['first']
            : ($this->options['limit'] + 1) . '-' . ($this->options['limit'] + $length);

        $links = [];
        foreach ($pages as $rel => $range) {
            $links[] = '&lt;' . $this->options['paginationUrl'] . $range . '&gt;; rel="' . $rel . '"';
        }

        $this->response = $this->response->withAddedHeader('Link', $links);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Offset (like sql offset)
     * @return int
     */
    public function offset()
    {
        return $this->options['offset'];
    }

    /**
     * Limit of the records (like sql limit)
     * @return int
     */
    public function length()
    {
        return $this->options['limit'] - $this->options['offset'] + 1;
    }

}