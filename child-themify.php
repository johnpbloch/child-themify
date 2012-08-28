<?php

/*
 * Plugin Name: Child Themify
 * Description: Enables the quick creation of child themes from any non-child theme you have installed.
 * Version: 0.1-alpha
 */

class CTF_Babymaker {

	public static function getTested() {

	}

	public static function showInterface() {

	}

	public static function getLink( $theme_name ) {
		$theme = wp_get_theme( $theme_name );
		// If the current user can't install a theme, the theme doesn't exist
		if ( !current_user_can( 'install_themes' ) || !$theme->exists() || $theme->parent() ) {
			return '';
		}
		$args = array(
			'action' => 'child-themify',
			'theme' => $theme_name,
			'_ctf_nonce' => wp_create_nonce( "child_themify_$theme_name" ),
		);
		$baseLink = is_multisite() ? network_admin_url( 'themes.php' ) : admin_url( 'themes.php' );
		return add_query_arg( $args, $baseLink );
	}

	public static function moodLighting( array $links, $theme ) {
		if ( !($theme instanceof WP_Theme) ) {
			$theme = wp_get_theme( $theme );
		}
		if ( !current_user_can( 'install_themes' ) || !$theme->exists() || $theme->parent() ) {
			return $links;
		}
		$link = self::getLink( $theme->get_stylesheet() );
		$html = "<a href=\"$link\">Create a child theme</a>";
		$links['child-themify'] = $html;
		return $links;
	}

	public static function procreate() {

	}

}
