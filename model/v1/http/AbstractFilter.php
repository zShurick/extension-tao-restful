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

namespace oat\taoRestAPI\model\v1\http;


abstract class AbstractFilter
{

    /**
     * Options for filters
     * @var array
     *  [
     *      query => string|array from Response
     *  ]
     */
    protected $options = ['query' => ''];

    /**
     * @var Response
     */
    protected $response;

    /**
     * Filters constructor.
     * @param Response $response - We can change headers for response in filter
     * @param array $options
     */
    public function __construct(Response &$response, $options = [])
    {
        $this->response = &$response;
        $this->options = array_merge($this->options, $options);
        $this->prepare();
    }

    /**
     * Prepare data for filtering
     * @return mixed
     */
    abstract protected function prepare();
}
