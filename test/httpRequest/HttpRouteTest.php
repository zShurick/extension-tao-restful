<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
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
    
    public function testHttpGet()
    {
        $this->request('GET', '/resources', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $route->router();
            return $res;
        });

        $this->assertEquals('list of the resources', $this->response->getResourceData());
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testHttpGetListPagination()
    {
        $this->request('GET', '/resources?range=0-25', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $route->router();
            return $res;
        });

        $this->assertEquals('list of the resources', $this->response->getResourceData());
        $this->assertEquals(200, $this->response->getStatusCode());
    }


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
