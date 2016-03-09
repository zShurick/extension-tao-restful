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

namespace oat\taoRestAPI\test\docs;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\service\docs\DocsService;

/**
 * Class RestApiDocsServiceTest
 * @package oat\taoRestAPI\test\docs
 */
class DocsServiceTest extends TaoPhpUnitTestRunner
{

    /**
     * @var DocsService
     */
    private $service = null;
    
    public function setUp()
    {
        parent::setUp();
        $this->service = new DocsService(['proxy' => 'Swagger', 'routes' => ['Example' => '\oat\taoRestAPI\model\example\v1\HttpRoute']]);
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\RestApiDocsException
     */
    public function testInvalidConfigException()
    {
        $service = new DocsService([]);
        $service->getApiDocs();
    }

    /**
     * catch trigger_error from Swagger
     * @expectedException \ReflectionException
     */
    public function testWithoutSwaggerPhpDoc()
    {
        $service = new DocsService(['proxy' => 'Swagger', 'routes' => ['Example' => '\oat\taoRestAPI\test\docs\RestApiDocsServiceTest']]);
        $service->getApiDocs();
    }
    
    public function testGetDocs()
    {
        $data = $this->service->getApiDocs();
        $this->assertTrue(isset($data['Example']));
        $this->assertEquals('2.0', $data['Example']->swagger);
    }
    
    public function testSection()
    {
        $data = $this->service->getApiDocs('Example');
        $this->assertTrue(isset($data['Example']));
        $this->assertEquals('2.0', $data['Example']->swagger);
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\RestApiDocsException
     */
    public function testIncorrectSection()
    {
        $this->service->getApiDocs('FailureService');
    }
}
