<?php

/**
 * Admin setup
 */
function child_themify_admin_init() {
	$action = 'admin_menu';
	if ( is_multisite() ) {
		$action = "network_$action";
	}
	add_action( $action, 'child_themify_admin_menu' );
}

/**
 * Create the admin menu page and hook into the loader to enqueue styles
 */
function child_themify_admin_menu() {
	$hook = add_theme_page(
		esc_html__( 'Create Child Theme', 'child-themify' ),
		esc_html__( 'Create Child Theme', 'child-themify' ),
		'install_themes',
		'child_themify',
		'child_themify_admin_page'
	);
	add_action( "load-$hook", 'child_themify_admin_page_load' );
}

/**
 * Enqueue the stylesheet
 */
function child_themify_admin_page_load() {
	wp_enqueue_style( 'child-themify', child_themify_css(), array(), child_themify_asset_version() );
}

/**
 * Render the admin page
 */
function child_themify_admin_page() {
	$permissions = child_themify_admin_ensure_write_permissions();
	if ( empty( $permissions ) ) {
		return;
	}
	?>
	<div id="ctfAppRoot"></div>
	<?php
	wp_enqueue_script(
		'child-themify',
		child_themify_js(),
		array(),
		child_themify_asset_version(),
		true
	);
	wp_localize_script(
		'child-themify',
		'ChildThemify',
		array(
			'rest'         => rest_url( 'child-themify/v1' ),
			'nonce'        => wp_create_nonce( 'wp_rest' ),
			'themes'       => child_themify_get_parent_themes_for_js(),
			'current_user' => wp_get_current_user()->display_name,
			'i18n'         => array(
				'header'             => esc_html__( 'Create a Child Theme', 'child-themify' ),
				'theme_select_label' => esc_html__( 'Select a parent theme', 'child-themify' ),
				'theme_placeholder'  => esc_html__( 'Select a theme...', 'child-themify' ),
				'name_label'         => esc_html__( 'Name your child theme', 'child-themify' ),
				'show_advanced'      => esc_html__( 'Show advanced fields', 'child-themify' ),
				'hide_advanced'      => esc_html__( 'Hide advanced fields', 'child-themify' ),
				'files_label'        => esc_html__( 'Extra Theme Files', 'child-themify' ),
				'files_description'  => esc_html__( 'Select extra files that you want to copy into the child theme. style.css and functions.php are not in this list because they will always be created.', 'child-themify' ),
				'select_all'         => esc_html__( 'Select All', 'child-themify' ),
				'select_none'        => esc_html__( 'Select None', 'child-themify' ),
				'author_label'       => esc_html__( 'Author Name', 'child-themify' ),
				'invalid_theme'      => esc_html__( 'A theme %s already exists!', 'child-themify' ),
				'create_button'      => array(
					'ready'   => esc_html__( 'Create Child Theme', 'child-themify' ),
					'working' => esc_html__( 'Creating Your Child Theme...', 'child-themify' ),
				),
				'success_message'    => esc_html__( 'Your theme has been created.', 'child-themify' ),
				/* translators: "it" in this sentence refers to the child theme just created */
				'success_link'       => esc_html__( 'Go check it out!', 'child-themify' ),
				'errors'             => array(
					/* translators: the placeholder is an error message from the API (translated elsewhere) */
					'server_msg' => esc_html__( 'Oops! Something went wrong! Here\'s the message we got: %s', 'child-themify' ),
					'server_gen' => esc_html__( 'Oops! Something went wrong! Please try again later.', 'child-themify' ),
					/* translators: the placeholder is an error message from the API (translated elsewhere) */
					'user_msg'   => esc_html__( 'Looks like something was wrong with the info you provided. Here\'s the message we got: %s', 'child-themify' ),
					'user_gen'   => esc_html__( 'Looks like something was wrong with the info you provided. Please make sure your info is correct and try again.', 'child-themify' ),
				),
			),
			'creds'        => $permissions,
		)
	);
}

/**
 * Make sure we can actually write to the filesystem before creating a theme
 *
 * If necessary, this will present a form to the user to collect credentials.
 *
 * @return mixed
 */
function child_themify_admin_ensure_write_permissions() {
	ob_start();
	$creds  = request_filesystem_credentials(
		child_themify_get_admin_page(),
		'',
		false,
		get_theme_root()
	);
	$output = ob_get_clean();
	if ( $creds !== false ) {
		$creds = is_array( $creds ) ? $creds : array();
		if ( child_themify_get_fs_object( $creds ) ) {
			return $creds + array( 'auth' => true );
		}
		ob_start();
		request_filesystem_credentials(
			child_themify_get_admin_page(),
			'',
			true,
			get_theme_root()
		);
		$output = ob_get_clean();
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Before we get started...', 'child-themify' ); ?></h1>
		<p><?php esc_html_e( 'Before we can create a child theme, it looks like you\'ll need to enter your credentials to create files on your hosting account.', 'child-themify' ); ?></p>
		<?php echo $output; ?>
	</div>
	<?php
	return false;
}
