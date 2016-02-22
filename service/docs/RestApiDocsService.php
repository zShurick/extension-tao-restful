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
use oat\taoRestAPI\service\RestApiDocsInterface;

class RestApiDocsService extends ConfigurableService implements RestApiDocsInterface
{
    /**
     * @var RestApiDocsProxy
     */
    private $proxy = null;
    
    public function __construct(array $options)
    {
        parent::__construct($options);
        
        $proxyClass = 'oat\taoRestAPI\service\docs\proxy\\';
        $proxyClass .= $this->hasOption('proxy') ? $this->getOption('proxy') : 'Swagger';
        
        $this->proxy = new $proxyClass;
    }
    
    public function generate()
    {
        if (count($this->getOptions()) 
            && isset($this->getOptions()['routes'])
            && count($this->getOptions()['routes'])
        ) {
            $this->proxy->generate($this->getOptions()['routes']);
        } else {
            throw new RestApiDocsException(__('Incorrect routes data for Restful documentations'));
        }
    }
    
    public function getApiDocs($section = '')
    {
        return $this->proxy->getApiDocs( $section = '' );
    }
    
}
