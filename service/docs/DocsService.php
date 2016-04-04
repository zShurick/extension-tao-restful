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

namespace oat\taoRestAPI\service\docs;


use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ConfigurableService;
use oat\taoRestAPI\exception\RestApiDocsException;
use oat\taoRestAPI\service\DocsInterface;
use Swagger\Annotations\Swagger;

/**
 * Class DocsService
 * 
 * Generate documentation for Rest API from all extensions
 * @package oat\taoRestAPI\service\docs
 */
class DocsService extends ConfigurableService implements DocsInterface
{
    const OPTION_PROXY = 'proxy';
    const OPTION_ROUTERS = 'routes';
    const OPTION_STORAGE = 'docs';
    
    /**
     * @var DocsProxy
     */
    private $proxy = null;
    
    public function __construct(array $options)
    {
        parent::__construct($options);
        
        $proxyClass = 'oat\taoRestAPI\proxy\\';
        $proxyClass .= $this->hasOption('proxy') ? $this->getOption(self::OPTION_PROXY) : 'Swagger';
        
        $this->proxy = new $proxyClass;
        
        if (!($this->proxy instanceof DocsProxy)) {
            throw new RestApiDocsException(__('Incorrect proxy for Restful documentations'));
        }
    }

    /**
     * @param string $section
     * @return array by sections
     * @throws RestApiDocsException
     */
    public function generateDocs($section = '')
    {
        if (!$this->hasOption(self::OPTION_ROUTERS) || !count($this->getOption(self::OPTION_ROUTERS))) {
            throw new RestApiDocsException(__('Incorrect routes data for Restful documentations'));
        }
            
        $data = [];
        if (empty($section)) {
            $data = $this->proxy->generate($this->getOption(self::OPTION_ROUTERS));
        } else {
            if (!isset($this->getOption(self::OPTION_ROUTERS)[$section])) {
                throw new RestApiDocsException(__('Incorrect section of the routes for Restful documentations (%s)', $section));
            } else {
                $data[$section] = $this->proxy->generateDocs( $this->getOption(self::OPTION_ROUTERS)[$section] );
            }
        }
    
        return $data;
    }

    /**
     * @param string $section
     * @throws RestApiDocsException
     * @return string Json
     */
    public function jsonDocs($section = '')
    {
        $docs = $this->generateDocs($section);

        return json_encode($this->compiler($docs));
    }

    /**
     * Generate one statistics from many
     * 
     * @param $docs [swagger, parent => [...]]
     * @param array|null $arrDocs
     * @return array
     */
    private function compiler($docs, array $arrDocs = null)
    {
        // get section
        while (count($docs)) {
            $section = array_pop($docs);
            $arrDocs = $this->compileTree($section);
        }
        
        return $arrDocs;
    }
    
    private function compileTree(array $section)
    {
        $docs = [];
        $row = array_pop($section);
        if ( ($row['swagger'] instanceof Swagger) ) {
            $row['swagger'];
        }
        
        if ($row['parent']) {
            $this->compileTree($row['parent']);
        }
        
        return $docs;
    }
}
