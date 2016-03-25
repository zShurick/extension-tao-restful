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

namespace oat\taoRestAPI\model\v1\http\Request\RouterAdapter;


use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\exception\HttpRequestExceptionWithHeaders;
use oat\taoRestAPI\exception\RestApiException;
use oat\taoRestAPI\model\DataStorageInterface;
use oat\taoRestAPI\model\RouterAdapterInterface;
use oat\taoRestAPI\model\v1\http\filters\Filter;
use oat\taoRestAPI\model\v1\http\filters\Paginate;
use oat\taoRestAPI\model\v1\http\filters\Partial;
use oat\taoRestAPI\model\v1\http\filters\Sort;
use oat\taoRestAPI\model\v1\http\Request\Router;

abstract class AbstractRouterAdapter extends Router implements RouterAdapterInterface
{
    /**
     * @var DataStorageInterface
     */
    private $storage;

    /**
     * Response status code for header()
     * @var int
     */
    private $httpStatusCode = 200;

    /**
     * Response Body Data
     * @var
     */
    protected $bodyData;

    /**
     * Response headers for header()
     * @var array
     */
    private $httpHeaders = [];

    public function __construct(DataStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return DataStorageInterface
     */
    public function storage()
    {
        return $this->storage;
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

    protected function addHeaders(array $addHeaders)
    {
        if (count($addHeaders)) {
            foreach ($addHeaders as $name => $headers) {
                $this->httpHeaders[$name] = $headers;
            }
        }
    }

    protected function setStatusCode($status = 0)
    {
        $this->httpStatusCode = intval($status);
    }

    protected function getList(array $queryParams=null)
    {
        
        $filter = new Filter([
            'query' => $queryParams,
            'fields' => $this->storage()->getFields(),
        ]);
        
        try {
            $paginate = new Paginate([
                'query' => isset($queryParams['range']) ? $queryParams['range'] : '',
                'total' => count($this->storage()->searchInstances()),
                'paginationUrl' => 'http://api.taotest.example/v1/items?range=',
            ]);
        } catch (HttpRequestExceptionWithHeaders $e) {
            // add failed headers if exists
            $this->addHeaders($e->getHeaders());
            throw new HttpRequestException($e->getMessage(), $e->getCode());
        }

        $partial = new Partial([
            'query' => isset($queryParams['fields']) ? $queryParams['fields'] : '',
            'fields' => $this->storage()->getFields(),
        ]);

        $sort = new Sort(['query' => $queryParams]);

        $this->bodyData = $this->storage()->searchInstances([

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

        $paginate->correctPaginationHeader(count($this->bodyData), $beforePaginationCount);

        if ($this->getStatusCode() == 200 && $paginate->getStatusCode()) {
            $this->setStatusCode($paginate->getStatusCode());
        }
        
        $this->addHeaders($paginate->getHeaders());
    }
    
    protected function getOne($partialFields = '')
    {
        
        $partial = new Partial([
            'query' => $partialFields,
            'fields' => $this->storage()->getFields(),
        ]);

        $this->bodyData = $this->storage()->getOne($this->getResourceId(), $partial->getFields());
    }
    
    public function post()
    {
        parent::post();
        
        $id = $this->storage()->create($this->getResourceData());
        
        // return new resource
        $this->bodyData = $this->storage()->getOne($id, $this->storage()->getFields());
        $this->setStatusCode(201);
        $this->addHeaders(['Location' => $this->getResourceUrl($id)]);
    }

    public function put()
    {
        parent::put();
        
        $this->storage()->put($this->getResourceId(), $this->getResourceData());
        
        $this->bodyData = $this->storage()->getOne($this->getResourceId(), $this->storage()->getFields());
    }

    public function patch()
    {
        parent::patch();
        
        $this->storage()->patch($this->getResourceId(), $this->getResourceData());

        $this->bodyData = $this->storage()->getOne($this->getResourceId(), $this->storage()->getFields());
    }
    
    public function delete()
    {
        parent::delete();
        
        $this->storage()->delete($this->getResourceId());
    }

    public function options()
    {
        $this->bodyData = parent::options();
    }

    /**
     * Get parsed request body (for put, patch, post requests)
     * for validation can be used $this->getResourceData
     * 
     * @return mixed
     */
    abstract protected function getParsedBody();

    /**
     * Resource address
     *
     * @param null $id
     * @return mixed|null
     * @throws RestApiException
     */
    protected function getResourceUrl($id = null) 
    {
        if ($id && $this->storage()->exists($id)) {
            // use id
        } elseif ($this->getResourceId() && $this->storage()->exists($this->getResourceId())) {
            $id = $this->getResourceId();
        } else {
            throw new RestApiException('Undefined resource identifier', 400);
        }
        
        return $id;
    }

    /**
     * Get requested data with validation
     * @return mixed
     * @throws HttpRequestException
     */
    private function getResourceData()
    {
        $resourceData = $this->getParsedBody();

        if (!$resourceData) {
            throw new HttpRequestException('Empty Request data.', 400);
        }

        return $resourceData;
    }
}
