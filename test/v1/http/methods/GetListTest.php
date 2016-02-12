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

class GetListTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;
    
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
            return $this->routerRunner($req, $res, $args);
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
            return $this->routerRunner($req, $res, $args);
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
}
