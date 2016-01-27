<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

namespace oat\taoRestAPI\model;


interface HttpDataFormatInterface
{
    
    /**
     * @return DataEncoderInterface
     */
    public static function encoder();
}