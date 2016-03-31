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


use oat\taoRestAPI\service\DocsProxyInterface;
use ReflectionClass;

abstract class DocsProxy implements DocsProxyInterface
{

    public function generate( array $routes = [] )
    {
        $docs = [];
        foreach ($routes as $name => $class) {
            $docs[$name] = $this->generateDocs( $class );
        }
        
        return $docs;
    }
    
    /**
     * @param $class
     * @return string
     */
    protected function getPath($class)
    {
        $reflectionClass = new ReflectionClass($class);
        $path = $reflectionClass->getFileName();
        return $path;
    }
    
    /**
     * Collect all documentation from class collection
     * 
     * @param $class
     * @return array|mixed
     */
    protected function goDipper($class)
    {
        $docs = [];
        $reflectionClass = new \ReflectionClass($class);
        while ($parent = $reflectionClass->getParentClass()) {

            $docs[] = $this->generateDocs($parent->getName());
            $reflectionClass = $parent;
        }
        
        return $docs;
    }
}
