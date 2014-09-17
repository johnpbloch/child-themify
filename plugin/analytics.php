<?php

function ctf_do_analytics() {
	$hash = md5( $GLOBALS['wp_version'] . '|' . PHP_VERSION );
	if ( ! is_admin() || $hash === get_option( 'ctf_analytics_hash' ) ) {
		return;
	}
	require dirname( CTF_PATH ) . '/includes/analytics.php';
	$analytics = new CTF_Analytics( $hash );
	add_action( 'shutdown', array( $analytics, 'shutdown' ) );
}

add_action( 'plugins_loaded', 'ctf_do_analytics', 9 );
