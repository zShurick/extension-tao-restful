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

class SortTest extends TaoPhpUnitTestRunner
{

    use EnvironmentTrait;

    public function testSortByOneField()
    {
        $this->request('GET', '/resources', '/resources?sort=title', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });
        
        $this->assertEquals(5, count($this->response->getResourceData()));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());

        $titles = [];
        foreach ($this->response->getResourceData() as $item) {
            $titles[] = $item['title'];
        }
         
        $this->assertEquals(['Carrot', 'Lemon', 'Lime', 'Orange', 'Potato'], $titles);
    }

    public function testSortByManyFields()
    {
        $this->request('GET', '/resources', '/resources?sort=form,type,id', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(5, count($this->response->getResourceData()));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());

        $form = $type = $titles = [];
        foreach ($this->response->getResourceData() as $item) {
            $titles[] = $item['title'];
            $form[] = $item['form'];
            $type[] = $item['type'];
        }

        $this->assertEquals(['Orange', 'Potato', 'Carrot', 'Lemon', 'Lime'], $titles);
        $this->assertEquals(['circle', 'circle', 'conical', 'ellipse', 'ellipse'], $form);
        $this->assertEquals(['citrus', 'vegetable', 'vegetable', 'citrus', 'citrus'], $type);
    }
    
    public function testSortDesc()
    {
        $this->request('GET', '/resources', '/resources?sort=title&desc=title', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });

        $this->assertEquals(5, count($this->response->getResourceData()));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());

        $titles = [];
        foreach ($this->response->getResourceData() as $item) {
            $titles[] = $item['title'];
        }

        $this->assertEquals(['Potato', 'Orange', 'Lime', 'Lemon', 'Carrot'], $titles);   
    }
}
