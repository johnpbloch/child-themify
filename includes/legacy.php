<?php

class CTF_Babymaker {

	/**
	 * Check the user's capabilities and validate the nonce
	 *
	 * Kills script execution if either of those tests fail
	 */
	public static function getTested() {
		$theme = empty( $_GET['theme'] ) ? '' : $_GET['theme'];
		if ( ! self::fertile() ) {
			wp_die( __( 'You do not have permission to do that!', 'child-themify' ) );
		}
		check_admin_referer( self::nonce_name( $theme ), '_ctf_nonce' );
	}

	protected static function fertile() {
		return current_user_can( 'install_themes' );
	}

	protected static function nonce( $theme ) {
		return wp_create_nonce( self::nonce_name( $theme ) );
	}

	protected static function nonce_name( $theme ) {
		return "child_themify_$theme";
	}

	public static function showInterface() {
		$theme = empty( $_GET['theme'] ) ? '' : $_GET['theme'];
		$theme = wp_get_theme( $theme );
		if ( self::checkCreds() ) {
			return;
		}
		settings_errors();
		?>
		<div class="wrap">
			<h2><?php echo esc_html( sprintf( _x( 'Create a child theme from %s', 'The placeholder is for a theme\'s name', 'child-themify' ), $theme->name ) ); ?></h2>

			<form method="post" action="<?php echo esc_url( self::getLink( $theme->get_stylesheet() ) ); ?>">
				<label><?php esc_html_e( 'Name your child theme', 'child-themify' ); ?></label><br>
				<input type="text" name="new_theme" />
				<?php submit_button( __( "Let's go!", 'child-themify' ) ); ?>
			</form>
		</div>
	<?php
	}

	protected static function checkCreds() {
		if ( empty( $_POST ) ) {
			return false;
		}
		self::getTested();
		$theme = empty( $_GET['theme'] ) ? '' : $_GET['theme'];
		$theme = wp_get_theme( $theme );
		$url   = self::getLink( $theme );
		if ( ( $creds = request_filesystem_credentials( $url, '', false, get_theme_root(), array( 'new_theme' ) ) ) === false ) {
			return true;
		}
		if ( ! WP_Filesystem( $creds, get_theme_root() ) ) {
			request_filesystem_credentials( $url, '', true, get_theme_root(), array( 'new_theme' ) );
			return true;
		}
		self::procreate( $_POST['new_theme'], $theme );
	}

	/**
	 * Get the link to create a child theme from a theme
	 *
	 * @param string $theme_name The template theme's directory
	 *
	 * @return string The url to create a child theme
	 */
	public static function getLink( $theme_name ) {
		$theme = wp_get_theme( $theme_name );
		// If the current user can't install a theme, the theme doesn't exist
		if ( ! self::fertile() || ! $theme->exists() || $theme->parent() ) {
			return '';
		}
		$args     = array(
			'action'     => 'child-themify',
			'theme'      => $theme_name,
			'_ctf_nonce' => self::nonce( $theme_name ),
		);
		$baseLink = is_multisite() ? network_admin_url( 'themes.php' ) : admin_url( 'themes.php' );
		return add_query_arg( $args, $baseLink );
	}

	/**
	 * Add the link for creating a child theme to the theme action links
	 *
	 * @param array           $links
	 * @param string|WP_Theme $theme
	 *
	 * @return array An array of action links
	 */
	public static function moodLighting( array $links, $theme ) {
		if ( ! ( $theme instanceof WP_Theme ) ) {
			$theme = wp_get_theme( $theme );
		}
		if ( ! self::fertile() || ! $theme->exists() || $theme->parent() ) {
			return $links;
		}
		$link                   = self::getLink( $theme->get_stylesheet() );
		$html                   = sprintf( "<a href=\"$link\">%s</a>", __( 'Create a child theme', 'child-themify' ) );
		$links['child-themify'] = $html;
		return $links;
	}

	/**
	 * Runs the actual child theme creation functionality
	 *
	 * @global WP_Filesystem_Base $wp_filesystem
	 *
	 * @param string              $new_theme
	 * @param WP_Theme            $template
	 *
	 * @throws Exception If the global filesystem object isn't available
	 */
	public static function procreate( $new_theme, WP_Theme $template ) {
		global $wp_filesystem;
		if ( ! ( $wp_filesystem instanceof WP_Filesystem_Base ) ) {
			if ( ! WP_Filesystem() ) {
				throw new Exception( __( 'Could not access the filesystem!', 'child-themify' ) );
			}
		}
		$oldStylesheet       = $template->get_stylesheet();
		$oldName             = $template->name;
		$new_theme_directory = trailingslashit( get_theme_root() ) . sanitize_file_name( strtolower( $new_theme ) );
		$wp_filesystem->mkdir( $new_theme_directory );
		$newStylesheet = trailingslashit( $new_theme_directory ) . 'style.css';
		$wp_filesystem->touch( $newStylesheet );
		$stylesheetContents = <<<EOF
/*
Theme Name: $new_theme
Version: 1.0
Description: A child theme of $oldName
Template: $oldStylesheet
*/

@import url("../$oldStylesheet/style.css");

EOF;
		$wp_filesystem->put_contents( $newStylesheet, $stylesheetContents );
		add_settings_error( '', 'child-themify', __( 'Your child theme was created successfully.', 'child-themify' ), 'updated' );
	}

	public static function load_themes_page() {
		if ( empty( $_GET['action'] ) || $_GET['action'] != 'child-themify' ) {
			if ( ! is_multisite() ) {
				add_action( 'admin_footer', array( 'CTF_Babymaker', 'link_current_theme' ) );
			}
			return;
		}
		require ABSPATH . 'wp-admin/admin-header.php';
		self::showInterface();
		require ABSPATH . 'wp-admin/admin-footer.php';
		exit;
	}

	public static function link_current_theme() {
		$theme    = wp_get_theme();
		$link     = self::getLink( $theme->get_stylesheet() );
		$filename = 'assets/js/legacy.';
		$filename .= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js' : 'min.js';
		wp_enqueue_script( 'child-themify', plugins_url( $filename, CTF_PATH ), array(), '1.0', true );
		wp_localize_script( 'child-themify', 'childThemify', array(
			'createAChildTheme' => __( 'Create a child theme', 'child-themify' ),
			'link'              => $link,
		) );
	}

	public static function init() {
		load_plugin_textdomain( 'child-themify', false, basename( dirname( CTF_PATH ) ) . '/languages' );
		add_filter( 'theme_action_links', array( 'CTF_Babymaker', 'moodLighting' ), 10, 2 );
		add_action( 'load-themes.php', array( 'CTF_Babymaker', 'load_themes_page' ) );
	}

}

