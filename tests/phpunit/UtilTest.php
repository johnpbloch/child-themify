<?php

namespace ChildThemify;

use Brain\Monkey\Functions;

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

}
