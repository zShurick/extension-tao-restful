<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model\restApi;
use oat\taoRestAPI\model\httpRequest\HttpDataFormat;
use oat\taoRestAPI\model\RestApiInterface;
use oat\taoRestAPI\model\stack\RestApiStack;
use oat\taoRestAPI\test\Mocks\TestHttpRoute;

/**
 * Bring together all middlewares and modules for getting restApi service
 * 
 * Class RestApiServiceV1
 * @package oat\taoRestAPI\model\restApi
 */
class RestApiServiceV1 implements RestApiInterface
{
    
    /**
     * @var RestApiStack
     */
    private $stack;
    
    public function __construct()
    {
        $this->stack = new RestApiStack();
    }

    public function __invoke($req, $res)
    {
        
        $this->stack
            ->add(function ($req, $res, $next) {
                $route = new TestHttpRoute($req, $res);
                $this->resourceData = $route->router();
                return $res;
            })
            ->add(function ($req, $res, $next) {
                $res = $next($req, $res);
                $format = new HttpDataFormat();
                $res->write( $format->encoder($this->resourceData) );
                return $res;
            })
            ->callMiddlewareStack($req, $res);
    }
    
}