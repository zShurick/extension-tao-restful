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

namespace oat\taoRestAPI\test\auth;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\model\Authentication\BasicAuthentication;
use oat\taoRestAPI\test\Mocks\EnvironmentTrait;

/**
 * Class BasicAuthenticationTest
 * @package oat\taoRestAPI\test\auth
 */
class BasicAuthenticationTest extends TaoPhpUnitTestRunner
{
    use EnvironmentTrait;
    
    private $credentials;
    
    public function setUp()
    {
        $this->credentials = ['username1234', 'password1234', 'token1234'];
    }
    
    protected function appendBasicAuth($app, array $config = [])
    {
        $config = array_merge([
            'authenticator' => function ($username = null, $password = null) {
                if ($this->credentials[0] === $username && $this->credentials[1] === $password) {
                    return $this->credentials[2];
                }
                return false;
            }
        ], $config);
        
        return new BasicAuthentication($app, $config);
    }

    public function protectedAndUnprotectedResources()
    {
        return [
            ['/', 'Root.'],
            ['/protected/resource', 'Protected Resource.'],
        ];
    }

    /**
     * @dataProvider protectedAndUnprotectedResources
     */
    protected function testAllowAccessToResource($resource, $expectedContent)
    {
        $app = $this->appendBasicAuth($this->app);
        var_dump($app);die;
        $this->request('GET', $resource, [], [], [
            'PHP_AUTH_USER' => $this->credentials[0],
            'PHP_AUTH_PW' => $this->credentials[1],
        ]);
        $this->assertEquals($expectedContent, (string)$this->response->getBody());
    }
}
