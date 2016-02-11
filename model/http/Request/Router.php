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

namespace oat\taoRestAPI\model\http\Request;


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\model\http\Response;
use oat\taoRestAPI\model\HttpRouterInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Router implements HttpRouterInterface
{

    /**
     * @var ServerRequestInterface
     */
    protected $req;

    /**
     * @var Response
     */
    protected $res;

    /**
     * @var mixed
     */
    private $resourceId;
    
    public function __construct(ServerRequestInterface $req, Response &$res)
    {
        $this->req = $req;
        $this->res = &$res;
        $this->setResourceId();
    }
    
    private function setResourceId()
    {
        $this->resourceId =  $this->req->getAttribute('id');
    }
    
    protected function getResourceId() 
    {
        return $this->resourceId;
    }

    public function router()
    {
        $method = strtolower($this->req->getMethod());

        if (!method_exists($this, $method)) {
            throw new HttpRequestException(__('Unsupported HTTP request method'), 400);
        }
        
        try {
            $this->$method();
        } catch(HttpRequestException $e) {
            
            // define $e->getMessage() in json format like error
            $this->res = $this->res->withJson(['errors' => [$e->getMessage()]]);
            $this->res = $this->res->withStatus($e->getCode());
        }
    }

    abstract protected function getList();
    
    abstract protected function getOne();
    
    public function get()
    {
        empty($this->resourceId) 
            ? $this->getList()
            : $this->getOne();
    }

    public function post()
    {
        if (!empty($this->resourceId)) {
            throw new HttpRequestException(__('You can\'t create new resource on object'), 400);
        }
        
        $this->res = $this->res->withStatus(201);
    }

    public function put()
    {
        if (empty($this->resourceId)) {
            throw new HttpRequestException(__('You can\'t update list of the resources'), 400);
        }
    }

    public function patch()
    {
        if (empty($this->resourceId)) {
            throw new HttpRequestException(__('You can\'t update list of the resources'), 400);
        }
    }

    public function delete()
    {
        if (empty($this->resourceId)) {
            throw new HttpRequestException(__('You can\'t delete list of the resources'), 400);
        }
    }

    public function options()
    {
        $allowed =  empty($this->resourceId)
            ? ['POST', 'GET', 'OPTIONS']
            : ['GET', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        
        $this->res = $this->res->withJson($allowed);
    }
}
