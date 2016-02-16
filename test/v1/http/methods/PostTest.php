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
use oat\taoRestAPI\test\v1\Mocks\EnvironmentTrait;
use oat\taoRestAPI\test\v1\Mocks\Response;
use Slim\Http\Environment;
use Slim\Http\Request;

class PostTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;
    
    public function testHttpPost()
    {
        // replace default post to post with data
        $_POST = [
            'id' => 7,
            'title' => 'beet',
            'type' => 'vegetable',
            'form' => 'ellipse',
            'color' => 'brown',
        ];

        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources',
            'REQUEST_METHOD' => 'POST',
            'HTTP_CONTENT_TYPE' => 'multipart/form-data;'
        ]);

        $this->request = Request::createFromEnvironment($env);
        unset($_POST);

        $this->response = new Response();

        $this->routerRunner($this->request, $this->response);

        $this->assertEquals(201, $this->response->getStatusCode());
        $this->assertEquals('Created', $this->response->getReasonPhrase());
        $this->assertEquals([$this->request->getUri() . '/7'], $this->response->getHeader('Location'));
    }
    
    public function testHttpPostInvalidData()
    {
        // replace default post to post with data
        $_POST = [
            'id' => 1,
            'title' => 'beet',
            'type' => 'vegetable',
            'form' => 'ellipse',
            'color' => 'brown',
        ];

        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources',
            'REQUEST_METHOD' => 'POST',
            'HTTP_CONTENT_TYPE' => 'multipart/form-data;'
        ]);

        $this->request = Request::createFromEnvironment($env);
        unset($_POST);

        $this->response = new Response();

        $this->routerRunner($this->request, $this->response);

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["Resource with id=1 exists."]}', (string)$this->response->getBody());
        $this->assertFalse($this->response->hasHeader('Location'));
    }

    public function testHttpPostWithoutData()
    {
        $this->request('POST', '/resources', function ($req, $res, $args) {
            return $this->routerRunner($req, $res, $args);
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["Empty Request data."]}', (string)$this->response->getBody());
        $this->assertFalse($this->response->hasHeader('Location'));
    }

    public function testHttpPostException()
    {
        $this->request('POST', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            return $this->routerRunner($req, $res, $args);
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["Forbidden to creating new resource on object"]}', (string)$this->response->getBody());
        $this->assertFalse($this->response->hasHeader('Location'));
    }
}
