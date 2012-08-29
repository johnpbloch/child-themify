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
		$theme = wp_get_theme();
		$_GET['theme'] = $_REQUEST['theme'] = $theme->get_stylesheet();
		$user = $this->factory->user->create();
		$user = new WP_User( $user );
		$user->add_cap( 'install_themes' );
		if ( is_multisite() ) {
			grant_super_admin( $user->ID );
		}
		wp_set_current_user($user->ID);
	}

	public function test_output() {
		ob_start();
		CTF_Babymaker::showInterface();
		$contents = ob_get_clean();
		$this->assertContains( 'child theme', strtolower( $contents ) );
		$this->assertNotContains( '<h2>Connection Information</h2>', $contents );
	}

	public function test_filesystem_use() {
		remove_all_filters( 'request_filesystem_credentials', 169 );
		add_filter( 'request_filesystem_credentials', '__return_false', 169 );
		foreach ( array( 'ssh', 'ftpext', 'ftpsockets' ) as $method ) {
			self::$method = $method;
			ob_start();
			CTF_Babymaker::showInterface();
			$contents = ob_get_clean();
			$this->assertContains( '<h2>Connection Information</h2>', $contents );
			$this->assertNotContains( 'child theme', strtolower( $contents ) );
		}
	}

}
