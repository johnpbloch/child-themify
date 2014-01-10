<?php

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	throw new Exception( 'You must install the project\'s dev dependencies with Composer before you can run the unit test suite!' );
}

require_once __DIR__ . '/vendor/antecedent/patchwork/Patchwork.php';
require_once __DIR__ . '/vendor/autoload.php';
