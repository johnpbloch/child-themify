<?php

function ctf_do_analytics() {
	require dirname( CTF_PATH ) . '/includes/analytics.php';
	$analytics = new CTF_Analytics();
	$analytics->run();
}

add_action( 'plugins_loaded', 'ctf_do_analytics', 9 );
