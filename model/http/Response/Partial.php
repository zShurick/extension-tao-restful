<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model\http\Response;


use oat\taoRestAPI\model\http\Response;

/**
 * Class Partial
 * Allow client to retrieve only the information they need
 *  options['query'] = 'type,title'
 *
 * @package oat\taoRestAPI\model\http\Response
 */
class Partial
{

    private $options = [
        'query' => '',
        'fields' => [],
    ];

    private $fields = [];

    public function __construct(Response &$response, $options = [])
    {

        $this->options = array_merge($this->options, $options);

        $this->setFields();
    }

    private function setFields()
    {
        if (!empty($this->options['query'])) {
            foreach (explode(',', $this->options['query']) as $field) {
                if (in_array($field, $this->options['fields'])) {
                    $this->fields[] = $field;
                }
            }
        }
    }

    public function getFields()
    {
        return $this->fields;
    }
}