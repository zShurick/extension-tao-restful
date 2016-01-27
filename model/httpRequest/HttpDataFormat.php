<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model\httpRequest;


use common_exception_ClientException;
use oat\taoRestAPI\exception\HttpRequestException;
use oat\taoRestAPI\model\HttpDataFormatInterface;
use tao_helpers_Http;

class HttpDataFormat implements HttpDataFormatInterface
{

    private static $acceptedMimeTypes = [
        'application/json' => 'JsonEncoder',
        'text/xml' => 'XmlEncoder', 
        'application/xml' => 'XmlEncoder', 
        //'application/rdf+xml' => 'RdfEncoder', todo
    ];
    
    public static function encoder()
    {
        $format = 'JsonEncoder';
        if(!empty($_SERVER['HTTP_ACCEPT'])){
            try {
                $accept = tao_helpers_Http::acceptHeader(array_keys(static::$acceptedMimeTypes), $_SERVER['HTTP_ACCEPT']);
                $format = static::$acceptedMimeTypes[$accept];
            }
            catch (common_exception_ClientException $e) {
                throw new HttpRequestException(__('Not acceptable encoding format'), 406);
            }
        }
        
        $encoder = '\oat\taoRestAPI\model\dataEncoder\\' . $format;
        return new $encoder();
    }
}
