<?php

namespace ChildThemify;

use Brain\Monkey\Functions;
use Mockery;

class UtilTest extends TestCase {

	public function test_child_themify_js() {
		Functions\when( 'esc_url_raw' )->returnArg( 1 );
		$this->assertEquals(
			CTF_URL . 'assets/js/child-themify.js',
			child_themify_js()
		);
		$file = 'random' . rand( 0, 99 ) . '.js';
		$this->assertEquals(
			CTF_URL . 'assets/js/' . $file,
			child_themify_js( $file )
		);
	}

	public function test_child_themify_css() {
		Functions\when( 'esc_url_raw' )->returnArg( 1 );
		$this->assertEquals(
			CTF_URL . 'assets/css/child-themify.css',
			child_themify_css()
		);
		$file = 'random' . rand( 0, 99 ) . '.css';
		$this->assertEquals(
			CTF_URL . 'assets/css/' . $file,
			child_themify_css( $file )
		);
	}

	public function test_get_parent_themes_for_js() {
		$theme1 = Mockery::mock( 'WP_Theme' );
		$theme2 = Mockery::mock( 'WP_Theme' );
		$theme3 = Mockery::mock( 'WP_Theme' );
		$theme1->shouldReceive( 'parent' )->once()->andReturn( false );
		$theme2->shouldReceive( 'parent' )->once()->andReturn( $theme1 );
		$theme3->shouldReceive( 'parent' )->once()->andReturn( false );
		$theme1->shouldReceive( 'get_stylesheet' )->once()->andReturn( 'theme1' );
		$theme2->shouldReceive( 'get_stylesheet' )->never();
		$theme3->shouldReceive( 'get_stylesheet' )->once()->andReturn( 'theme3' );
		$theme1->shouldReceive( 'get' )->once()->with( 'Name' )->andReturn( 'Theme 1' );
		$theme2->shouldReceive( 'get' )->never();
		$theme3->shouldReceive( 'get' )->once()->with( 'Name' )->andReturn( 'Theme 3' );
		Functions\when( 'wp_get_themes' )->justReturn( array( $theme1, $theme2, $theme3 ) );

		$this->assertEquals(
			array(
				array( 'value' => 'theme1', 'label' => 'Theme 1' ),
				array( 'value' => 'theme3', 'label' => 'Theme 3' ),
			),
			child_themify_get_parent_themes_for_js()
		);
	}

	public function test_get_admin_page() {
		Functions\when( 'add_query_arg' )->alias( function ( $args, $url ) {
			return $url . '?' . http_build_query( $args );
		} );
		Functions\expect( 'is_multisite' )->twice()->andReturn( false, true );
		Functions\expect( 'admin_url' )
			->once()
			->with( 'themes.php' )
			->andReturn( 'http://test.ctf.dev/wp-admin/themes.php' );
		Functions\expect( 'network_admin_url' )
			->once()
			->with( 'themes.php' )
			->andReturn( 'http://test.ctf.dev/wp-admin/network/themes.php' );

		$this->assertEquals(
			'http://test.ctf.dev/wp-admin/themes.php?page=child_themify',
			child_themify_get_admin_page()
		);
		$this->assertEquals(
			'http://test.ctf.dev/wp-admin/network/themes.php?page=child_themify',
			child_themify_get_admin_page()
		);
	}

}
