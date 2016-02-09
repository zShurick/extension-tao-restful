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

class FilterTest extends TaoPhpUnitTestRunner
{

    use EnvironmentTrait;

    public function testFilter()
    {
        $this->request('GET', '/resources', '/resources?type=citrus', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });
        
        $this->assertEquals(5, count($this->response->getResourceData()[0]));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());

        foreach ($this->response->getResourceData() as $item) {
            $this->assertEquals('citrus', $item['type']);
        }
    }

    public function testMultipleFilter()
    {
        $this->request('GET', '/resources', '/resources?type=citrus,vegetable&form=circle', function ($req, $res, $args) {
            $route = new TestHttpRoute($req, $res);
            return $this->response = $route->router()->getResponse();
        });

        $this->assertEquals(5, count($this->response->getResourceData()[0]));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());

        foreach ($this->response->getResourceData() as $item) {
            $this->assertTrue(in_array($item['type'], ['citrus', 'vegetable']));
            $this->assertTrue(in_array($item['form'], ['circle']));
        }

    }
}
