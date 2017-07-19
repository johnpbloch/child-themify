<?php

function child_themify_api_init() {
	add_action( 'rest_api_init', 'child_themify_api_endpoints' );
}

/**
 * @param WP_REST_Server $server
 */
function child_themify_api_endpoints( $server ) {
	register_rest_route( 'child-themify/v1', 'theme-files/(?P<theme>.+)', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'child_themify_api_get_theme_files',
		'args'                => array(
			'theme' => array(
				'sanitize_callback' => 'child_themify_api_sanitize_theme',
			),
		),
		'permission_callback' => 'child_themify_api_permissions',
	) );
}

/**
 * @param WP_REST_Request $request
 */
function child_themify_api_get_theme_files( $request ) {
	if ( ! $request['theme'] || ! $request['theme'] instanceof WP_Theme ) {
		return new WP_Error(
			'ctf_invalid_theme',
			esc_html__( 'That theme does not exist', 'child-themify' ),
			array( 'status' => 404 )
		);
	}
	/** @var WP_Theme $theme */
	$theme = $request['theme'];
	$files = $theme->get_files( null, 1 );
	if ( isset( $files['functions.php'] ) ) {
		unset( $files['functions.php'] );
	}
	if ( isset( $files['style.css'] ) ) {
		unset( $files['style.css'] );
	}

	return rest_ensure_response( array( 'files' => $files ) );
}

function child_themify_api_sanitize_theme( $maybe_theme ) {
	$theme = wp_get_theme( $maybe_theme );

	return $theme->exists() ? $theme : null;
}

function child_themify_api_permissions() {
	return current_user_can( 'install_themes' );
}
