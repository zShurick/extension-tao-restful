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

namespace oat\taoRestAPI\model\Authentication;


use oat\taoRestAPI\model\AuthenticationInterface;

class BasicAuthentication implements AuthenticationInterface
{
    private $app;
    
    private $container;

    public function __construct($app, array $options = array())
    {
        $this->app = $app;
        $this->container = $this->setupContainer($options);
    }
    
    public function authenticate()
    {
        $request->headers->get('PHP_AUTH_USER', false);
        $request->headers->get('PHP_AUTH_PW');
    }
}