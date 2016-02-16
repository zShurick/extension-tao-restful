<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model\example\v1;


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\exception\HttpRequestExceptionWithHeaders;
use oat\taoRestAPI\model\v1\http\filters\Filter;
use oat\taoRestAPI\model\v1\http\filters\Paginate;
use oat\taoRestAPI\model\v1\http\filters\Partial;
use oat\taoRestAPI\model\v1\http\filters\Sort;
use oat\taoRestAPI\model\v1\http\Request\Router;
use oat\taoRestAPI\test\v1\Mocks\DB;
use Request;
use Response;


class HttpRoute extends Router
{
    /**
     * @var Request
     */
    protected $req;

    /**
     * @var Response
     */
    protected $res;

    /**
     * @var DB
     */
    private $db;
    
    public function __construct()
    {
        $this->db = new DB();
    }

    public function __invoke(Request $request, Response $response)
    {
        $this->req = $request;
        $this->res = &$response;
        
        $this->runApiCommand($this->req->getMethod(), $this->req->getParameter('uri'));
    }

    // todo implement in response class or move upper with Exception
    private $httpHeaders = [];
    private $httpStatusCode = 200;
    public function getResponseHeaders()
    {
        return $this->httpHeaders;
    }
    private function addFilterHeadersInResponse(array $addHeaders)
    {
        if (count($addHeaders)) {
            foreach ($addHeaders as $name => $headers) {
                $this->httpHeaders[$name] = $headers; 
            }
        }
    }
    
    protected function getList()
    {
        $queryParams = $this->req->getParameters();

        $filter = new Filter([
            'query' => $queryParams,
            'fields' => array_keys($this->db->getResources()[0]),
        ]);

        try {
            $paginate = new Paginate([
                'query' => isset($queryParams['range']) ? $queryParams['range'] : '',
                'total' => count($this->db->getResources()),
                'paginationUrl' => 'http://api.taotest.example/v1/items?range=',
            ]);
        } catch (HttpRequestExceptionWithHeaders $e) {
            // add failed headers if exists
            $this->addFilterHeadersInResponse($e->getHeaders());
            throw new HttpRequestException($e->getMessage(), $e->getCode());
        }

        $partial = new Partial([
            'query' => isset($queryParams['fields']) ? $queryParams['fields'] : '',
            'fields' => array_keys($this->db->getResources()[0]),
        ]);

        $sort = new Sort(['query' => $queryParams]);

        $data = $this->db->searchInstances([

            // use filter by values
            'filters' => $filter->getFilters(),

            // columns
            'fields' => $partial->getFields(),

            // sort
            'sortBy' => $sort->getSorting(),

            // pagination
            'offset' => $paginate->offset(),
            'limit' => $paginate->length(),
        ]);

        $beforePaginationCount = count($this->db->searchInstances(['filters' => $filter->getFilters()]));

        $paginate->correctPaginationHeader(count($data), $beforePaginationCount);

        if ($paginate->getStatusCode()) {
            $this->httpStatusCode = $paginate->getStatusCode();
        }
        // success headers
        $this->addFilterHeadersInResponse($paginate->getHeaders());
        
        var_dump($this->httpHeaders, $this->httpStatusCode, $data);
    }
    
    protected function getOne()
    {
        echo 'one';
    }
}