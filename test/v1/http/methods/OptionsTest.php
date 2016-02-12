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
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 * 
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\test\v1\http\methods;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\model\v1\http\Response;
use oat\taoRestAPI\test\v1\Mocks\EnvironmentTrait;
use oat\taoRestAPI\test\v1\Mocks\TestHttpRoute;
use Slim\Http\Environment;
use Slim\Http\Request;

class OptionsTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;
   
    public function testHttpListResourcesOptions()
    {
        $this->request('OPTIONS', '/resources/', function ($req, $res, $args) {
            return $this->routerRunner($req, $res, $args);
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals('["POST","GET","OPTIONS"]', (string)$this->response->getBody());
    }
    
    public function testHttpResourceOptions()
    {
        $this->request('OPTIONS', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            return $this->routerRunner($req, $res, $args);
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals('["GET","PUT","PATCH","DELETE","OPTIONS"]', (string)$this->response->getBody());
    }
}
