<?php

class WP_Test_CTF_Interface extends WP_UnitTestCase {

	protected static $method = 'direct';

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		add_filter( 'filesystem_method', array( __CLASS__, '_fs_method' ), 999 );
	}

	public static function _fs_method() {
		// should be either 'direct', 'ssh', 'ftpext', or 'ftpsockets'
		return self::$method;
	}

	public function setUp() {
		parent::setUp();
		self::$method = 'direct';
	}

	public function test_output() {
		ob_start();
		CTF_Babymaker::show_interface();
		$contents = ob_get_clean();
		$this->assertContains( 'child theme', strtolower( $contents ) );
		$this->assertNotContains( '<h2>Connection Information</h2>', $contents );
	}

	public function test_filesystem_use( $method ) {
		remove_all_filters( 'request_filesystem_credentials', 169 );
		add_filter( 'request_filesystem_credentials', '__return_false', 169 );
		foreach ( array( 'ssh', 'ftpext', 'ftpsockets' ) as $method ) {
			self::$method = $method;
			ob_start();
			CTF_Babymaker::show_interface();
			$contents = ob_get_clean();
			$this->assertContains( '<h2>Connection Information</h2>', $contents );
			$this->assertNotContains( 'child theme', strtolower( $contents ) );
		}
	}

}
