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

namespace oat\taoRestAPI\model;


interface HttpRouterInterface
{
    
    /**
     * Starts the method defined in the Http request
     * 
     * @return mixed method result
     */
    public function router();

    /**
     * Http GET
     * @return mixed (list of resources or resource by id)
     */
    public function get();

    /**
     * Http POST
     * @return mixed new resource
     */
    public function post();

    /**
     * Http PUT
     * @return mixed updated resource
     */
    public function put();

    /**
     * Http PATCH
     * @return mixed updated resource
     */
    public function patch();

    /**
     * Http DELETE
     * @return bool
     */
    public function delete();

    /**
     * List of the allowed options
     * (for resource can be described structure)
     * @return mixed
     */
    public function options();
}