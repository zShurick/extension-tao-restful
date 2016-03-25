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

namespace oat\taoRestAPI\model\v1\StorageAdapter;


use oat\taoRestAPI\exception\RestApiException;
use oat\taoRestAPI\model\DataStorageInterface;

abstract class AbstractStorageAdapter implements DataStorageInterface
{
    public function put($id, array $propertiesValues)
    {
        $this->throwIfResourceNotExists($id);
    }
    
    public function patch($id, array $propertiesValues)
    {
        $this->throwIfResourceNotExists($id);
    }
    
    abstract public function exists($id);
    
    /**
     * @param $id
     * @throws RestApiException
     */
    private function throwIfResourceNotExists($id)
    {
        if (!$this->exists($id)) {
            throw new RestApiException(__('Resource not found'), 404);
        }
    }
}
