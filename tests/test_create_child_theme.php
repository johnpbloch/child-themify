<?php

class WP_Test_Create_Child_Theme extends WP_UnitTestCase {

	/**
	 * @var WP_Theme Theme object
	 */
	protected $theme;

	public function setUp() {
		parent::setUp();
	}

	public function test_create_child_theme() {
		$current_theme = wp_get_theme();
		$random = wp_generate_password( 6, false );
		$new_theme_name = basename( $current_theme->stylesheet_dir ) . '_' . $random;
		$this->assertFalse( $current_theme->parent() );
		CTF_BabyMaker::procreate( $new_theme_name, $current_theme );
		$this->theme = $new_theme = wp_get_theme( $new_theme_name );
		$this->assertTrue( $new_theme->exists() );
		$this->assertEquals( $new_theme->parent(), $current_theme );
		$files = $new_theme->get_files();
		$this->assertEquals( count( $files ), 1 );
	}

	public function tearDown() {
		$themeDir = $this->theme->get_stylesheet_directory();
		`rm -r $themeDir`;
		parent::tearDown();
	}

}
