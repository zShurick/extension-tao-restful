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
 * 
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model\restApi;


use oat\taoRestAPI\exception\RestApiException;
use oat\taoRestAPI\model\httpRequest\HttpDataFormat;
use oat\taoRestAPI\model\RestApiInterface;
use oat\taoRestAPI\model\stack\RestApiStack;
use oat\taoRestAPI\test\Mocks\TestRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Bring together all middlewares and modules for getting restApi service
 *
 * Class RestApiServiceV1
 * @package oat\taoRestAPI\model\restApi
 */
class RestApiServiceV1 implements RestApiInterface
{

    /**
     * @var RestApiStack
     */
    private $stack;

    public function __construct()
    {
        $this->stack = new RestApiStack();
    }

    public function resources(ServerRequestInterface $req, ResponseInterface $res)
    {
        try {
            $this->stack
                ->add(function ($req, $res, $next) {
                    $route = new TestRoute($req, $res);
                    // set response answer status and set resourceData
                    $route->router();
                    return $res;
                })
                ->add(function ($req, $res, $next) {
                    $res = $next($req, $res);
                    $format = new HttpDataFormat();
                    $res->write( $format->encoder($res->getResourceData()) );
                    return $res;
                })
                ->callMiddlewareStack($req, $res);
        } catch (RestApiException $e) {
            // answer with error
            var_dump($e);
        }
    }

}