<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\test\http\Response;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\test\Mocks\EnvironmentTrait;
use oat\taoRestAPI\test\Mocks\TestHttpRoute;

class PaginateTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;

    /**
     * Get all resources
     * with limit, if provided in controller
     */
    public function testHttpGetListAll()
    {
        $this->request('GET', '/resources', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });
        
        $this->assertEquals(5, count($this->response->getResourceData()));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals(['0-4/5'], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals(0, count($this->response->getHeader('Link')));
    }

    /**
     * Range is biggest, than allowed
     */
    public function testHttpGetListEnormousRange()
    {
        $this->request('GET', '/resources', '/resources?range=0-50', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        $this->assertEquals('', $this->response->getResourceData());
        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals([], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals('{"errors":["Invalid range"]}', (string)$this->response->getBody());
        $this->assertEquals(0, count($this->response->getHeader('Link')));
    }

    /**
     * Outside request
     */
    public function testHttpGetListOutsideRange()
    {
        $this->request('GET', '/resources', '/resources?range=50-51', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        $this->assertEquals('', $this->response->getResourceData());
        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals([], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals('{"errors":["Invalid range"]}', (string)$this->response->getBody());
        $this->assertEquals(0, count($this->response->getHeader('Link')));
    }

    /**
     * Invalid range
     */
    public function testHttpGetListInvalidRange()
    {
        // incorrect range
        $this->request('GET', '/resources', '/resources?range=3-2', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        $this->assertEquals('', $this->response->getResourceData());
        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals([], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals('{"errors":["Invalid range"]}', (string)$this->response->getBody());
        $this->assertEquals(0, count($this->response->getHeader('Link')));

        // less then 0
        $this->request('GET', '/resources', '/resources?range=-1-2', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        $this->assertEquals('', $this->response->getResourceData());
        $this->assertEquals(400, $this->response->getStatusCode());
        $this->assertEquals('Bad Request', $this->response->getReasonPhrase());
        $this->assertEquals([], $this->response->getHeader('Content-Range'));
        $this->assertEquals([], $this->response->getHeader('Accept-Range'));
        $this->assertEquals('{"errors":["Incorrect range parameter. Try to use: ?range=0-25"]}', (string)$this->response->getBody());
        $this->assertEquals(0, count($this->response->getHeader('Link')));

    }

    /**
     * Last data page
     */
    public function testHttpGetListLastRange()
    {
        $this->request('GET', '/resources', '/resources?range=3-7', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        $this->assertEquals(2, count($this->response->getResourceData()));
        $this->assertEquals(206, $this->response->getStatusCode());
        $this->assertEquals('Partial Content', $this->response->getReasonPhrase());
        $this->assertEquals(['3-4/5'], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals(4, count($this->response->getHeader('Link')));
    }

    /**
     * Range of resources
     */
    public function testHttpGetListRange()
    {
        $this->request('GET', '/resources', '/resources?range=0-3', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        $this->assertEquals(4, count($this->response->getResourceData()));
        $this->assertEquals(206, $this->response->getStatusCode());
        $this->assertEquals('Partial Content', $this->response->getReasonPhrase());
        $this->assertEquals(['0-3/5'], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
        $this->assertEquals(4, count($this->response->getHeader('Link')));
    }
    
}
