<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */


namespace oat\taoRestAPI\test;

use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\model\RestApiInterface;
use oat\taoRestAPI\model\v1\RestApiService;

/**
 * Class RestApiServiceTest
 * @package oat\taoRestAPI\test
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */
class v1RestApiServiceTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTestTrait;

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
        $this->assertEquals($this->restApiService->version(), '1');
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
