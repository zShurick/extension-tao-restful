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
        $this->service = new DocsService(['proxy' => 'Swagger', 'routes' => [
            'Example' => '\oat\taoRestAPI\test\v1\Mocks\DB',
            'AnotherExample' => '\oat\taoRestAPI\test\v1\Mocks\AnotherDocs'
        ]]);
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\RestApiDocsException
     */
    public function testInvalidConfigException()
    {
        $service = new DocsService([]);
        $service->generateDocs();
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\RestApiDocsException
     */
    public function testWithoutSwaggerPhpDoc()
    {
        $service = new DocsService(['proxy' => 'Swagger', 'routes' => ['Example' => '\oat\taoRestAPI\test\docs\DocsServiceTest']]);
        $service->generateDocs();
    }

    /**
     * @expectedException \oat\taoRestAPI\exception\RestApiDocsException
     */
    public function testIncorrectSection()
    {
        $this->service->generateDocs('FailureService');
    }

    
    public function testGenerateDocs()
    {
        $data = $this->service->generateDocs();

        $this->assertTrue(isset($data['Example']));
        $this->assertEquals('2.0', $data['Example'][$this->service->getOption(DocsService::OPTION_ROUTERS)['Example']]['swagger']->swagger);

        $this->assertTrue(isset($data['AnotherExample']));
        $this->assertEquals('2.0', $data['AnotherExample'][$this->service->getOption(DocsService::OPTION_ROUTERS)['AnotherExample']]['parent']['oat\taoRestAPI\test\v1\Mocks\DB']['swagger']->swagger);
    }

    public function testGenerateSectionDocs()
    {
        $data = $this->service->generateDocs('Example');
        
        $this->assertTrue(isset($data['Example']));
        $this->assertEquals('2.0', $data['Example'][$this->service->getOption(DocsService::OPTION_ROUTERS)['Example']]['swagger']->swagger);
    }
    
    public function testJsonDocs()
    {
        $data = $this->service->jsonDocs();
    }
}
