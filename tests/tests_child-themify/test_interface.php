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
		$user = $this->factory->user->create();
		$user = new WP_User( $user );
		$user->add_cap( 'install_themes' );
		if ( is_multisite() ) {
			grant_super_admin( $user->ID );
		}
		wp_set_current_user( $user->ID );
		$theme = wp_get_theme();
		$_GET['theme'] = $_REQUEST['theme'] = $theme->get_stylesheet();
		$_GET['_ctf_nonce'] = $_REQUEST['_ctf_nonce'] = wp_create_nonce( 'child_themify_' . $theme->get_stylesheet() );
	}

	public function test_output() {
		ob_start();
		CTF_Babymaker::showInterface();
		$contents = ob_get_clean();
		$this->assertContains( 'child theme', strtolower( $contents ) );
		$this->assertNotContains( '<h2>Connection Information</h2>', $contents );
	}

	public function test_filesystem_use() {
		$_POST['new_theme'] = 'dummyvalue';
		remove_all_filters( 'request_filesystem_credentials' );
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
