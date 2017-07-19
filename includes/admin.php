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
			'themes' => child_themify_get_parent_themes_for_js(),
			'i18n'   => array(
				'header'             => esc_html__( 'Create a Child Theme', 'child-themify' ),
				'theme_select_label' => esc_html__( 'Select a parent theme', 'child-themify' ),
				'theme_placeholder'  => esc_html__( 'Select a theme...', 'child-themify' ),
				'name_label'         => esc_html__( 'Name your child theme', 'child-themify' ),
			),
		)
	);
}
