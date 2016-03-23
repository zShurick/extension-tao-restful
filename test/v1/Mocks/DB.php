<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\test\v1\Mocks;
use oat\taoRestAPI\model\v1\StorageAdapter\ArrayStorageAdapter;


/**
 * Class DB
 * 
 * Local static DB for testing RestApi
 * 
 * @package oat\taoRestAPI\test\v1\Mocks
 */
class DB extends ArrayStorageAdapter
{

    /**
     * Data for testing and example of workflow
     * @var array
     */
    protected $resourcesData = [
        [
            'id' => 1,
            'title' => 'Potato',
            'type' => 'vegetable',
            'form' => 'circle',
            'color' => 'brown',
        ],
        [
            'id' => 2,
            'title' => 'Lemon',
            'type' => 'citrus',
            'form' => 'ellipse',
            'color' => 'yellow',
        ],
        [
            'id' => 3,
            'title' => 'Lime',
            'type' => 'citrus',
            'form' => 'ellipse',
            'color' => 'green',
        ],
        [
            'id' => 4,
            'title' => 'Carrot',
            'type' => 'vegetable',
            'form' => 'conical',
            'color' => 'orange',
        ],
        [
            'id' => 5,
            'title' => 'Orange',
            'type' => 'citrus',
            'form' => 'circle',
            'color' => 'orange',
        ],
    ];
}
