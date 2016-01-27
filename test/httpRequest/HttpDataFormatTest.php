<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\test\httpRequest;


use oat\tao\test\TaoPhpUnitTestRunner;
use oat\taoRestAPI\model\httpRequest\HttpDataFormat;

class HttpDataFormatTest extends TaoPhpUnitTestRunner
{
    /**
     * @expectedException \oat\taoRestAPI\exception\HttpRequestException
     */
    public function testIncorrectMimeType()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/xls';
        HttpDataFormat::encoder();
    }

    public function testDefaultEncoder()
    {
        $encoder = HttpDataFormat::encoder();
        $this->assertInstanceOf('oat\taoRestAPI\model\dataEncoder\JsonEncoder', $encoder);
    }
    
    public function testJsonEncoder()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $encoder = HttpDataFormat::encoder();
        $this->assertInstanceOf('oat\taoRestAPI\model\dataEncoder\JsonEncoder', $encoder);
        $this->assertEquals('[]', $encoder->encode([]));
        $this->assertEquals('{"key":"val"}', $encoder->encode(['key' => 'val']));
        $this->assertEquals('[{"key":{"key":"val"}},{"key":"val"}]', $encoder->encode([['key' => ['key' => 'val']], ['key' => 'val']]));
    }
    
    public function testXmlEncoder()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/xml';
        $encoder = HttpDataFormat::encoder();
        $this->assertInstanceOf('oat\taoRestAPI\model\dataEncoder\XmlEncoder', $encoder);
        $this->assertEquals("<?xml version=\"1.0\"?>\n<root/>\n", $encoder->encode([]));
        $this->assertEquals("<?xml version=\"1.0\"?>\n<root>\n  <key>val</key>\n</root>\n", $encoder->encode(['key' => 'val']));
        $this->assertEquals("<?xml version=\"1.0\"?>\n<root>\n  <element>\n    <key>\n      <key>val</key>\n    </key>\n  </element>\n  <element>\n    <key>val</key>\n  </element>\n</root>\n", $encoder->encode([['key' => ['key' => 'val']], ['key' => 'val']]));
    }
    
    public function testRdfEncoder()
    {
        //...
    }
}
