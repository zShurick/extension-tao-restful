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


class TaoRouterAdapter extends AbstractRouterAdapter
{
    /**
     * @var Request
     */
    protected $req;

    public function __invoke(Request $req)
    {
        $this->req = $req;
        $this->runApiCommand($this->req->getMethod(), $this->req->getParameter('uri'));
    }

    protected function getList(array $params = null)
    {
        $queryParams = $this->req->getParameters();
        parent::getList($queryParams);
    }

    protected function getOne()
    {
        parent::getOne( $this->req->hasParameter('fields') ? $this->req->getParameter('fields') : '' );
    }
    
    protected function getResourceUrl($uri = null)
    {
        return \tao_helpers_Uri::getPath('?uri=' . parent::getResourceUrl($uri));
    }
    
    protected function getParsedBody()
    {
        // todo in here post, put, patch parameters
    }
}
