<?php

/**
 * API setup
 */
function child_themify_api_init() {
	add_action( 'rest_api_init', 'child_themify_api_endpoints' );
}

/**
 * Register custom endpoints
 *
 * @param WP_REST_Server $server
 */
function child_themify_api_endpoints( $server ) {
	register_rest_route( 'child-themify/v1', 'theme-data/(?P<theme>.+)', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => 'child_themify_api_get_theme_files',
		'args'                => array(
			'theme' => array(
				'sanitize_callback' => 'child_themify_api_sanitize_theme',
			),
		),
		'permission_callback' => 'child_themify_api_permissions',
	) );
	register_rest_route( 'child-themify/v1', 'create-theme', array(
		'methods'             => WP_REST_Server::CREATABLE,
		'callback'            => 'child_themify_api_create_theme',
		'args'                => array(
			'slug'        => array(
				'sanitize_callback' => 'sanitize_file_name',
				'validate_callback' => 'child_themify_validate_slug',
				'required'          => true,
			),
			'parent'      => array(
				'sanitize_callback' => 'child_themify_api_sanitize_theme',
				'validate_callback' => 'child_themify_api_validate_parent',
				'required'          => true,
			),
			'name'        => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
			'author'      => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
			'extra_files' => array(
				'sanitize_callback' => 'child_themify_sanitize_extra_files',
				'default'           => array(),
			),
			'creds'       => array(
				'default' => array(),
			)
		),
		'permission_callback' => 'child_themify_api_permissions',
	) );
}

/**
 * Get a list of theme files for the React app
 *
 * @param WP_REST_Request $request
 *
 * @return WP_Error|WP_REST_Response
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
	$theme     = $request['theme'];
	$raw_files = $theme->get_files( array( 'php', 'inc', 'css', 'js', 'json' ), 1 );
	$files     = array();
	foreach ( $raw_files as $name => $path ) {
		if ( in_array( $name, array( 'functions.php', 'style.css' ) ) ) {
			continue;
		}
		$files[ $name ] = $path;
	}

	return rest_ensure_response( array( 'files' => $files ) );
}

/**
 * Turn a slug into a theme object if the theme exists
 *
 * @param $maybe_theme
 *
 * @return null|WP_Theme
 */
function child_themify_api_sanitize_theme( $maybe_theme ) {
	$theme = wp_get_theme( $maybe_theme );

	return $theme->exists() ? $theme : null;
}

/**
 * Create a child theme
 *
 * @param WP_REST_Request $request
 *
 * @return WP_Error|WP_REST_Response
 */
function child_themify_api_create_theme( $request ) {
	$parent = $request['parent'];
	$parent = $parent instanceof WP_Theme ? $parent : wp_get_theme( $parent );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	try {
		child_themify_create_theme(
			$request['slug'],
			$parent,
			$request['name'] ? $request['name'] : $request['slug'],
			$request['author'],
			$request['extra_files'],
			$request['creds']
		);
	} catch ( Exception $e ) {
		$status = $e->getCode() === 1 ? 400 : 500;
		$codes  = array(
			1 => 'ctf_no_fs',
			2 => 'ctf-no-create-dir',
			3 => 'ctf-no-create-stylesheet',
			4 => 'ctf-no-write-stylesheet',
			5 => 'ctf-no-create-functions',
		);
		$code   = empty( $codes[ $e->getCode() ] ) ? 'ctf-error' : $codes[ $e->getCode() ];

		return new WP_Error( $code, $e->getMessage(), compact( 'status' ) );
	}

	$response = rest_ensure_response( array( 'success' => true ) );
	$response->set_status( 201 );

	return $response;
}

/**
 * Validate the child slug by making sure it doesn't already exist
 *
 * @param $slug
 *
 * @return bool
 */
function child_themify_validate_slug( $slug ) {
	$theme = wp_get_theme( $slug );

	return ! $theme->exists();
}

/**
 * Validate a parent by making sure it exists and isn't a child theme
 *
 * @param $parent
 *
 * @return bool
 */
function child_themify_api_validate_parent( $parent ) {
	if ( ! ( $parent instanceof WP_Theme ) ) {
		$parent = wp_get_theme( $parent );
	}

	return $parent->exists() && ! $parent->parent();
}

/**
 * Run the array of extra files through sanitize_text_field
 *
 * @param string[] $files
 *
 * @return array
 */
function child_themify_sanitize_extra_files( $files ) {
	$files = (array) $files;

	return array_map( 'sanitize_text_field', $files );
}

/**
 * Check whether the current user can use the plugin's api endpoints
 *
 * @return bool
 */
function child_themify_api_permissions() {
	return current_user_can( 'install_themes' );
}
