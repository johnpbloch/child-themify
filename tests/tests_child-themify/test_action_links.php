<?php

class WP_Test_CTF_Action_Links extends WP_UnitTestCase {

	/**
	 * @var WP_User
	 */
	protected $user;

	/**
	 * @var WP_Theme
	 */
	protected $theme;

	public function setUp() {
		parent::setUp();
		$user = null;
		while ( !$user ) {
			$user = $this->factory->user->create();
			if ( !is_scalar( $user ) ) {
				$user = null;
			}
		}
		$this->user = new WP_User( $user );
		$this->user->add_cap( 'install_themes' );
		if ( is_multisite() ) {
			grant_super_admin( $this->user->ID );
		}
		wp_set_current_user( $this->user->ID );
		$this->theme = wp_get_theme();
	}

	public function test_get_link_regular() {
		if ( is_multisite() ) {
			$this->markTestSkipped();
			return;
		}
		$theme_slug = $this->theme->get_stylesheet();
		$args = array(
			'action' => 'child-themify',
			'theme' => $theme_slug,
			'_ctf_nonce' => wp_create_nonce( "child_themify_$theme_slug" ),
		);
		$link = add_query_arg( $args, admin_url( 'themes.php' ) );
		$this->assertEquals( $link, Child_Themify::getLink( $theme_slug ) );
	}

	public function test_get_link_network() {
		if ( !is_multisite() ) {
			$this->markTestSkipped();
			return;
		}
		$theme_slug = $this->theme->get_stylesheet();
		$args = array(
			'action' => 'child-themify',
			'theme' => $theme_slug,
			'_ctf_nonce' => wp_create_nonce( "child_themify_$theme_slug" ),
		);
		$link = add_query_arg( $args, network_admin_url( 'themes.php' ) );
		$this->assertEquals( $link, Child_Themify::getLink( $theme_slug ) );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function test_action_links() {
		$theme_slug = $this->theme->get_stylesheet();
		$links = Child_Themify::addActionLink( array( ), $this->theme );
		$this->assertInternalType( 'array', $links );
		$this->assertArrayHasKey( 'child-themify', $links );
		$link = Child_Themify::getLink( $theme_slug );
		$this->assertContains( $link, $links['child-themify'] );
		define( 'DISALLOW_FILE_MODS', true );
		$links = Child_Themify::addActionLink( array( ), $this->theme );
		$this->assertInternalType( 'array', $links );
		$this->assertArrayNotHasKey( 'child-themify', $links );
	}

}
