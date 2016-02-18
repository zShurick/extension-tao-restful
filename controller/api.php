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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *               
 * @author A. Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\controller;


use oat\taoRestAPI\exception\RestApiException;
use oat\taoRestAPI\model\example\v1\HttpRoute;
use oat\taoRestAPI\model\v1\http\Request\DataFormat;
use oat\taoRestAPI\proxy\BasicAuthentication;
use oat\taoRestAPI\service\v1\RestApiService;
use tao_actions_CommonModule;

/**
 * @author Open Assessment Technologies SA
 * @package taoRestAPI v1
 * @license GPL-2.0
 *
 */
class api extends tao_actions_CommonModule {

    /**
     * @var RestApiService
     */
    protected $service;
    
    /**
     * initialize the services
     */
    public function __construct(){
        parent::__construct();
        $this->service = new RestApiService();
    }

    public function documentation()
    {
        echo "doc";
    }
    
    /**
     * A possible entry point to tao
     */
    public function v1() {
        
        try {
            $this->service
                ->setEncoder(new DataFormat())
                ->setRouter(new HttpRoute())
                ->setAuth(new BasicAuthentication())
                ->execute(function ($router, $encoder) {
                    
                    $router($this->getRequest(), $this->getResponse());
                    
                    $this->service->writeResponse(
                        $router->getStatusCode(), 
                        $encoder->getContentType(), 
                        $router->getHeaders(), 
                        $encoder->encode($router->getBodyData())
                    );
                });
        } catch (RestApiException $e) {
            $this->service->writeResponse($e->getCode(), 'text/plain', [], $e->getMessage());
        }
    }
}
