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
     * Order of operations for request
     * Searching
     * Filtering
     * Sorting
     * Pagination
     * Partial by fields
     * For testing we used several tests and group of tests from http/Response/*Test.php
     */

    /**
     * Global test for all operations
     */
    public function testHttpGetList()
    {
        $this->request('GET', '/resources', '/resources?range=0-3&fields=title,type', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        // filters
        // search
        // sort
        // paging
        $this->assertEquals(4, count($this->response->getResourceData()));
        // fields
        $this->assertEquals(2, count($this->response->getResourceData()[0]));

        $this->assertEquals(206, $this->response->getStatusCode());
        $this->assertEquals('Partial Content', $this->response->getReasonPhrase());
        $this->assertEquals(['0-3/5'], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals(4, count($this->response->getHeader('Link')));
    }

    /**
     * One resource
     */

    public function testHttpGetWithResource()
    {
        $this->request('GET', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $route->router();
            return $res;
        });

        $this->assertEquals('one resource 1', $this->response->getResourceData());
        $this->assertEquals(200, $this->response->getStatusCode());
    }
    
    public function testHttpGetWithResourceWithParams()
    {
        $this->request('GET', '/resources/{id}', '/resources/1?fields=field1,field2', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $route->router();
            return $res;
        });

        $this->assertEquals('one resource 1 field1,field2', $this->response->getResourceData());
        $this->assertEquals(200, $this->response->getStatusCode());
    }
    
    public function testHttpPost()
    {
        $this->request('POST', '/resources', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });

        $this->assertEquals('resource created', (string)$this->response->getBody());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpPostException()
    {
        $this->request('POST', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });
    }

    public function testHttpPut()
    {
        $this->request('PUT', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });

        $this->assertEquals('resource updated', (string)$this->response->getBody());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpPutException()
    {
        $this->request('PUT', '/resources', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });
    }

    public function testHttpPatch()
    {
        $this->request('PATCH', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });

        $this->assertEquals('resource updated partially', (string)$this->response->getBody());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpPatchException()
    {
        $this->request('PATCH', '/resources', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });
    }

    public function testHttpDelete()
    {
        $this->request('DELETE', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });

        $this->assertEquals('1', (string)$this->response->getBody());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpDeleteException()
    {
        $this->request('DELETE', '/resources', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });
    }

    public function testHttpListResourcesOptions()
    {
        $this->request('OPTIONS', '/resources/', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write( implode(',', $route->router()) );

            return $res;
        });

        $this->assertEquals('POST,GET,OPTIONS', (string)$this->response->getBody());
    }
    
    public function testHttpResourceOptions()
    {
        $this->request('OPTIONS', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write( implode(',', $route->router()) );

            return $res;
        });

        $this->assertEquals('GET,PUT,PATCH,DELETE,OPTIONS', (string)$this->response->getBody());
    }
}
