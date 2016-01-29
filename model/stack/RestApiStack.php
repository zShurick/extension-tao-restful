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
     * @param $callable
     * @return $this
     */
    public function add($callable)
    {
        $this->addMiddleware($callable);
        return $this;
    }
}
