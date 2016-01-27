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

namespace oat\taoRestAPI\model\httpRequest;


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\model\HttpRouterInterface;

abstract class HttpRouter implements HttpRouterInterface
{

    private $url;
    private $key;

    public function __construct($uriMap = '')
    {
        if (empty($uriMap) || strpos($uriMap, '/') === false) {
            throw new HttpRequestException(__('Incorrect uri format for Restful'));
        }

        $pos = strrpos($uriMap, '/');
        $this->key = trim(substr($uriMap, $pos), '/');
        $this->url = substr($uriMap, 0, $pos);
    }

    public function router()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!method_exists($this, $method)) {
            throw new HttpRequestException(__('Unsupported HTTP request method'));
        }

        return $this->$method();
    }

    public function getKey()
    {
        return $this->key;
    }

    abstract protected function getList();
    
    abstract protected function getOne();
    
    public function get()
    {
        return empty($this->key)
            ? $this->getList()
            : $this->getOne();
    }

    public function post()
    {
        if (!empty($this->key)) {
            throw new HttpRequestException(__('You can\'t create new resource on object'));
        }
    }

    public function put()
    {
        if (empty($this->key)) {
            throw new HttpRequestException(__('You can\'t update list of the resources'));
        }
    }

    public function patch()
    {
        if (empty($this->key)) {
            throw new HttpRequestException(__('You can\'t update list of the resources'));
        }
    }

    public function delete()
    {
        if (empty($this->key)) {
            throw new HttpRequestException(__('You can\'t delete list of the resources'));
        }
    }

    public function options()
    {
        return empty($this->key)
            ? ['POST', 'GET', 'OPTIONS']
            : ['GET', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    }
}
