<?php
/**
 * Add the unit test namespace(s) to the autoloader.
 */

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('KixeyeChallengeTest', __DIR__);
$loader->add('KixeyeLibsTest', __DIR__);
