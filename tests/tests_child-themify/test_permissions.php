<?php

class WP_Test_CTF_Permissions extends WP_UnitTestCase {

	protected $users = array( );
	protected $themes = array( );

	public function setUp() {
		parent::setUp();
		$this->themes = $this->users = array( );
		$users = $this->factory->user->create_many( 3 );
		foreach ( $users as $user ) {
			$user = get_user_by( 'ID', $user );
			$user->remove_all_caps();
			$this->users[] = $user;
		}
		$this->users[1]->add_cap( 'install_themes' );
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
			wp_set_current_user( $this->users[$x]->ID );
			$_GET['_ctf_nonce'] = wp_create_nonce( 'child_themify_' . $current_theme_slug );
			CTF_Babymaker::getTested();
			switch ( $x ) {
				case 0:
				case 1:
					$this->assertNotEquals( $exits, CTF_Exit_Overload::count() );
					break;
				case 2:
					$this->assertEquals( $exits, CTF_Exit_Overload::count() );
					break;
				default:
					$this->fail( "What is this I don't even" );
					break;
			}
			wp_set_current_user( 0 );
		}
	}

	public function test_single() {
		if ( is_multisite() ) {
			$this->markTestSkipped( 'No need to test single installs on multisite' );
		}
		$current_theme = wp_get_theme();
		$current_theme_slug = basename( $current_theme->stylesheet_dir );
		for ( $x = 0; $x < 3; $x++ ) {
			$exits = CTF_Exit_Overload::count();
			wp_set_current_user( $this->users[$x]->ID );
			$_GET['_ctf_nonce'] = wp_create_nonce( 'child_themify_' . $current_theme_slug );
			CTF_Babymaker::getTested();
			switch ( $x ) {
				case 0:
				case 2:
					$this->assertNotEquals( $exits, CTF_Exit_Overload::count() );
					break;
				case 1:
					$this->assertEquals( $exits, CTF_Exit_Overload::count() );
					break;
				default:
					$this->fail( "What is this I don't even" );
					break;
			}
			wp_set_current_user( 0 );
		}
	}

	/**
	 * @runInSeparateProcess
	 */
	public function test_disallow_file_mods() {
		if ( defined( 'DISALLOW_FILE_MODS' ) ) {
			$this->markTestSkipped( "Can't test for constants if they're already defined" );
			return;
		}
		$workingUser = is_multisite() ? 2 : 1;
		wp_set_current_user( $this->users[$workingUser]->ID );
		$count = CTF_Exit_Overload::count();
		CTF_Babymaker::getTested();
		$this->assertEquals( $count, CTF_Exit_Overload::count() );
		define( 'DISALLOW_FILE_EDIT', true );
		$count = CTF_Exit_Overload::count();
		CTF_Babymaker::getTested();
		$this->assertEquals( $count, CTF_Exit_Overload::count() );
		define( 'DISALLOW_FILE_MODS', true );
		$count = CTF_Exit_Overload::count();
		CTF_Babymaker::getTested();
		$this->assertNotEquals( $count, CTF_Exit_Overload::count() );
	}

}