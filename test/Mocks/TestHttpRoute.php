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


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\model\http\Request\Router;
use oat\taoRestAPI\model\http\Response\Paginate;

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
        $range = $this->getRequestedRange();
        
        // in paginate should be correct offset, limit for searchInstances
        $paginate = new Paginate($this->res, [
            'offset' => $range[0], 
            'limit' => $range[1], 
            'total' => count($this->resourcesData),
            'paginationUrl' => 'http://api.taotest.example/v1/items?range=',
        ]);
        
        $data = $this->searchInstances([
            'offset' => $paginate->offset(),
            'limit' => $paginate->length(),
            'fields' => $this->getRequestedFields() 
        ]);

        $this->res->setResourceData($data);
    }
    
    private function getRequestedRange()
    {
        $params = $this->req->getQueryParams();
        
        if (isset($params['range'])) {

            if (!preg_match("/^\d{1,4}-\d{1,4}$/", $params['range'])) {
                throw new HttpRequestException('Incorrect range parameter. Try to use: ?range=0-25', 400);
            } else {
                return explode('-', $params['range']);
            }
        }

        return [0, 0];
    }
    
    private function getRequestedFields()
    {
        $columns = [];
        $params = $this->req->getQueryParams();
        if (isset($params['fields'])) {
            foreach (explode(',', $params['fields']) as $field) {
                if (isset($this->resourcesData[0][$field])) {
                    $columns[] = $field;
                }
            }
        }
        return $columns;
    }

    private function searchInstances($params = [])
    {
        
        // pagination
        $data = array_slice($this->resourcesData, $params['offset'], $params['limit']);

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
