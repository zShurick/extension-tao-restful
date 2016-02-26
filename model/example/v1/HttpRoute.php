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


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\exception\HttpRequestExceptionWithHeaders;
use oat\taoRestAPI\model\v1\http\filters\Filter;
use oat\taoRestAPI\model\v1\http\filters\Paginate;
use oat\taoRestAPI\model\v1\http\filters\Partial;
use oat\taoRestAPI\model\v1\http\filters\Sort;
use oat\taoRestAPI\model\v1\http\Request\Router;
use oat\taoRestAPI\test\v1\Mocks\DB;
use Request;
use Response;

/**
 * Class HttpRoute
 * @package oat\taoRestAPI\model\example\v1
 * 
 * #######
 * ===
 * 
 * @SWG\Swagger(
 *   swagger="2.0",
 *   schemes={"http"},
 *   host=API_HOST,
 *   basePath="/taoRestAPI/v1/",
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
 *   @SWG\Response(
 *     response=200,
 *     description="OK"
 *   )
 * )
 * 
 * @SWG\Get(
 *   path="resources?range=0-1",
 *   summary="Pagination",
 *   description="Find resources in range",
 *   tags={"Resources for example"},
 *   operationId="getListPagination",
 *   @SWG\Parameter(
 *      name="range",
 *      in="query",
 *      type="string",
 *      description="Range of the resources",
 *      required=false,
 *   ),
 *   @SWG\Response(
 *     response=206,
 *     description="Partial Content"
 *   )
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
 *      required=false,
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
class HttpRoute extends Router
{
    /**
     * @var Request
     */
    protected $req;

    /**
     * @var Response
     */
    protected $res;

    /**
     * @var DB
     */
    private $db;

    /**
     * Response headers for header() 
     * @var array
     */
    private $httpHeaders = [];

    /**
     * Response status code for header() 
     * @var int
     */
    private $httpStatusCode = 200;

    /**
     * Response Body Data
     * @var
     */
    private $bodyData;

    public function __construct()
    {
        $this->db = new DB();
    }

    public function __invoke(Request $request, Response $response)
    {
        $this->req = $request;
        $this->res = $response;
        
        $this->runApiCommand($this->req->getMethod(), $this->req->getParameter('uri'));
    }

    public function getHeaders()
    {
        return $this->httpHeaders;
    }

    public function getStatusCode()
    {
        return $this->httpStatusCode;
    }
    
    public function getBodyData()
    {
        return $this->bodyData;
    }

    private function addFilterHeadersInResponse(array $addHeaders)
    {
        if (count($addHeaders)) {
            foreach ($addHeaders as $name => $headers) {
                $this->httpHeaders[$name] = $headers; 
            }
        }
    }
    
    protected function getList()
    {
        $queryParams = $this->req->getParameters();

        $filter = new Filter([
            'query' => $queryParams,
            'fields' => array_keys($this->db->getResources()[0]),
        ]);

        try {
            $paginate = new Paginate([
                'query' => isset($queryParams['range']) ? $queryParams['range'] : '',
                'total' => count($this->db->getResources()),
                'paginationUrl' => 'http://api.taotest.example/v1/items?range=',
            ]);
        } catch (HttpRequestExceptionWithHeaders $e) {
            // add failed headers if exists
            $this->addFilterHeadersInResponse($e->getHeaders());
            throw new HttpRequestException($e->getMessage(), $e->getCode());
        }

        $partial = new Partial([
            'query' => isset($queryParams['fields']) ? $queryParams['fields'] : '',
            'fields' => array_keys($this->db->getResources()[0]),
        ]);

        $sort = new Sort(['query' => $queryParams]);

        $this->bodyData = $this->db->searchInstances([

            // use filter by values
            'filters' => $filter->getFilters(),

            // columns
            'fields' => $partial->getFields(),

            // sort
            'sortBy' => $sort->getSorting(),

            // pagination
            'offset' => $paginate->offset(),
            'limit' => $paginate->length(),
        ]);

        $beforePaginationCount = count($this->db->searchInstances(['filters' => $filter->getFilters()]));

        $paginate->correctPaginationHeader(count($this->bodyData), $beforePaginationCount);

        if ($paginate->getStatusCode()) {
            $this->httpStatusCode = $paginate->getStatusCode();
        }

        // success headers
        $this->addFilterHeadersInResponse($paginate->getHeaders());
    }
    
    protected function getOne()
    {
        echo 'one';
    }
}
