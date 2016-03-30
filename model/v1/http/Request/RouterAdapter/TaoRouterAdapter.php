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


use Request;

/**
 * Class TaoRouterAdapter
 * @package oat\taoRestAPI\model\v1\http\Request\RouterAdapter
 * @deprecated use Slim, please
 */
class TaoRouterAdapter extends AbstractRouterAdapter
{
    /**
     * @var Request
     */
    protected $req;

    public function __invoke(Request $req)
    {
        $this->req = $req;
        $queryParams = $this->getQueryParams();
        $uri = isset($queryParams['uri']) ? $queryParams['uri'] : null;
        $this->runApiCommand($this->req->getMethod(), $uri);
    }

    protected function getUri()
    {
        return \tao_helpers_Uri::getPath('');
    }

    protected function getResourceUrl($uri = null)
    {
        return \tao_helpers_Uri::getPath('?uri=' . parent::getResourceUrl($uri));
    }

    /**
     * Get params from Get request
     */
    protected function getQueryParams()
    {
        if (!isset($this->queryParams)){
            $this->queryParams = [];
            foreach ($_GET as $key => $param) {
                $this->queryParams[urldecode($key)] = urldecode($param);
            }
        }

        return $this->queryParams;
    }

    protected function getParsedBody()
    {
        switch ($this->req->getMethod()) {
            case 'PUT':
                $parameters = \tao_helpers_Http::getJsonDataFromStream();
                break;
            default:
                $parameters = $this->req->getParameters();
        }
        
        //exclude uri parameters (this only for access to resource)
        if (is_array($parameters) && key_exists('uri', $parameters)) {
            unset($parameters['uri']);
        }
        
        return $parameters;
    }
}
