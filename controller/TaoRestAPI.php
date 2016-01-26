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


use tao_actions_CommonModule;
use oat\taoRestAPI\model\RestApi\v1\RestApiService;

/**
 * @author Open Assessment Technologies SA
 * @package taoRestAPI
 * @license GPL-2.0
 *
 */
class TaoRestAPI extends tao_actions_CommonModule {

    /**
     * @var RestApiService
     */
    private $restApiService;
    
    /**
     * initialize the services
     */
    public function __construct(){
        parent::__construct();
        
        $this->restApiService = new RestApiService();
    }

    /**
     * A possible entry point to tao
     */
    public function index() {
        echo __("Hello World");
    }

    public function templateExample() {
        $this->setData('author', 'Open Assessment Technologies SA');
        $this->setView('TaoRestAPI/templateExample.tpl');
    }
}