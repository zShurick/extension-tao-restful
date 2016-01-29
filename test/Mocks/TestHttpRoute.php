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


use oat\taoRestAPI\model\http\Request\Router;

class TestHttpRoute extends Router
{
    
    private function getRequestedRange()
    {
        $params = $this->req->getQueryParams();

        if (isset($params['range']) && preg_match("/^\d{1,4}-\d{1,4}$/", $params['range'])) {
            return explode('-', $params['range']);
        }
        
        return [];
    }
    
    private function getRequestedFilters()
    {
        $allowedFilters = ['filter1', 'filter2', 'filter3'];
        $params = $this->req->getQueryParams();
        $filters = [];
        foreach ($allowedFilters as $filter) {
            if (isset($params[$filter]) && !empty($params[$filter])) {
                $filters[$filter] = $params[$filter];
            }
        }
        return $filters;
    }
    
    protected function getList()
    {
        
        $range = $this->getRequestedRange();
        if(count($range) == 2) {
            if($range[0] > 0 && $range[1] < 48) {
                $this->res = $this->res->withStatus(200, 'Ok');
                //headers
                /**
                 * Content-Range: 0-47/48
                 * Accept-Range: items 50
                 */
            }
            
            if($range[0] > 0 && $range[0] < 50 && $range[1] > 50) {
                $this->res = $this->res->withStatus(206, 'Partial Content');
                //headers
                /**
                 * Content-Range: 0-47/48
                 * Accept-Range: items 50
                 */
            }
        }
        
        $filters = $this->getRequestedFilters();
        
        $this->res->setResourceData('list of the resources');
    }
    
    protected function getOne()
    {
        $res = 'one resource ' . $this->req->getAttribute('id');
        // if params
        $params = $this->req->getQueryParams();
        $res .= (isset($params['fields']) ? ' ' . $params['fields'] : '');
        
        $this->res->setResourceData($res);
    }
    
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
}
