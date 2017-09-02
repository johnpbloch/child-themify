<?php

namespace ChildThemify;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Brain\Monkey\Functions;

abstract class TestCase extends BaseTestCase {

	protected $__contentFilterCallback;

	protected function setUp() {
		\Brain\Monkey\setUp();
		$this->boringPassthrus();
		$this->setUpContentFiltering();
	}

	protected function tearDown() {
		\Brain\Monkey\tearDown();
	}

	public function assertConditionsMet( $message = '' ) {
		$this->assertThat( null, new ExpectationsMetConstraint, $message );
	}

	public function expectOutputString( $expectedString ) {
		if ( is_callable( $this->__contentFilterCallback ) ) {
			$expectedString = call_user_func( $this->__contentFilterCallback, $expectedString );
		}
		parent::expectOutputString( $expectedString );
	}

	public function stripTabsAndNewlines( $content ) {
		return str_replace( array( "\t", "\r", "\n" ), '', $content );
	}

	protected function setUpContentFiltering() {
		$this->__contentFilterCallback = false;
		$annotations                   = $this->getAnnotations();
		if (
			! isset( $annotations['stripTabsAndNewlinesFromOutput'] ) ||
			$annotations['stripTabsAndNewlinesFromOutput'][0] !== 'disabled' ||
			(
				is_numeric( $annotations['stripTabsAndNewlinesFromOutput'][0] ) &&
				(int) $annotations['stripTabsAndNewlinesFromOutput'][0] !== 0
			)
		) {
			$this->__contentFilterCallback = array( $this, 'stripTabsAndNewlines' );
			$this->setOutputCallback( $this->__contentFilterCallback );
		}
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
