<?php

class WP_Test_CTF_Permissions extends WP_UnitTestCase {

	protected $users = array( );
	protected $themes = array( );

	public function setUp() {
		parent::setUp();
		$this->themes = array( );
		$users = $this->factory->user->create_many( 3 );
		foreach ( $users as $user ) {
			$user = get_user_by( 'ID', $user );
			$user->remove_all_caps();
			$this->users[] = $user;
		}
		$this->users[1]->add_cap( 'edit_themes' );
		if ( is_multisite() ) {
			grant_super_admin( $this->users[2]->ID );
		}
	}

	public function tearDown() {
		foreach ( $this->themes as $themePath ) {
			_rmdir( $themePath );
		}
		parent::tearDown();
	}

	public function test_multisite() {
		if ( !is_multisite() ) {
			$this->markTestSkipped( 'No need to test multisite on single installations.' );
			return;
		}
		$current_theme = wp_get_theme();
		$current_theme_slug = basename( $current_theme->stylesheet_dir );
		for ( $x = 0; $x < 3; $x++ ) {
			$exits = CTF_Exit_Overload::count();
			wp_set_auth_cookie( $this->users[$x]->ID );
			wp_set_current_user( $this->users[$x]->ID );
			$_GET['_ctf_nonce'] = wp_create_nonce( 'child_themify_' . $current_theme_slug );
			CTF_Babymaker::getTested();
			switch ( $x ) {
				case 0:
				case 1:
					$this->assertNotEmpty( CTF_Exit_Overload::message() );
					$this->assertNotEquals( $exits, CTF_Exit_Overload::count() );
					break;
				case 2:
					$this->assertEquals( $exits, CTF_Exit_Overload::count() );
					break;
				default:
					$this->fail( "What is this I don't even" );
					break;
			}
			$GLOBALS['current_user'] = null;
			wp_clear_auth_cookie();
		}
	}

	public function test_single() {
		if ( is_multisite() ) {
			$this->markTestSkipped( 'No need to test single installs on multisite' );
		}
	}

}