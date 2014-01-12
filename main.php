<?php

define( 'CTF_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'CTF_URL', WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'CTF_VERSION', '%%VERSION%%' );

function ctf_plugins_loaded() {
	if ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) {
		global $child_themify;
		require_once dirname( __FILE__ ) . '/plugin.php';
		$child_themify = new Child_Themify();
		add_action( 'init', array( $child_themify, 'init' ) );
	} else {
		require_once dirname( __FILE__ ) . '/legacy.php';
		add_action( 'init', array( 'CTF_Babymaker', 'init' ) );
	}
}

add_action( 'plugins_loaded', 'ctf_plugins_loaded' );
