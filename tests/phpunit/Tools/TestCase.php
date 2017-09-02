<?php

namespace ChildThemify;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Brain\Monkey\Functions;

abstract class TestCase extends BaseTestCase {

	protected function setUp() {
		\Brain\Monkey\setUp();
		$this->boringPassthrus();
	}

	protected function tearDown() {
		\Brain\Monkey\tearDown();
	}

	public function assertConditionsMet( $message = '' ) {
		$this->assertThat( null, new ExpectationsMetConstraint, $message );
	}

	protected function boringPassthrus() {
		Functions\when( '__' )->returnArg( 1 );
		Functions\when( '_x' )->returnArg( 1 );
		Functions\when( '_e' )->echoArg( 1 );
		Functions\when( 'esc_html__' )->returnArg( 1 );
		Functions\when( 'esc_html_x' )->returnArg( 1 );
		Functions\when( 'esc_html_e' )->echoArg( 1 );
		Functions\when( 'esc_attr__' )->returnArg( 1 );
		Functions\when( 'esc_attr_x' )->returnArg( 1 );
		Functions\when( 'esc_attr_e' )->echoArg( 1 );
		Functions\when( 'esc_html' )->returnArg( 1 );
		Functions\when( 'esc_attr' )->returnArg( 1 );
		Functions\when( 'esc_url' )->returnArg( 1 );
		Functions\when( 'esc_url_raw' )->returnArg( 1 );
		Functions\when( 'esc_textarea' )->returnArg( 1 );
	}

}
