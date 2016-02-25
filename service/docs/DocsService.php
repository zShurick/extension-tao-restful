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


use oat\oatbox\service\ConfigurableService;
use oat\taoRestAPI\exception\RestApiDocsException;
use oat\taoRestAPI\service\DocsInterface;

class DocsService extends ConfigurableService implements DocsInterface
{
    /**
     * @var DocsProxy
     */
    private $proxy = null;
    
    public function __construct(array $options)
    {
        parent::__construct($options);
        
        $proxyClass = 'oat\taoRestAPI\proxy\\';
        $proxyClass .= $this->hasOption('proxy') ? $this->getOption('proxy') : 'Swagger';
        
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
    public function getApiDocs($section = '')
    {
        if (count($this->getOptions())
            && isset($this->getOptions()['routes'])
            && count($this->getOptions()['routes'])
        ) {
            
            $data = [];
            if(!empty($section)) {
                
                if (!isset($this->getOptions()['routes'][$section])) {
                    throw new RestApiDocsException(__('Incorrect section of the routes for Restful documentations (%s)', $section));
                } else {
                    $data[$section] = $this->proxy->getApiDocs( $this->getOptions()['routes'][$section] );
                }
            } else {
                $data = $this->proxy->generate($this->getOptions()['routes']);
            }
            return $data;
        } else {
            throw new RestApiDocsException(__('Incorrect routes data for Restful documentations'));
        }
    }
    
}
