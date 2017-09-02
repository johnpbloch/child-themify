<?php

namespace ChildThemify;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

	protected function setUp() {
		\Brain\Monkey\setUp();
	}

	protected function tearDown() {
		\Brain\Monkey\tearDown();
	}

	public function assertConditionsMet( $message = '' ) {
		$this->assertThat( null, new ExpectationsMetConstraint, $message );
	}

}
