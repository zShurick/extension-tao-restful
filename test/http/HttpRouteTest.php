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

namespace oat\taoRestAPI\test\httpRequest;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\test\Mocks\EnvironmentTrait;
use oat\taoRestAPI\test\Mocks\TestHttpRoute;
use Slim\Http\Environment;
use Slim\Http\Request;

class HttpRouteTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;
    
    /**
     * @expectedException \FastRoute\BadRouteException
     */
    public function testWithoutUri()
    {
        $this->request('GET', '', function ($req, $res, $args) {
            new TestHttpRoute($req, $res);
            return $res;
        });
    }

    /**
     * ==========================
     *  Get list of the resources
     * 
     */
    
    /**
     * Order of operations for request
     * Filtering
     * Sorting
     * Pagination
     * Partial by fields
     * For testing we used several tests and group of tests from http/filters/*Test.php
     */

    public function testHttpGetSortedPartialRangeOfList()
    {
        $this->request('GET', '/resources', '/resources?sort=title&range=0-3&fields=title,type', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(4, count($this->response->getResourceData()));
        $this->assertEquals(2, count($this->response->getResourceData()[0]));
        $this->assertEquals(206, $this->response->getStatusCode());
        $this->assertEquals('Partial Content', $this->response->getReasonPhrase());
        $this->assertEquals(['0-3/5'], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals(4, count($this->response->getHeader('Link')));

        $titles = [];
        foreach ($this->response->getResourceData() as $item) {
            $titles[] = $item['title'];
        }

        $this->assertEquals(['Carrot', 'Lemon', 'Lime', 'Orange'], $titles);
    }

    public function testHttpGetFilteredSortedPartialRangeOfList()
    {
        $this->request('GET', '/resources', '/resources?sort=title&range=0-3&fields=title,type&type=citrus', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(3, count($this->response->getResourceData()));
        $this->assertEquals(2, count($this->response->getResourceData()[0]));
        $this->assertEquals(206, $this->response->getStatusCode());
        $this->assertEquals('Partial Content', $this->response->getReasonPhrase());
        $this->assertEquals(['0-2/5'], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals(4, count($this->response->getHeader('Link')));

        $titles = [];
        foreach ($this->response->getResourceData() as $item) {
            $titles[] = $item['title'];
        }

        $this->assertEquals(['Lemon', 'Lime', 'Orange'], $titles);
    }

    /**
     * ==========================
     *  Get one resource
     *
     */


    /**
     * Get one resource
     */
    public function testHttpGetWithResource()
    {
        $this->request('GET', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(5, count($this->response->getResourceData()));
        $this->assertEquals(1, $this->response->getResourceData()['id']);
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    /**
     * Get one resource partial
     */
    public function testHttpGetWithResourceWithParams()
    {
        $this->request('GET', '/resources/{id}', '/resources/5?fields=id,title,form', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(3, count($this->response->getResourceData()));
        $this->assertEquals(5, $this->response->getResourceData()['id']);
        $this->assertEquals('Orange', $this->response->getResourceData()['title']);
        $this->assertEquals('circle', $this->response->getResourceData()['form']);
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    /**
     * ==========================
     *  Create resource
     *
     */


    /**
     * Create new resource
     */
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

        $request = Request::createFromEnvironment($env);
        unset($_POST);

        $this->request('POST', '/resources', function ($req, $res, $args) use ($request) {
            (new TestHttpRoute($request, $res))->router();
            return $this->response = $res;
        });

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

        $request = Request::createFromEnvironment($env);
        unset($_POST);

        $this->request('POST', '/resources', function ($req, $res, $args) use ($request) {
            (new TestHttpRoute($request, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["Resource with id=1 exists."]}', (string)$this->response->getBody());
        $this->assertFalse($this->response->hasHeader('Location'));
    }

    public function testHttpPostWithoutData()
    {
        $this->request('POST', '/resources', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["Empty Request data."]}', (string)$this->response->getBody());
        $this->assertFalse($this->response->hasHeader('Location'));
    }

    public function testHttpPostException()
    {
        $this->request('POST', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["You can\'t create new resource on object"]}', (string)$this->response->getBody());
        $this->assertFalse($this->response->hasHeader('Location'));
    }

    /**
     * ==========================
     *  Update whole resource
     *
     */


    /**
     * Update full resource data
     * Update only fields that is set, all the other fields will be deleted
     */
    public function testHttpPut()
    {
        // replace default body data
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources/1',
            'REQUEST_METHOD' => 'PUT',
        ]);

        $request = Request::createFromEnvironment($env);
        unset($_POST);
        
        // add Attribute in request
        $request = $request->withAttribute('id', 1);
        $putData = [
            'id' => 1,
            'title' => 'beef',
            'type' => 'meat',
            'form' => 'circle',
            'color' => 'brown',
        ];
        $request = $request->withParsedBody($putData);
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');


        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('PUT', '/resources/{id}', '/resources/1', function ($req, $res, $args) use ($request, &$routing, $putData) {
            $routing = new TestHttpRoute($request, $res);
            $this->assertNotEquals($putData, $routing->getResources()[0]);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals($putData, $routing->getResources()[0]);
    }

    public function testHttpPartialPut()
    {
        // replace default body data
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources/1',
            'REQUEST_METHOD' => 'PUT',
        ]);

        $request = Request::createFromEnvironment($env);
        unset($_POST);

        // add Attribute in request
        $request = $request->withAttribute('id', 1);
        $putData = [
            'id' => 1,
            'title' => 'Potato',
            'color' => 'vegetable',
        ];
        $request = $request->withParsedBody($putData);
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');


        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('PUT', '/resources/{id}', '/resources/1', function ($req, $res, $args) use ($request, &$routing, $putData) {
            $routing = new TestHttpRoute($request, $res);
            $this->assertNotEquals($putData, $routing->getResources()[0]);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals($putData, $routing->getResources()[0]);
        $this->assertEquals(3, count($routing->getResources()[0]));
    }

    public function testHttpPutInvalidData()
    {
        // replace default body data
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources/1',
            'REQUEST_METHOD' => 'PUT',
        ]);

        $request = Request::createFromEnvironment($env);
        unset($_POST);

        // add Attribute in request
        $request = $request->withAttribute('id', 1);
        $putData = [
            'title' => 'Potato',
            'color' => 'vegetable',
        ];
        $request = $request->withParsedBody($putData);
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');


        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('PUT', '/resources/{id}', '/resources/1', function ($req, $res, $args) use ($request, &$routing, $putData) {
            $routing = new TestHttpRoute($request, $res);
            $this->assertNotEquals($putData, $routing->getResources()[0]);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertNotEquals($putData, $routing->getResources()[0]);
    }

    /**
     * ==========================
     *  Update resource partial
     *
     */


    /**
     * Partial data update
     * Update only the specified data
     */
    public function testHttpPatch()
    {
        // replace default body data
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources/1',
            'REQUEST_METHOD' => 'PATCH',
        ]);

        $request = Request::createFromEnvironment($env);
        unset($_POST);

        // add Attribute in request
        $request = $request->withAttribute('id', 1);
        $putData = [
            'title' => 'Carrot',
            'color' => 'orange',
        ];
        $request = $request->withParsedBody($putData);
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');


        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('PATCH', '/resources/{id}', '/resources/1', function ($req, $res, $args) use ($request, &$routing) {
            $routing = new TestHttpRoute($request, $res);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals(5, count($routing->getResources()[0]));
        $this->assertEquals('Carrot', $routing->getResources()[0]['title']);
        $this->assertEquals('orange', $routing->getResources()[0]['color']);
    }

    public function testHttpPatchOnListOfTheData()
    {
        // replace default body data
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources',
            'REQUEST_METHOD' => 'PATCH',
        ]);

        $request = Request::createFromEnvironment($env);
        unset($_POST);

        // add Attribute in request
        $putData = [
            'title' => 'Carrot',
            'color' => 'orange',
        ];
        $request = $request->withParsedBody($putData);
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');


        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('PATCH', '/resources', '/resources', function ($req, $res, $args) use ($request, &$routing) {
            $routing = new TestHttpRoute($request, $res);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["You can\'t update list of the resources"]}', (string)$this->response->getBody());
    }

    public function testHttpPatchWithError()
    {
        // replace default body data
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => '/resources/1',
            'REQUEST_METHOD' => 'PATCH',
        ]);

        $request = Request::createFromEnvironment($env);
        unset($_POST);

        // add Attribute in request
        $request = $request->withAttribute('id', 1);
        $putData = [
            'id' => 2,
            'title' => 'Carrot',
            'color' => 'orange',
        ];
        $request = $request->withParsedBody($putData);
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');


        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('PATCH', '/resources/{id}', '/resources/1', function ($req, $res, $args) use ($request, &$routing) {
            $routing = new TestHttpRoute($request, $res);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals('{"errors":["Invalid Id"]}', (string)$this->response->getBody());
    }

    /**
     * ==========================
     *  Delete resource
     *
     */


    public function testHttpDelete()
    {
        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('DELETE', '/resources/{id}', '/resources/1', function ($req, $res, $args) use (&$routing) {
            $routing = new TestHttpRoute($req, $res);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals(4, count($routing->getResources()));
    }

    public function testHttpDeleteOnList()
    {
        /** @var TestHttpRoute $routing */
        $routing = null;
        $this->request('DELETE', '/resources', function ($req, $res, $args) use (&$routing) {
            $routing = new TestHttpRoute($req, $res);
            $routing->router();
            return $this->response = $res;
        });

        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals(5, count($routing->getResources()));
    }

    /**
     * ==========================
     *  Get Restful Options
     *
     */


    public function testHttpListResourcesOptions()
    {
        $this->request('OPTIONS', '/resources/', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals('["POST","GET","OPTIONS"]', (string)$this->response->getBody());
    }
    
    public function testHttpResourceOptions()
    {
        $this->request('OPTIONS', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals('["GET","PUT","PATCH","DELETE","OPTIONS"]', (string)$this->response->getBody());
    }
}
