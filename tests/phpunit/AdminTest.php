<?php

namespace ChildThemify;

use Brain\Monkey\Functions;

class AdminTest extends TestCase {

	public function testSetup() {
		child_themify_admin_init();
		$this->assertTrue( has_action( 'admin_menu', 'child_themify_admin_menu' ) );
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

}
