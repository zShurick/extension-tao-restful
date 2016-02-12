<?php
/**
 * Copyright (c) 2016 Open Assessment Technologies, S.A.
 *
 * @author Alexander Zagovorichev, <zagovorichev@1pt.com>
 */

require_once dirname(__FILE__). '/../tao/includes/class.Bootstrap.php';

$bootStrap = new BootStrap('taoQtiTest');
$bootStrap->start();
$bootStrap->dispatch();
