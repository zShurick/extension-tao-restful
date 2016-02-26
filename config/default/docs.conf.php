<?php

/**
 * Documentation for Restful service extension
 */

// used by swagger
define('API_HOST', preg_replace('|^.*://(.*)$|', "\\1", ROOT_URL));

return new \oat\taoRestAPI\service\docs\RestApiDocsService([
    'proxy' => 'Swagger',
    'routes' => DEBUG_MODE ? [
        'Example' => '\oat\taoRestAPI\model\example\v1\HttpRoute'
    ] : []
]);
