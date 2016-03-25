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

namespace oat\taoRestAPI\model\example\v1;


use oat\taoRestAPI\model\v1\http\Request\RouterAdapter\AbstractRouterAdapter;
use Request;

if (!defined('API_HOST')) {
    define('API_HOST', trim(preg_replace('|^.*://(.*)$|', "\\1", ROOT_URL), '/'));
}

/**
 * Class HttpRoute
 * @package oat\taoRestAPI\model\example\v1
 *
 * #######
 * ===
 *
 * @SWG\Swagger(
 *   swagger="2.0",
 *   schemes={"http","https"},
 *   host=API_HOST,
 *   basePath="/taoRestAPI/v1/",
 *   consumes={"application/json","application/xml"},
 *   produces={"application/json","application/xml"},
 *   @SWG\Info(
 *     title="TAO Example RestAPI",
 *     version="1.0.0",
 *     description="RestAPI control for TAO.
 *      [Learn about TAO](http://www.taotesting.com/).
 *      For this sample, you can use the api key `oAuth-token-for-test`",
 *     termsOfService="http://www.taotesting.com/resources/faq/",
 *     @SWG\Contact(
 *       name="Open Assessment Technologies S.A.",
 *       email="contact@taotesting.com",
 *       url="http://www.taotesting.com/contact/"
 *     ),
 *     @SWG\License(
 *       name="GNU General Public License",
 *       url="http://www.gnu.org/licenses/gpl.html"
 *     ),
 *   )
 * )
 *
 * ### Models
 * ---
 * @SWG\Definition(
 *     required={"title", "type"},
 *     @SWG\Xml(name="Resource"),
 *     @SWG\Property(title="id",format="int64"),
 *     definition="Resource for test"
 * )
 * ===
 * ###
 *
 *
 * ### Tags
 * ---
 * @SWG\Tag(
 *   name="Resources for example",
 *   description="TaoRestAPI examples"
 * )
 * ===
 * ###
 *
 *
 * ### List of the resources
 * ---
 *
 * @SWG\Get(
 *   path="resources",
 *   summary="All resources",
 *   description="Get list of the resources",
 *   tags={"Resources for example"},
 *   operationId="getList",
 *   consumes={"application/xml", "application/json"},
 *   produces={"application/xml", "application/json"},
 *   @SWG\Response(
 *     response=200,
 *     description="OK",
 *     @SWG\Schema(ref="#/definitions/Resource")
 *   ),
 *
 *   @SWG\Parameter(
 *     name="range",
 *     in="query",
 *     type="string",
 *     description="Paginate by the resources {0-1}",
 *     required=false,
 *     collectionFormat="multi"
 *   ),
 *   @SWG\Parameter(
 *     name="title",
 *     in="query",
 *     description="Filter by title (Potato,Orange)",
 *     required=false,
 *     type="string",
 *     @SWG\Items(type="array"),
 *     collectionFormat="multi"
 *   ),
 *   @SWG\Parameter(
 *     name="type",
 *     in="query",
 *     description="Filter by type (vegetable)",
 *     required=false,
 *     type="string",
 *     @SWG\Items(type="array"),
 *     collectionFormat="multi"
 *   ),
 *   @SWG\Parameter(
 *     name="fields",
 *     in="query",
 *     description="Return only specified fields (title,type)",
 *     required=false,
 *     type="string",
 *     @SWG\Items(type="array"),
 *     collectionFormat="multi"
 *   ),
 *   @SWG\Parameter(
 *     name="sort",
 *     in="query",
 *     description="Sorting by fields ASC (title,type)",
 *     required=false,
 *     type="string",
 *     @SWG\Items(type="array"),
 *     collectionFormat="multi"
 *   ),
 *   @SWG\Parameter(
 *     name="desc",
 *     in="query",
 *     description="Using with ?sort=field, and set DESC direction for field (title,type) ",
 *     required=false,
 *     type="string",
 *     @SWG\Items(type="array"),
 *     collectionFormat="multi"
 *   ),
 *
 *   @SWG\Response(
 *     response=206,
 *     description="Partial Content",
 *     @SWG\Schema(ref="#/definitions/Resource")
 *   ),
 *   @SWG\Response(
 *     response=400,
 *     description="Incorrect range parameter. Try to use: ?range=0-25"
 *   ),
 *   @SWG\Response(
 *     response=406,
 *     description="Not acceptable encoding format"
 *   ),
 *
 * )
 *
 * ====
 * ###
 *
 * ### Get one resource
 *
 * @SWG\Get(
 *   path="resources/{id}",
 *   summary="Find resource by ID",
 *   tags={"Resources for example"},
 *   operationId="getItem",
 *   @SWG\Parameter(
 *      name="id",
 *      in="query",
 *      type="string",
 *      description="Unique Id of the resource",
 *      required=true
 *   ),
 *   @SWG\Response(
 *     response=200,
 *     description="A list with resources|Resource was found"
 *   ),
 *   @SWG\Response(
 *     response="default",
 *     description="an unexpected error"
 *   )
 * )
 *
 * ===
 * #######
 */
class HttpRoute extends AbstractRouterAdapter
{
    /**
     * @var Request
     */
    protected $req;

    public function __invoke(Request $request)
    {
        $this->req = $request;
        $this->runApiCommand($this->req->getMethod(), $this->req->getParameter('uri'));
    }

    protected function getList(array $params = null)
    {
        $queryParams = $this->req->getParameters();
        parent::getList($queryParams);
    }

    protected function getOne()
    {
        echo 'one';
    }
    
    protected function getParsedBody()
    {
    }
    
    protected function getResourceUrl($resource = null)
    {
    }
}
