<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\test;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\model\stack\RestApiStack;
use oat\taoRestAPI\test\Mocks\EnvironmentTrait;
use oat\taoRestAPI\test\Mocks\TestHttpRoute;

class RestApiStackTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;

    /**
     * @var RestApiStack
     */
    private $restApiStack;

    public function setUp()
    {
        parent::setUp();

        TaoPhpUnitTestRunner::initTest();

        /**
         * In project use
         * $this->restApiService = ServiceManager::getServiceManager()->get(RestApiService::SERVICE_ID);
         */

        $this->restApiStack = new RestApiStack();
    }

    public function testGetList()
    {
        $self = $this;
        $this->request('GET', '/list', function ($req, $res) use ($self) {

            $self->restApiStack
                ->add(function ($req, $res, $next) {
                    $route = new TestHttpRoute($req, $res);
                    $route->router();
                    return $res;
                })
                ->add(function ($req, $res, $next) {
                    $res = $next($req, $res);
                    $res->write($this->getResourceData());
                    return $res;
                })
                ->callMiddlewareStack($req, $res);
    
            return $res;
        });
        
        $this->assertEquals('list of the resources', (string)$this->response->getBody());

    }
}
