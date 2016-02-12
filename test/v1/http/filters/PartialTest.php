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

namespace oat\taoRestAPI\test\v1\http\Response;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\test\v1\Mocks\EnvironmentTrait;
use oat\taoRestAPI\test\v1\Mocks\TestHttpRoute;

class FieldsTest extends TaoPhpUnitTestRunner
{

    use EnvironmentTrait;

    public function testFields()
    {
        $this->request('GET', '/resources', '/resources?fields=title,type', function ($req, $res, $args) {
            (new TestHttpRoute($req, $res))->router();
            return $this->response = $res;
        });
        
        $this->assertEquals(['title', 'type'], array_keys($this->response->getResourceData()[0]));
        $this->assertEquals(2, count($this->response->getResourceData()[0]));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertEquals('OK', $this->response->getReasonPhrase());
        $this->assertEquals(['0-4/5'], $this->response->getHeader('Content-Range'));
        $this->assertEquals(['resource 50'], $this->response->getHeader('Accept-Range'));
    }
}
