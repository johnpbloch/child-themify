<?php

function ctf_plugins_loaded() {
	if ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) {
		global $child_themify;
		require_once dirname( CTF_PATH ) . '/includes/plugin.php';
		$child_themify = new Child_Themify();
		add_action( 'init', array( $child_themify, 'init' ) );
	} else {
		require_once dirname( CTF_PATH ) . '/includes/legacy.php';
		add_action( 'init', array( 'CTF_Babymaker', 'init' ) );
	}
}

add_action( 'plugins_loaded', 'ctf_plugins_loaded' );
