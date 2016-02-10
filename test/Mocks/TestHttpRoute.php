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
 */

namespace oat\taoRestAPI\test\Mocks;


use oat\taoRestAPI\model\http\filters\Filter;
use oat\taoRestAPI\model\http\filters\Paginate;
use oat\taoRestAPI\model\http\filters\Partial;
use oat\taoRestAPI\model\http\filters\Sort;
use oat\taoRestAPI\model\http\Request\Router;

class TestHttpRoute extends Router
{

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

    public function post()
    {
        parent::post();
        return 'resource created';
    }

    public function put()
    {
        parent::put();
        return 'resource updated';
    }

    public function patch()
    {
        parent::patch();
        return 'resource updated partially';
    }

    public function delete()
    {
        parent::delete();
        return true;
    }

    protected function getList()
    {
        $queryParams = $this->req->getQueryParams();

        $partial = new Partial($this->res, [
            'query' => isset($queryParams['fields']) ? $queryParams['fields'] : '',
            'fields' => array_keys($this->resourcesData[0]),
        ]);

        $paginate = new Paginate($this->res, [
            'query' => isset($queryParams['range']) ? $queryParams['range'] : '',
            'total' => count($this->resourcesData),
            'paginationUrl' => 'http://api.taotest.example/v1/items?range=',
        ]);

        $sort = new Sort($this->res, ['query' => $queryParams]);

        $filter = new Filter($this->res, [
            'query' => $queryParams,
            'fields' => array_keys($this->resourcesData[0]),
        ]);

        $data = $this->searchInstances([

            'fields' => $partial->getFields(),

            // sort
            'sortBy' => $sort->getSorting(),

            // pagination
            'offset' => $paginate->offset(),
            'limit' => $paginate->length(),

            // use filter
            'filters' => $filter->getFilters(),
        ]);

        $paginate->correctPaginationHeader(count($data));
        
        $this->res->setResourceData($data);
    }

    private function searchInstances($params = [])
    {
        $data = $this->resourcesData;

        // filters
        // fields with and, values with or
        $filteredData = [];
        if (count($params['filters'])) {
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
        if (count($params['sortBy'])) {
            $sorting = [];
            
            if (!isset($params['sortBy']['desc'])) {
                $params['sortBy']['desc'] = [];
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
        $data = array_slice($data, $params['offset'], $params['limit']);

        // fields
        if (count($params['fields'])) {

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

    protected function getOne()
    {
        $res = 'one resource ' . $this->req->getAttribute('id');
        // if params
        $params = $this->req->getQueryParams();
        $res .= (isset($params['fields']) ? ' ' . $params['fields'] : '');

        $this->res->setResourceData($res);
    }
}
