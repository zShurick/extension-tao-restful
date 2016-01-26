<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */


namespace oat\taoRestAPI\test\v1;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\model\RestApiInterface;
use oat\taoRestAPI\model\RestAPI\v1\RestApiService;
use oat\taoRestAPI\test\Mocks\EnvironmentTrait;

/**
 * Class RestApiServiceTest
 * @package oat\taoRestAPI\test
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */
class RestApiServiceTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;

    /**
     * @var RestApiInterface
     *
     * @author Alexander Zagovorichev <zagovorichev@gmail.com>
     */
    private $restApiService;

    public function setUp()
    {
        parent::setUp();

        TaoPhpUnitTestRunner::initTest();

        /**
         * In project use
         * $this->restApiService = ServiceManager::getServiceManager()->get(RestApiService::SERVICE_ID);
         */

        $this->restApiService = new RestApiService();
    }

    public function testVersion()
    {
        $this->assertLessThan(2, floatval(RestApiService::VERSION));
    }

    /**
     * Http Get
     * list of the resources
     */
    public function testGetList()
    {
        $self = $this;
        $this->request('GET', '/list', function ($req, $res) use ($self) {
            $resources = $self->restApiService->get();
            $self->assertTrue(is_array($resources));
            $res->write();
            return $res;
        });
        
        $this->assertEquals('resource 1name,id', (string)$this->response->getBody());

    }

    /**
     * Http Get
     * get one resource
     */
    public function testGetResource()
    {
        $self = $this;
        $this->request('GET', '/list/{id}', '/list/1', function ($req, $res, $args) use ($self) {
            $res->write($self->restApiService->get($args['id']));
            return $res;
        });

        $this->assertEquals('resource 1', (string)$this->response->getBody());
    }

    /**
     * Http Get
     * get partial data from resource (some fields)
     */
    public function testGetResourcePartial()
    {
        $self = $this;
        $this->request('GET', '/list/{id}', '/list/1?fields=name,id', function ($req, $res, $args) use ($self) {
            $res->write($self->restApiService->get($args['id'], $req->getQueryParam('fields')));
            return $res;
        });

        $this->assertEquals('resource 1name,id', (string)$this->response->getBody());
    }

    /**
     * Http Post
     * Create new resource
     */
    public function testCreateNewResource()
    {

    }

    /**
     * Http Put
     * Update resource
     */
    public function testUpdateResource()
    {

    }

    /**
     * Http Patch
     * Update one parameter in resource
     * for example "update date" will not be changed
     */
    public function testUpdateParam()
    {

    }

    /**
     * Http delete
     * Delete resource
     */
    public function testDeleteResource()
    {

    }

    /**
     * Http Options
     * Get link options
     */
    public function testOptions()
    {

    }
}
