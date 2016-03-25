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
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use core_kernel_classes_ResourceFormatter;
use oat\taoRestAPI\exception\RestApiException;
use tao_models_classes_ClassService;

class RdfStorageAdapter extends AbstractStorageAdapter
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
                $resource = new core_kernel_classes_Resource($resources[0]->uri);

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
        $searchOptions = ['like' => false];
        
        $result = [];

        // filters
        // fields with and, values by or
        if (isset($params['filters']) && count($params['filters'])) {
            $searchPropertyFilters = array_merge($searchPropertyFilters, $params['filters']);
        }

        // sort in rdf can be only by 1 field
        if (isset($params['sortBy']) && count($params['sortBy'])) {
            $sortBy = '';
            $sortDirection = 'ASC';
            
            if (isset($params['sortBy']['sort']) && count($params['sortBy']['sort'])) {
                foreach ($params['sortBy']['sort'] as $field) {
                    if (in_array($field, $this->getFields())) {
                        $sortBy = $field;
                        break;
                    }
                }
            }
            
            if (!empty($sortBy) && isset($params['sortBy']['desc']) && in_array($sortBy, $params['sortBy']['desc'])) {
                $sortDirection = 'DESC';
            }
            
            $searchOptions = array_merge($searchOptions, ['order' => $sortBy, 'orderdir' => $sortDirection]);
        }

        // pagination
        if (isset($params['offset']) && isset($params['limit'])) {
            $searchOptions = array_merge($searchOptions, ['offset' => $params['offset'], 'limit' => $params['limit']]);
        }

        $resources = $this->service->getRootClass()->searchInstances($searchPropertyFilters, $searchOptions);

        foreach ($resources as $resource) {
            $row = $this->formatter->getResourceDescription($resource, false);
            if (isset($params['fields']) && count($params['fields'])) {
                $row = $this->getPartial($row, $params['fields']);
            }
            $result[] = $row;
        }
        return $result;
    }

    public function create(array $propertiesValues)
    {
        if (!isset($propertiesValues[RDFS_LABEL])) {
            $propertiesValues[RDFS_LABEL] = "";
        }

        $type = isset($propertiesValues[RDF_TYPE]) ? $propertiesValues[RDF_TYPE] : null;
        $label = $propertiesValues[RDFS_LABEL];
        unset($propertiesValues[RDFS_LABEL]);
        unset($propertiesValues[RDF_TYPE]);

        $type = (isset($type)) ? new core_kernel_classes_Class($type) : $this->service->getRootClass();

        if ($type->getUri() != $this->service->getRootClass()->getUri()) {
            throw new RestApiException(__('Incorrect type of the resource'), 400);
        }

        $resource = $this->service->createInstance($type, $label);
        $resource->setPropertiesValues($propertiesValues);

        return $resource->getUri();
    }

    public function put($uri, array $propertiesValues)
    {
        parent::put($uri, $propertiesValues);
        
        $resource = new core_kernel_classes_Resource($uri);
        // delete all properties of the resource
        $this->delete($uri);
        
        // add new properties from propertiesValues
        $resource->setPropertiesValues($propertiesValues);
    }

    public function patch($uri, array $propertiesValues)
    {
        parent::patch($uri, $propertiesValues);
        
        $resource = new core_kernel_classes_Resource($uri);

        foreach ($propertiesValues as $property => $value) {
            $resource->editPropertyValues(new \core_kernel_classes_Property($property), $value);
        }
    }
    
    public function delete($uri)
    {
        if ($this->exists($uri)) {
        
            if (!common_Utils::isUri($uri)) {
                throw new RestApiException(__('Undefined identifier'), 400);
            }
    
            $resource = new core_kernel_classes_Resource($uri);
            
            if (!$resource->hasType($this->service->getRootClass())) {
                throw new RestApiException(__('Incorrect identifier type'), 400);
            }
            
            $resource->delete();
        }
        
    }

    public function getOne($uri, array $partialFields)
    {
        $resource = $this->getResource($uri);
        $result =  $this->formatter->getResourceDescription($resource, false);

        if (count($partialFields)){
            $result = $this->getPartial($result, $partialFields);
        }
        
        return $result;
    }

    private function getPartial($result, array $partialFields)
    {
        foreach ($result->properties as $key => $property) {
            if (!in_array($property->predicateUri, $partialFields)) {
                unset($result->properties[$key]);
            }
        }
        
        return $result;
    }

    /**
     * @param $uri
     * @return core_kernel_classes_Resource
     * @throws RestApiException
     */
    protected function getResource($uri)
    {
        if (!common_Utils::isUri($uri)) {
            throw new RestApiException(__('Undefined identifier'), 404);
        }

        $resource = new core_kernel_classes_Resource($uri);
        if (!$resource->hasType($this->service->getRootClass())) {
            throw new RestApiException(__('Incorrect identifier type'), 400);
        }
        return $resource;
    }
    
    public function exists($uri)
    {
        return $this->getResource($uri)->exists();
    }
}
