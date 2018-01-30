<?php

namespace ChildThemify;

use Brain\Monkey\Functions;
use Mockery;
use stdClass;

class UtilTest extends TestCase {

	public function test_child_themify_js() {
		Functions\when( 'esc_url_raw' )->returnArg( 1 );
		$this->assertEquals(
			JPB_CTF_URL . 'assets/js/child-themify.js',
			child_themify_js()
		);
		$file = 'random' . rand( 0, 99 ) . '.js';
		$this->assertEquals(
			JPB_CTF_URL . 'assets/js/' . $file,
			child_themify_js( $file )
		);
	}

	public function test_child_themify_css() {
		Functions\when( 'esc_url_raw' )->returnArg( 1 );
		$this->assertEquals(
			JPB_CTF_URL . 'assets/css/child-themify.css',
			child_themify_css()
		);
		$file = 'random' . rand( 0, 99 ) . '.css';
		$this->assertEquals(
			JPB_CTF_URL . 'assets/css/' . $file,
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

	public function test_get_fs_object_failure() {
		Functions\when( 'get_theme_root' )->justReturn( '/srv/www/html/wp-content/themes' );
		Functions\when( 'WP_Filesystem' )->justReturn( false );

		$this->assertFalse( child_themify_get_fs_object( array() ) );
	}

	public function test_get_fs_object_bad_global() {
		global $wp_filesystem;
		$wp_filesystem = new stdClass();
		Functions\when( 'get_theme_root' )->justReturn( '/srv/www/html/wp-content/themes' );
		Functions\when( 'WP_Filesystem' )->justReturn( true );

		$this->assertFalse( child_themify_get_fs_object( array() ) );
	}

	public function test_get_fs_object() {
		global $wp_filesystem;
		$fs = $wp_filesystem = Mockery::mock( 'WP_Filesystem_Base' );
		Functions\when( 'get_theme_root' )->justReturn( '/srv/www/html/wp-content/themes' );
		$creds = array(
			'username' => 'foobar',
			'password' => 'bazbat',
		);
		Functions\expect( 'WP_Filesystem' )
			->once()
			->with( $creds, '/srv/www/html/wp-content/themes' )
			->andReturn( true );

		$this->assertSame( $fs, child_themify_get_fs_object( $creds ) );
	}

	/**
	 * @dataProvider data_relative_url
	 */
	public function test_relative_url( $from, $to, $expected ) {
		$this->assertEquals( $expected, child_themify_relative_url( $from, $to ) );
	}

	public function data_relative_url() {
		return array(
			array(
				'http://foo.bar/path/to/first/file.txt',
				'http://foo.bar/path/to/second/file.txt',
				'../second/file.txt',
			),
			array(
				'http://foo.bar/path/to/dir',
				'http://foo.bar/path/to/second/dir/',
				'./second/dir',
			),
			array(
				'http://foo.bar/path/to/dir/',
				'http://foo.bar/path/to/second/dir/',
				'../second/dir',
			),
		);
	}

	public function test_mkdir_p_already_exists() {
		$fs  = Mockery::mock( 'WP_Filesystem_Base' );
		$dir = '/srv/www/wp-content/themes/test-theme';
		$fs->shouldReceive( 'exists' )->once()->with( $dir )->andReturn( true );
		$fs->shouldReceive( 'mkdir' )->never();
		/** @var \WP_Filesystem_Base $fs */
		$this->assertTrue( child_themify_mkdir_p( $fs, $dir ) );
		$this->assertConditionsMet();
	}

	public function test_mkdir_p_failure_condition() {
		$fs  = Mockery::mock( 'WP_Filesystem_Base' );
		$dir = '/srv/www/wp-content/themes/test-theme/test';
		$fs->shouldReceive( 'exists' )->once()->with( $dir )->andReturn( false );
		$fs->shouldReceive( 'exists' )->once()->with( dirname( $dir ) )->andReturn( false );
		$fs->shouldReceive( 'exists' )->once()->with( dirname( dirname( $dir ) ) )->andReturn( true );
		$fs->shouldReceive( 'mkdir' )->with( dirname( $dir ) )->once()->andReturn( false );
		$fs->shouldReceive( 'mkdir' )->with( $dir )->never();
		/** @var \WP_Filesystem_Base $fs */
		$this->assertFalse( child_themify_mkdir_p( $fs, $dir ) );
		$this->assertConditionsMet();
	}

	public function test_mkdir_p() {
		$fs  = Mockery::mock( 'WP_Filesystem_Base' );
		$dir = '/srv/www/wp-content/themes/test-theme/test';
		$fs->shouldReceive( 'exists' )->once()->with( $dir )->andReturn( false );
		$fs->shouldReceive( 'exists' )->once()->with( dirname( $dir ) )->andReturn( false );
		$fs->shouldReceive( 'exists' )->once()->with( dirname( dirname( $dir ) ) )->andReturn( true );
		$fs->shouldReceive( 'mkdir' )->with( dirname( $dir ) )->once()->andReturn( true );
		$fs->shouldReceive( 'mkdir' )->with( $dir )->once()->andReturn( true );
		/** @var \WP_Filesystem_Base $fs */
		$this->assertTrue( child_themify_mkdir_p( $fs, $dir ) );
		$this->assertConditionsMet();
	}

}
