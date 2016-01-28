<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model\stack;


use oat\taoRestAPI\model\RestApiStackInterface;
use Slim\MiddlewareAwareTrait;

class RestApiStack implements RestApiStackInterface
{
    use MiddlewareAwareTrait;

    /**
     * For data transfer between middlewares 
     * # (init with HttpRoute, using in encoder)
     * 
     * @var mixed
     */
    private $resourceData;

    /**
     * @param $callable
     * @return $this
     */
    public function add($callable)
    {
        $this->addMiddleware($callable);
        return $this;
    }
}
