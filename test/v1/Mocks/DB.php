<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\test\v1\Mocks;


/**
 * Class DB
 * 
 * Local static DB for testing RestApi
 * 
 * @package oat\taoRestAPI\test\v1\Mocks
 */
class DB
{
    /**
     * Data for testing and example of workflow
     * @var array
     */
    private $resourcesData = [
        [
            'id' => 1,
            'title' => 'Potato',
            'type' => 'vegetable',
            'form' => 'circle',
            'color' => 'brown',
        ],
        [
            'id' => 2,
            'title' => 'Lemon',
            'type' => 'citrus',
            'form' => 'ellipse',
            'color' => 'yellow',
        ],
        [
            'id' => 3,
            'title' => 'Lime',
            'type' => 'citrus',
            'form' => 'ellipse',
            'color' => 'green',
        ],
        [
            'id' => 4,
            'title' => 'Carrot',
            'type' => 'vegetable',
            'form' => 'conical',
            'color' => 'orange',
        ],
        [
            'id' => 5,
            'title' => 'Orange',
            'type' => 'citrus',
            'form' => 'circle',
            'color' => 'orange',
        ],
    ];

    public function getResources()
    {
        return $this->resourcesData;
    }
    
    public function deleteResource($key)
    {
        unset($this->resourcesData[$key]);
    }
    
    public function saveResource($key, array $resource)
    {
        $this->resourcesData[$key] = $resource;
    }

    /**
     * 
     * // use filter by values
     * 'filters' => $filter->getFilters(),
     *
     * // columns
     * 'fields' => $partial->getFields(),
     * 
     * // sort
     * 'sortBy' => $sort->getSorting(),
     * 
     * // pagination
     * 'offset' => $paginate->offset(),
     * 'limit' => $paginate->length(),
     *
     * 
     * @param array $params
     * @return array|mixed
     */
    public function searchInstances($params = [])
    {
        $data = $this->getResources();

        // filters
        // fields with and, values with or
        $filteredData = [];
        if (isset($params['filters']) && count($params['filters'])) {
            foreach ($data as $key => $row) {
                foreach ($params['filters'] as $field => $filters) {
                    if (in_array($row[$field], $filters)) {
                        $filteredData[$key] = $row;
                    } else {
                        if (isset($filteredData[$key])) {
                            unset($filteredData[$key]);
                        }
                        continue;
                    }
                }
            }
            $data = $filteredData;
        }

        // sort
        if (isset($params['sortBy']) && count($params['sortBy'])) {
            $sorting = [];

            if (!isset($params['sortBy']['desc'])) {
                $params['sortBy']['desc'] = [];
            }

            if (!isset($params['sortBy']['sort'])) {
                $params['sortBy']['sort'] = [];
            }
            
            foreach ($params['sortBy']['sort'] as $field) {
                $column = [];
                foreach ($data as $key => $row) {
                    $column[$key] = $row[$field];
                }

                $sorting[] = $column;

                if (in_array($field, $params['sortBy']['desc'])) {
                    $sorting[] = SORT_DESC;
                } else {
                    $sorting[] = SORT_ASC;
                }
            }

            $sorting[] = &$data;
            call_user_func_array('array_multisort', $sorting);
            $data = array_pop($sorting);
        }

        // pagination
        if (isset($params['offset']) && isset($params['limit'])) {
            $data = array_slice($data, $params['offset'], $params['limit']);
        }

        // fields
        if (isset($params['fields']) && count($params['fields'])) {

            foreach ($data as $k => $row) {
                foreach ($row as $key => $value) {
                    if (!in_array($key, $params['fields'])) {
                        unset($data[$k][$key]);
                    }
                }
            }
        }

        return $data;
    }
}