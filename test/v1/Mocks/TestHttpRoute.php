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

namespace oat\taoRestAPI\test\v1\Mocks;


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\exception\HttpRequestExceptionWithHeaders;
use oat\taoRestAPI\exception\RestApiException;
use oat\taoRestAPI\model\v1\http\filters\Filter;
use oat\taoRestAPI\model\v1\http\filters\Paginate;
use oat\taoRestAPI\model\v1\http\filters\Partial;
use oat\taoRestAPI\model\v1\http\filters\Sort;
use oat\taoRestAPI\model\v1\http\RouterAdapter\AbstractRouterAdapter;
use Psr\Http\Message\ServerRequestInterface;

class TestHttpRoute extends AbstractRouterAdapter
{

    /**
     * @var ServerRequestInterface
     */
    protected $req;

    /**
     * @var Response
     */
    protected $res;
    
    /**
     * Rest API auto runner
     *
     * # Defines and runs the necessary methods for current Http header _method
     *
     * ## for dev test with slim, can compile http responses with correct status codes
     *
     * @param ServerRequestInterface $req
     * @param Response $res
     * @throws HttpRequestException
     */
    public function __invoke(ServerRequestInterface $req = null, Response &$res = null)
    {
        $this->req = $req;
        $this->res = &$res;
        
        try {

            $this->runApiCommand($this->req->getMethod(), $this->req->getAttribute('id'));
                
        } catch (RestApiException $e) {
            $res = $res->withJson(['errors' => [$e->getMessage()]]);
            $res = $res->withStatus($e->getCode());
        }
    }

    /**
     * For phpunit testing data changing (put, post, delete, patch)
     * 
     * @return array
     */
    public function getResources()
    {
        return $this->storage()->searchInstances();
    }

    /**
     * Create new resource
     *
     * @throws HttpRequestException
     */
    public function post()
    {
        parent::post();

        $resource = $this->req->getParsedBody();
        // without data
        if (!$resource) {
            throw new HttpRequestException('Empty Request data.', 400);
        }
        // data validation
        $ids = [];
        foreach ($this->getResources() as $row) {
            $ids[] = $row['id'];
        }
        if (in_array($resource['id'], $ids)) {
            throw new HttpRequestException('Resource with id=' . $resource['id'] . ' exists.', 400);
        }

        //creating
        $this->getResources()[] = $resource;

        $this->res = $this->res->withStatus(201);
        $this->res = $this->res->withHeader('Location', (string)$this->req->getUri() . '/' . $resource['id']);
    }

    public function put()
    {
        parent::put();

        $resource = $this->req->getParsedBody();

        // without data
        if (!$resource) {
            throw new HttpRequestException('Empty Request data.', 400);
        }
        if (!isset($resource['id'])) {
            throw new HttpRequestException('Id is required', 400);
        }

        // data validation
        $ids = [];
        foreach ($this->getResources() as $key => $row) {
            $ids[$key] = $row['id'];
        }
        if (!in_array($this->getResourceId(), $ids)) {
            throw new HttpRequestException('Resource with id=' . $this->getResourceId() . ' not exists.', 400);
        }

        //replace
        $this->storage()->saveResource(array_search($this->getResourceId(), $ids), $resource);
    }

    public function patch()
    {
        parent::patch();

        $resource = $this->req->getParsedBody();

        // without data
        if (!$resource) {
            throw new HttpRequestException('Empty Request data.', 400);
        }
        // data validation
        if (isset($resource['id']) && $resource['id'] !== $this->getResourceId()) {
            throw new HttpRequestException('Invalid Id', 400);
        }

        $ids = [];
        foreach ($this->getResources() as $key => $row) {
            $ids[$key] = $row['id'];
        }
        if (!in_array($this->getResourceId(), $ids)) {
            throw new HttpRequestException('Resource with id=' . $this->getResourceId() . ' not exists.', 400);
        }

        $resourceKey = array_search($this->getResourceId(), $ids);
        $updResource = $this->getResources()[$resourceKey];
        foreach ($resource as $key => $value) {
            $updResource[$key] = $value;
        }
        
        $this->storage()->saveResource($resourceKey, $updResource);
    }

    public function delete()
    {
        parent::delete();

        $ids = [];
        foreach ($this->getResources() as $key => $row) {
            $ids[$key] = $row['id'];
        }
        if (in_array($this->getResourceId(), $ids)) {
            $this->storage()->deleteResource(array_search($this->getResourceId(), $ids));
        }

    }

    public function options()
    {
        $this->res = $this->res->withJson(parent::options());
    }

    protected function getList()
    {
        $queryParams = $this->req->getQueryParams();
        
        $filter = new Filter([
            'query' => $queryParams,
            'fields' => array_keys($this->getResources()[0]),
        ]);
        
        try {
            $paginate = new Paginate([
                'query' => isset($queryParams['range']) ? $queryParams['range'] : '',
                'total' => count($this->getResources()),
                'paginationUrl' => 'http://api.taotest.example/v1/items?range=',
            ]);
        } catch (HttpRequestExceptionWithHeaders $e) {
            // add failed headers if exists
            $this->addFilterHeadersInResponse($e->getHeaders());
            throw new HttpRequestException($e->getMessage(), $e->getCode());
        }

        $partial = new Partial([
            'query' => isset($queryParams['fields']) ? $queryParams['fields'] : '',
            'fields' => array_keys($this->getResources()[0]),
        ]);
        
        $sort = new Sort(['query' => $queryParams]);

        $data = $this->storage()->searchInstances([

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

        $beforePaginationCount = count($this->storage()->searchInstances(['filters' => $filter->getFilters()]));
        
        $paginate->correctPaginationHeader(count($data), $beforePaginationCount);

        if ($this->res->getStatusCode() == 200 && $paginate->getStatusCode()) {
            $this->res = $this->res->withStatus($paginate->getStatusCode());
        }
        // success headers
        $this->addFilterHeadersInResponse($paginate->getHeaders());

        $this->res->setResourceData($data);
    }

    private function addFilterHeadersInResponse(array $addHeaders)
    {
        if (count($addHeaders)) {
            foreach ($addHeaders as $name => $header) {
                $this->res = $this->res->withHeader($name, $header);
            }
        }
    }

    protected function getOne()
    {
        $queryParams = $this->req->getQueryParams();

        $resource = [];
        foreach ($this->getResources() as $resource) {
            if ($resource['id'] == $this->req->getAttribute('id')) {
                break;
            }
        }

        $partial = new Partial([
            'query' => isset($queryParams['fields']) ? $queryParams['fields'] : '',
            'fields' => array_keys($this->getResources()[0]),
        ]);

        foreach ($resource as $key => $value) {
            if (!in_array($key, $partial->getFields())) {
                unset($resource[$key]);
            }
        }

        $this->res->setResourceData($resource);
    }
}
