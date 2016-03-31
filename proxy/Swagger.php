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

namespace oat\taoRestAPI\proxy;


use oat\taoRestAPI\exception\RestApiDocsException;
use oat\taoRestAPI\service\docs\DocsProxy;

/**
 * Use swagger for documentation
 *
 * Class Swagger
 * @package oat\taoRestAPI\service\docs\proxy
 */
class Swagger extends DocsProxy
{

    /**
     * @param string $class
     * @return mixed
     * @throws RestApiDocsException
     */
    public function generateDocs($class = '')
    {
        $path = $this->getPath($class);
        if (!file_exists($path)) {
            throw new RestApiDocsException('Incorrect file path for parsing documentation ' . $class);
        }
        
        $docs = [];
        try{
            $docs[$class]['swagger'] = \Swagger\scan($path);
            $docs[$class]['parent'] = $this->getParent($class);
        } catch (\Exception $e) {
            // if swagger docs not found in current class, looking in parent classes
            if ($e->getMessage() == 'Required @SWG\Info() not found') {

                if (!isset($docs[$class])) {
                    $docs[$class] = [];
                }

                $docs[$class]['swagger'] = null;
                $docs[$class]['parent'] = $this->getParent($class);
            } else {
                throw new RestApiDocsException($e->getMessage());
            }
            
            if (!isset ($docs)) {
                throw new RestApiDocsException('RestApi documentation does not found');
            }
        }
        return $docs;
    }
}
