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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */

namespace oat\taoRestAPI\model;


interface RestApiInterface
{
    
    const SERVICE_ID = 'taoRestAPI/restApi';

    /**
     * Current version
     *
     * @var string
     */
    const VERSION = '1.0.0';
    
    /**
     * Get data
     * HTTP GET
     * @param null $uri - if null get list of resources, else one resource
     * @param null $params - params for getting data (partial content page or list of fields from resource)
     * @return array|resource 200 or 206 ("Partial Content" if many items and used pagination)
     *
     * @author Alexander Zagovorichev <zagovorichev@gmail.com>
     */
    public function get($uri = null, $params = null);

    /**
     * Create new record
     * HTTP POST
     * @return mixed
     *
     * @author Alexander Zagovorichev <zagovorichev@gmail.com>
     */
    public function post();

    /**
     * Update full record (all data in record)
     * HTTP PUT
     * @return mixed
     *
     * @author Alexander Zagovorichev <zagovorichev@gmail.com>
     */
    public function put();

    /**
     * Change only specified data (not all record)
     * HTTP PATCH
     * @return mixed
     *
     * @author Alexander Zagovorichev <zagovorichev@gmail.com>
     */
    public function patch();

    /**
     * Delete record
     * HTTP DELETE
     * @return mixed
     *
     * @author Alexander Zagovorichev <zagovorichev@gmail.com>
     */
    public function delete();

    /**
     * Return options for record (List of the available rest operations)
     * HTTP OPTIONS
     *
     * # Can be:
     * # 200 Ok
     * # Allow: OPTIONS, GET, POST, DELETE
     *
     * # Or a complete description of the available rest operations
     * # {
     * #   "POST": {
     * #      "description": "Create an issue",
     * #      "parameters": {
     * #        "title": {
     * #          "type": "string",
     * #          "description": "Issue title",
     * #          "required": true
     * #        },
     * #        "body": {
     * #          "type": "string",
     * #           "description": "Issue body"
     * #        }
     * #    }
     * # }
     * #
     * @return mixed
     *
     * @author Alexander Zagovorichev <zagovorichev@gmail.com>
     */
    public function options();
    
}