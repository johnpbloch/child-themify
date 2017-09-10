<?php

namespace ChildThemify;

use Brain\Monkey\Functions;

class AdminTest extends TestCase {

	public function testSetup() {
		Functions\when( 'is_multisite' )->justReturn( false );
		child_themify_admin_init();
		$this->assertTrue( has_action( 'admin_menu', 'child_themify_admin_menu' ) );
		$this->assertFalse( has_action( 'network_admin_menu', 'child_themify_admin_menu' ) );
	}

	public function testSetupMultisite() {
		Functions\when( 'is_multisite' )->justReturn( true );
		child_themify_admin_init();
		$this->assertFalse( has_action( 'admin_menu', 'child_themify_admin_menu' ) );
		$this->assertTrue( has_action( 'network_admin_menu', 'child_themify_admin_menu' ) );
	}

	public function testAdminMenu() {
		$hook = 'child_themify-page' . rand( 0, 9 );
		Functions\expect( 'add_theme_page' )
			->once()
			->with(
				'Create Child Theme',
				'Create Child Theme',
				'install_themes',
				'child_themify',
				'child_themify_admin_page'
			)
			->andReturn( $hook );
		child_themify_admin_menu();
		$this->assertTrue( has_action( "load-$hook", 'child_themify_admin_page_load' ) );
	}

	public function testAdminPageLoad() {
		Functions\when( 'child_themify_css' )->justReturn( 'css' );
		Functions\when( 'child_themify_asset_version' )->justReturn( '1.0.0' );
		Functions\expect( 'wp_enqueue_style' )
			->once()
			->with( 'child-themify', 'css', array(), '1.0.0' );
		child_themify_admin_page_load();
		$this->assertConditionsMet();
	}

	public function testAdminPage_no_permission() {
		$this->expectOutputString( '' );
		Functions\when( 'child_themify_admin_ensure_write_permissions' )
			->justReturn( false );
		Functions\expect( 'wp_enqueue_script' )->never();
		child_themify_admin_page();
	}

	public function testAdminPage() {
		$this->expectOutputString( '<div id="ctfAppRoot"></div>' );
		Functions\when( 'child_themify_admin_ensure_write_permissions' )
			->justReturn( array( 'auth' => true ) );
		Functions\when( 'child_themify_js' )->justReturn( 'js' );
		Functions\when( 'child_themify_asset_version' )->justReturn( '1.0.0' );
		Functions\expect( 'wp_enqueue_script' )
			->once()
			->with( 'child-themify', 'js', array(), '1.0.0', true );
		Functions\expect( 'rest_url' )->once()->with( 'child-themify/v1' )->andReturn( 'rest_url' );
		Functions\expect( 'wp_create_nonce' )->once()->with( 'wp_rest' )->andReturn( 'nonce' );
		$themes = array(
			array( 'value' => 'twentyfifteen', 'label' => 'Twenty Fifteen' ),
			array( 'value' => 'twentysixteen', 'label' => 'Twenty Sixteen' ),
			array( 'value' => 'twentyseventeen', 'label' => 'Twenty Seventeen' ),
		);
		Functions\when( 'child_themify_get_parent_themes_for_js' )->justReturn( $themes );
		Functions\when( 'wp_get_current_user' )->justReturn( (object) array( 'display_name' => 'Test' ) );
		Functions\expect( 'wp_localize_script' )
			->once()
			->with( 'child-themify', 'ChildThemify', array(
				'rest'         => 'rest_url',
				'nonce'        => 'nonce',
				'themes'       => $themes,
				'current_user' => 'Test',
				'i18n'         => array(
					'header'             => 'Create a Child Theme',
					'theme_select_label' => 'Select a parent theme',
					'theme_placeholder'  => 'Select a theme...',
					'name_label'         => 'Name your child theme',
					'show_advanced'      => 'Show advanced fields',
					'hide_advanced'      => 'Hide advanced fields',
					'files_label'        => 'Extra Theme Files',
					'files_description'  => 'Select extra files that you want to copy into the child theme. style.css and functions.php are not in this list because they will always be created.',
					'select_all'         => 'Select All',
					'select_none'        => 'Select None',
					'author_label'       => 'Author Name',
					'invalid_theme'      => 'A theme %s already exists!',
					'create_button'      => array(
						'ready'   => 'Create Child Theme',
						'working' => 'Creating Your Child Theme...',
					),
					'success_message'    => 'Your theme has been created.',
					'success_link'       => 'Go check it out!',
					'errors'             => array(
						'server_msg' => 'Oops! Something went wrong! Here\'s the message we got: %s',
						'server_gen' => 'Oops! Something went wrong! Please try again later.',
						'user_msg'   => 'Looks like something was wrong with the info you provided. Here\'s the message we got: %s',
						'user_gen'   => 'Looks like something was wrong with the info you provided. Please make sure your info is correct and try again.',
					),
				),
				'creds'        => array( 'auth' => true ),
			) );
		child_themify_admin_page();
	}

}
