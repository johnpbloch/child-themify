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

	public function test_multisite() {
		if ( !is_multisite() ) {
			$this->markTestSkipped( 'No need to test multisite on single installations.' );
			return;
		}
		$current_theme = wp_get_theme();
		$random = wp_generate_password( 6, false );
		$newThemeName = basename( $current_theme->stylesheet_dir ) . '_' . $random;
		/**
		 * @todo Test permissions for user 1 and user 2
		 * User 1 shouldn't be able to run this in multisite (not a super admin)
		 * User 2 should be able to.
		 */
	}

	/**
	 * @todo Test basic permissions in a normal site. User 0 shouldn't be able to create
	 * a child theme and user 1 should.
	 */

}