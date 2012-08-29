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

	/**
	 * Runs the actual child theme creation functionality
	 * 
	 * @global WP_Filesystem_Base $wp_filesystem
	 * @param string $new_theme
	 * @param WP_Theme $template
	 * @throws Exception If the global filesystem object isn't available
	 */
	public static function procreate( $new_theme, WP_Theme $template ) {
		global $wp_filesystem;
		if ( !($wp_filesystem instanceof WP_Filesystem_Base) ) {
			if ( !WP_Filesystem() ) {
				throw new Exception( 'Could not access the filesystem!' );
			}
		}
		$oldStylesheet = $template->get_stylesheet();
		$oldName = $template->name;
		$new_theme_directory = trailingslashit( get_theme_root() ) . sanitize_file_name( $new_theme );
		$wp_filesystem->mkdir( $new_theme_directory );
		$newStylesheet = trailingslashit( $new_theme_directory ) . 'style.css';
		$wp_filesystem->touch( $newStylesheet );
		$stylesheetContents = <<<EOF
/*
Theme Name: $oldName Child
Version: 1.0
Description: A child theme of $oldName
Template: $oldStylesheet
*/

@import url("../$oldStylesheet/style.css")

EOF;
		$wp_filesystem->put_contents( $newStylesheet, $stylesheetContents );
	}

}
