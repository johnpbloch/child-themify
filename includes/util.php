<?php

/**
 * Generic function to get an asset from the plugin
 *
 * @param string $type
 * @param string $file
 *
 * @return string
 */
function child_themify_asset( $type, $file ) {
	return esc_url_raw( sprintf(
		'%sassets/%s/%s',
		CTF_URL,
		( 'js' === $type ) ? 'js' : 'css',
		$file
	) );
}

/**
 * Get the URL for the plugin javascript
 *
 * @param string $file
 *
 * @return string
 */
function child_themify_js( $file = 'child-themify.js' ) {
	return child_themify_asset( 'js', $file );
}

/**
 * Get the URL for the plugin styles
 *
 * @param string $file
 *
 * @return string
 */
function child_themify_css( $file = 'child-themify.css' ) {
	return child_themify_asset( 'css', $file );
}

/**
 * Get the current version of the plugin
 *
 * Uses a unix timestamp for the development version of the plugin
 *
 * @return int|string
 */
function child_themify_asset_version() {
	return '{{VERSION}}' === CTF_VERSION ? time() : CTF_VERSION;
}

/**
 * Get a list of themes that are eligible to be child-themed
 *
 * @return array
 */
function child_themify_get_parent_themes_for_js() {
	$all_themes = wp_get_themes();
	$themes     = array();
	foreach ( $all_themes as $theme ) {
		/** @var WP_Theme $theme */
		if ( $theme->parent() ) {
			continue;
		}
		$themes[] = array(
			'value' => $theme->get_stylesheet(),
			'label' => $theme->get( 'Name' ),
		);
	}

	return $themes;
}

/**
 * Get the URL for the CTF admin page
 *
 * @return string
 */
function child_themify_get_admin_page() {
	return add_query_arg(
		array( 'page' => 'child_themify' ),
		is_multisite() ? network_admin_url( 'themes.php' ) : admin_url( 'themes.php' )
	);
}

/**
 * Get filesystem abstraction object if available
 *
 * @param array $creds
 *
 * @return bool|WP_Filesystem_Base False if unavailable
 */
function child_themify_get_fs_object( $creds ) {
	if ( WP_Filesystem( $creds, get_theme_root() ) ) {
		$obj = $GLOBALS['wp_filesystem'];
		if ( $obj instanceof WP_Filesystem_Base ) {
			return $obj;
		}
	}

	return false;
}
