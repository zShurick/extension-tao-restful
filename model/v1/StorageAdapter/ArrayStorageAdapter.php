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


use oat\taoRestAPI\model\DataStorageInterface;

class ArrayStorageAdapter implements DataStorageInterface
{
    protected $resourcesData = [];
    
    public function getFields()
    {
        return array_keys($this->searchInstances()[0]);
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
    public function searchInstances(array $params = null)
    {
        $data = $this->resourcesData;

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

    public function delete($key)
    {
        if (isset($this->resourcesData[$key])) {
            unset($this->resourcesData[$key]);
        }
    }

    public function save($key, array $resource)
    {
        $this->resourcesData[$key] = $resource;
    }
}
