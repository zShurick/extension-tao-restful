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

namespace oat\taoRestAPI\proxy;


use common_Utils;
use core_kernel_classes_Resource;
use core_kernel_classes_ResourceFormatter;
use oat\taoRestAPI\exception\RestApiException;

abstract class TaoRdfStorage
{
    public function getResource($uri = '')
    {
        if (!(common_Utils::isUri($uri))) {
            throw new RestApiException('Invalid resource uri');
        }

        $resource = new core_kernel_classes_Resource($uri);

        if (!($resource->hasType($this->getRootClass()))) {
            throw new RestApiException('Invalid resource class type');
        }
        
        return $resource;
    }
    
    public function get($uri = '')
    {
        $resource = $this->getResource($uri);
        $formatter = new core_kernel_classes_ResourceFormatter();
        return $formatter->getResourceDescription($resource,false);
    }

    public function getAll($filters)
    {
        $formatter = new core_kernel_classes_ResourceFormatter();
        $resources = array();
        foreach ($this->getRootClass()->getInstances(true) as $resource) {
            $resources[] = $formatter->getResourceDescription($resource,false);
        }
        return $resources;
    }
}
