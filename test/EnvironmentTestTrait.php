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
 */

namespace oat\taoRestAPI\test;


use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class EnvironmentTestTrait
 * @package oat\taoRestAPI\test
 *
 * @author Alexander Zagovorichev <zagovorichev@gmail.com>
 */
trait EnvironmentTestTrait
{
    /**
     * @var App
     */
    private $app = null;

    /**
     * @var Request
     */
    private $request;
    /**
     * @var Response
     */
    private $response;

/*    public function request($method = '', $path = '', $options = [])
    {
        ob_start();

        Environment::mock(array_merge([
            'mode' => 'PhpUnit testing',
            'REQUEST_METHOD' => $method,
            'PATH_INFO' => $path,
            'SERVER_NAME' => 'tao.mock',
        ], $options));

        $this->app = new App();
        $this->request = $this->app->getContainer()->get('request');
        $this->response = $this->app->getContainer()->get('response');

        return ob_end_clean();
    }*/

    public function get($path = '', $callable)
    {
        $this->app = new App();
        return $this->app->get($path, $callable);

        //$this->assertInstanceOf('\Slim\Route', $route);
        //$this->request('GET', $path, $options);
    }
}
