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
use oat\taoRestAPI\model\Authentication\BasicAuthentication;
use oat\taoRestAPI\model\httpRequest\HttpDataFormat;
use oat\taoRestAPI\model\RestApi\v1\RestApiService;
use oat\taoRestAPI\test\Mocks\EnvironmentTrait;
use oat\taoRestAPI\test\Mocks\TestHttpRoute;

class RestApiTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;

    /**
     * @var RestApiService
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

        $this->restApiService = new RestApiService([
            //'auth' => new BasicAuthentication()
            'httpRoute' => new TestHttpRoute(),
            'encoder' => HttpDataFormat::encoder(),
        ]);

    }

    public function testGetList()
    {
        $self = $this;
        $this->request('GET', '/list', function ($req, $res) use ($self) {
            
            $jqueryList = '';
            try{
                $jqueryList = $self->restApiService->execute();
            }catch(\Exception $e){
                
            }
            
            $res->write($jqueryList);
            return $res;
        });

        $this->assertEquals('resource 1name,id', (string)$this->response->getBody());

    }
}
