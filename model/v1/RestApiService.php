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
 */

namespace oat\taoRestAPI\model\v1;


use oat\taoRestAPI\model\RestApiInterface;

/**
 * Class RestApiService
 * @package oat\taoRestAPI\model\v1
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */
class RestApiService implements RestApiInterface
{
    public function version()
    {
        return '1';
    }

    public function get()
    {
        // TODO: Implement get() method.
    }

    public function post()
    {
        // TODO: Implement post() method.
    }

    public function put()
    {
        // TODO: Implement put() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function patch()
    {
        // TODO: Implement patch() method.
    }

    public function options()
    {
        // TODO: Implement options() method.
    }
}