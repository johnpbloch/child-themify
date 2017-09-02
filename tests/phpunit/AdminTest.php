<?php

namespace ChildThemify;

class AdminTest extends TestCase {

	public function testSetup() {
		child_themify_admin_init();
		$this->assertTrue( has_action( 'admin_menu', 'child_themify_admin_menu' ) );
	}

}
