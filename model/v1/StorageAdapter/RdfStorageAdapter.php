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

namespace oat\taoRestAPI\model\v1\StorageAdapter;


use common_Utils;
use core_kernel_classes_Resource;
use core_kernel_classes_ResourceFormatter;
use oat\taoRestAPI\exception\RestApiException;
use oat\taoRestAPI\model\DataStorageInterface;
use tao_models_classes_ClassService;

class RdfStorageAdapter implements DataStorageInterface
{

    /**
     * Service for current data
     * @var tao_models_classes_ClassService
     */
    protected $service;
    
    /**
     * @var core_kernel_classes_ResourceFormatter
     */
    private $formatter;

    /**
     * @var array
     */
    private $fields = [];
    
    public function __construct(tao_models_classes_ClassService $service)
    {
        $this->service = $service;
        $this->formatter = new core_kernel_classes_ResourceFormatter();
    }

    public function getFields()
    {
        if (!count($this->fields)) {
            $resources = $this->searchInstances(['limit' => 1]);
            if (count($resources)) {
                $resource = new \core_kernel_classes_Resource($resources[0]->uri);

                $types = $resource->getTypes();
                $properties = [];
                foreach ($types as $type){
                    /** @var \core_kernel_classes_Property $property */
                    foreach ($type->getProperties(true) as $property){
                        $properties[$property->getUri()] = $property;
                    }
                }

                $this->fields = array_keys(array_unique($properties));
            }
        }
        
        return $this->fields;
    }

    public function searchInstances(array $params = null)
    {
        $searchPropertyFilters = [];
        $result = [];

        // filters
        // fields with and, values by or
        if (isset($params['filters']) && count($params['filters'])) {
            $searchPropertyFilters = array_merge($searchPropertyFilters, $params['filters']);
        }
        
        $resources = $this->service->getRootClass()->searchInstances($searchPropertyFilters);
        foreach ($resources as $resource) {
            $result[] = $this->formatter->getResourceDescription($resource, false);
        }
        return $result;
    }
    
    public function save($key, array $resource)
    {
        // TODO: Implement save() method.
    }
    
    public function delete($key)
    {
        // TODO: Implement delete() method.
    }
    
    public function getOne($uri, array $partialFields)
    {
        if (!common_Utils::isUri($uri)) {
            throw new RestApiException('Undefined identifier', 400);
        }
        
        $resource = new core_kernel_classes_Resource($uri);
        if (!$resource->hasType($this->service->getRootClass())) {
            throw new RestApiException('Incorrect identifier type', 400);
        }

        $result =  $this->formatter->getResourceDescription($resource, false);

        foreach ($result->properties as $key => $property) {
            if (!in_array($property->predicateUri, $partialFields)) {
                unset($result->properties[$key]);
            }
        }
        
        return $result;
    }
    
}
