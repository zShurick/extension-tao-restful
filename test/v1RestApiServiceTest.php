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

    public function testGet()
    {

        $callable = function ($req, $res) {
            $this->restApiService->get();
        };

        $route = $this->get('/', $callable);

        $this->assertInstanceOf('\Slim\Route', $route);
        $this->assertAttributeContains('GET', 'methods', $route);

        $router = $this->app->getContainer()->get('router');
        $this->assertAttributeEquals('/', 'pattern', $router->lookupRoute('route0'));
    }

}
