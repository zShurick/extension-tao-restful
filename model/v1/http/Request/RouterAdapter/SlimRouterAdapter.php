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
use oat\taoRestAPI\exception\RestApiException;
use Psr\Http\Message\ServerRequestInterface;

class SlimRouterAdapter extends AbstractRouterAdapter
{

    /**
     * @var ServerRequestInterface
     */
    protected $req;

    /**
     * Rest API auto runner
     *
     * # Defines and runs the necessary methods for current Http header _method
     *
     * ## for dev test with slim, can compile http responses with correct status codes
     *
     * @param ServerRequestInterface $req
     * @throws HttpRequestException
     */
    public function __invoke(ServerRequestInterface $req = null)
    {
        $this->req = $req;
        $this->runApiCommand($this->req->getMethod(), $this->req->getAttribute('id'));
    }
    
    public function getList(array $params=null)
    {
        $queryParams = $this->req->getQueryParams();
        parent::getList($queryParams);
    }

    protected function getOne()
    {
        $queryParams = $this->req->getQueryParams();
        parent::getOne(isset($queryParams['fields']) ? $queryParams['fields'] : '');
    }
    
    protected function getResourceUrl($id=null)
    {
        return (string)$this->req->getUri() . '/' . parent::getResourceUrl($id);
    }
    
    protected function getParsedBody()
    {
        return $this->req->getParsedBody();
    }
}
