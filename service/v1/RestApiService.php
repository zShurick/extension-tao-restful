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
use oat\taoRestAPI\model\RestApiInterface;
use oat\taoRestAPI\model\v1\http\Request\DataFormat;
use oat\taoRestAPI\model\v1\http\Response;
use oat\taoRestAPI\test\v1\Mocks\TestHttpRoute;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RestApiServiceV1
 * @package oat\taoRestAPI\model\restApi
 */
class RestApiService implements RestApiInterface
{

    public function execute(ServerRequestInterface $req, Response $res)
    {
        try {

            // auth
            $encoder = DataFormat::encoder();

            (new TestHttpRoute($req, $res))->router();

            if (count($res->getResourceData())) {
                $encodedData = $encoder->encode($res->getResourceData());
                $res = $res->withBody($encodedData);
            }
            
        } catch (RestApiException $e) {
            // answer with error
            $res = $res
                ->withJson(['errors' => [$e->getMessage()]])
                ->withStatus($e->getCode());
        }

        return $res;
    }
}
