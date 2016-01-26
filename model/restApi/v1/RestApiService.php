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

namespace oat\taoRestAPI\model\RestApi\v1;


use oat\taoRestAPI\model\RestApiInterface;
use Slim\CallableResolverAwareTrait;
use Slim\MiddlewareAwareTrait;

/**
 * Class RestApiService
 * @package oat\taoRestAPI\model\v1
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */
class RestApiService implements RestApiInterface
{

    use MiddlewareAwareTrait;
    use CallableResolverAwareTrait;

    public function __construct($config = [])
    {

    }

    /**
     * To list if the items - getAllResources()
     * CURL –X GET \
     * -H "Accept: application/json" \
     * https://api.tao.com/v1/item
     *
     * OR
     * To get one item data getResource()
     *
     * CURL –X GET \
     * -H "Accept: application/json" \
     * https://api.tao.com/v1/item/1?fields=id,name
     *
     *
     * @param null $uri
     * @param null $params
     * @return array|string
     */
    public function get($uri = null, $params = null)
    {
        if ($uri) {
            return $this->getResource($uri, $params);
        } else {
            return $this->getAllResources($params);
        }
    }

    /**
     * CURL –X POST \
     * -H "Accept: application/json" \
     * -d '{"state":"running"}' \
     * https://api.tao.com/v1/item/1
     */
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