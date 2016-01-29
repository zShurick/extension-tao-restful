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
        $self = $this;
        $this->request('GET', '/', function ($req, $res, $args) use ($self) {
            $route = new TestHttpRoute($req, $res);
            $route->router();
            $self->assertEquals('list of the resources', $res->getResourceData());
            return $res;
        });

        $this->request('GET', '/resources', function ($req, $res, $args) use ($self) {
            $route = new TestHttpRoute($req, $res);
            $self->assertEquals('list of the resources', $route->router());

            return $res;
        });
    }
    
    public function testHttpGetWithResource()
    {
        $this->request('GET', '/resources/{id}', '/resources/1', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });

        $this->assertEquals('one resource 1', (string)$this->response->getBody());
    }
    
    public function testHttpGetWithResourceWithParams()
    {
        $this->request('GET', '/resources/{id}', '/resources/1?params=field1,field2', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            $res->write($route->router());

            return $res;
        });

        $this->assertEquals('one resource 1 field1,field2', (string)$this->response->getBody());
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
