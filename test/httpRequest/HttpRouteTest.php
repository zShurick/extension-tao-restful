<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */


namespace oat\taoRestAPI\test\httpRequest;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\test\Mocks\TestHttpRoute;

class HttpRouteTest extends TaoPhpUnitTestRunner
{
    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testWithoutUri()
    {
        new TestHttpRoute();
    }
    
    public function testUriMap()
    {
        $route = new TestHttpRoute('/');
        $this->assertEquals('', $route->getKey());
        $route = new TestHttpRoute('/some-key');
        $this->assertEquals('some-key', $route->getKey());
        $route = new TestHttpRoute('/link/to/the/source/some-key');
        $this->assertEquals('some-key', $route->getKey());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testIncorrectHttpMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'FAILED';
        $route = new TestHttpRoute('/');
        $route->router();
    }
    
    public function testHttpGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $route = new TestHttpRoute('/');
        $this->assertEquals('list of the resources', $route->router());

        $route = new TestHttpRoute('link/someId');
        $this->assertEquals('one resource', $route->router());
    }
    
    public function testHttpPost()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $route = new TestHttpRoute('/');
        $this->assertEquals('resource', $route->router());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpPostException()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $route = new TestHttpRoute('/id');
        $route->router();
    }

    public function testHttpPut()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $route = new TestHttpRoute('/id');
        $this->assertEquals('resource', $route->router());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpPutException()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $route = new TestHttpRoute('/');
        $route->router();
    }

    public function testHttpPatch()
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $route = new TestHttpRoute('/id');
        $this->assertEquals('resource', $route->router());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpPatchException()
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $route = new TestHttpRoute('/');
        $route->router();
    }

    public function testHttpDelete()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $route = new TestHttpRoute('/id');
        $this->assertTrue($route->router());
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testHttpDeleteException()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $route = new TestHttpRoute('/');
        $route->router();
    }
    
    public function testHttpOptions()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $route = new TestHttpRoute('/');
        $this->assertEquals(['POST', 'GET', 'OPTIONS'], $route->router());
        $route = new TestHttpRoute('/id');
        $this->assertEquals(['GET', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route->router());
    }
}
