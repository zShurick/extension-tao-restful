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

namespace oat\taoRestAPI\model\route;


use oat\oatbox\service\ServiceManager;
use oat\tao\model\routing\Route;
use oat\taoRestAPI\exception\RestApiException;
use oat\taoRestAPI\helpers\Response;
use oat\taoRestAPI\service\v1\RestApiService;


/**
 * #1 Looking for {ExtName}/RestApi/v{int} in url
 * #2 Load Configuration from extension and append it on RestApi router
 * #2.1 Throw Extension if configuration file does not exists
 * 
 * Class ResourceRoute
 * @package oat\taoRestAPI\model\route
 */
class ResourceRoute extends Route
{
    const CONFIG_NAME = 'restApi';
    
    public function resolve($relativeUrl) {
        try {
            
            $parts = explode('/', $relativeUrl);
            if ($parts[1] == 'RestApi' && preg_match('/v\d+/', $parts[2]) == 1) {
                //return 'oat\\taoRestAPI\\controller\\v1@protocol';
                
                //$parts = explode('/', $this->getRequest()->getRequestURI());
                //var_dump($this->getExtension());die;
                $extensionId = $parts[0];
                $extension = \common_ext_ExtensionsManager::singleton()->getExtensionById($extensionId);
                $config = $extension->getConfig(self::CONFIG_NAME);

                try {

                    $service = ServiceManager::getServiceManager()->get(RestApiService::SERVICE_ID);
                    
                    if (isset($config['storageService'])) {
                        // if str pos :: then singleton else should use service manager 
                        if (is_array($config['storageService'])) {
                            $storageService = call_user_func($config['storageService']);
                        } else {
                            $storageService = new $config['storageService'];
                        }
                        $storageAdapter = new $config['storageAdapter']( $storageService );
                    } else {
                        $storageAdapter = new $config['storageAdapter'];
                    }

                    if (isset($config['encoder'])) {
                        $service->setEncoder(new $config['encoder']);
                    }

                    $service->setRouter(new $config['routerAdapter']($storageAdapter));

                    if (isset($config['authenticator'])) {
                        $service->setAuth(new $config['authenticator']);
                    }

                    $service->execute(function ($router, $encoder) use ($config) {
                        
                        $router(
                            \Context::getInstance()->getRequest(),
                            $config['idRule']
                        );
                        
                        Response::write(
                            $router->getStatusCode(),
                            $encoder->getContentType(),
                            $router->getHeaders(),
                            $encoder->encode($router->getBodyData())
                        );
                    });
                } catch (RestApiException $e) {
                    Response::write($e->getCode(), 'text/plain', [], $e->getMessage());
                }
                
            }
        } catch (\ResolverException $r) {
            // namespace does not match URL, aborting
        }
        return null;
    }
}
