<?php

function child_themify_admin_init() {
	add_action( 'admin_menu', 'child_themify_admin_menu' );
}

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

function child_themify_admin_page_load() {
	wp_enqueue_style( 'child-themify', child_themify_css(), array(), child_themify_asset_version() );
}

function child_themify_admin_page() {
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
			'rest'   => rest_url( 'child-themify/v1' ),
			'nonce'  => wp_create_nonce( 'wp_rest' ),
			'themes' => child_themify_get_parent_themes_for_js(),
			'i18n'   => array(
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
			),
		)
	);
}
