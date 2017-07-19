<?php

function child_themify_asset( $type, $file ) {
	return esc_url_raw( sprintf(
		'%sassets/%s/%s',
		CTF_URL,
		( 'js' === $type ) ? 'js' : 'css',
		$file
	) );
}

function child_themify_js( $file = 'child-themify.js' ) {
	return child_themify_asset( 'js', $file );
}

function child_themify_css( $file = 'child-themify.css' ) {
	return child_themify_asset( 'css', $file );
}

function child_themify_asset_version() {
	return '{{VERSION}}' === CTF_VERSION ? time() : CTF_VERSION;
}
