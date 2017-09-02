<?php

namespace ChildThemify;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase {

	protected function setUp() {
		\Brain\Monkey\setUp();
	}

	protected function tearDown() {
		\Brain\Monkey\tearDown();
	}

}
