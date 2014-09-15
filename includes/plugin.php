<?php

class Child_Themify {

	/**
	 * Check the user's capabilities and validate the nonce
	 *
	 * Kills script execution if either of those tests fail
	 *
	 * @param WP_Theme $theme
	 */
	public function isActionAllowed( WP_Theme $theme ) {
		if ( ! $this->checkCapability() ) {
			wp_die( __( 'You do not have permission to do that!', 'child-themify' ) );
		}
		check_admin_referer( $this->nonceName( $theme ), '_ctf_nonce' );
	}

	public function checkCapability() {
		return current_user_can( 'install_themes' );
	}

	/**
	 * @param WP_Theme $theme
	 *
	 * @return bool
	 */
	public function isValidTheme( WP_Theme $theme ) {
		return ( $this->checkCapability() && $theme->exists() && ! $theme->parent() );
	}

	/**
	 * @param WP_Theme $theme
	 *
	 * @return string
	 */
	public function nonce( WP_Theme $theme = null ) {
		return wp_create_nonce( $this->nonceName( $theme ) );
	}

	/**
	 * @param WP_Theme $theme
	 *
	 * @return string
	 */
	public function nonceName( WP_Theme $theme = null ) {
		$nonce_name = 'child_themify';
		if ( $theme ) {
			$nonce_name .= '_' . $theme->get_stylesheet();
		}
		return $nonce_name;
	}

	public function showInterface() {
		$theme = empty( $_GET['theme'] ) ? '' : $_GET['theme'];
		$theme = wp_get_theme( $theme );
		if ( $this->checkCreds( $theme ) ) {
			return;
		}
		$this->display( $theme );
	}

	/**
	 * @param WP_Theme $theme
	 */
	public function display( WP_Theme $theme ) {
		settings_errors();
		?>
		<div class="wrap">
			<h2><?php echo esc_html( sprintf( _x( 'Create a child theme from %s', 'The placeholder is for a theme\'s name', 'child-themify' ), $theme->name ) ); ?></h2>

			<form method="post" action="<?php echo esc_url( $this->getLink( $theme ) ); ?>">
				<label><?php esc_html_e( 'Name your child theme', 'child-themify' ); ?></label><br>
				<input type="text" name="new_theme" />
				<?php submit_button( __( "Let's go!", 'child-themify' ) ); ?>
			</form>
		</div>
		<?php
		// End of Display
	}

	/**
	 * @param WP_Theme $theme
	 *
	 * @return bool
	 */
	public function checkCreds( WP_Theme $theme ) {
		if ( empty( $_POST ) ) {
			return false;
		}
		$this->isActionAllowed( $theme );

		$url = $this->getLink( $theme );
		if ( ( $creds = request_filesystem_credentials( $url, '', false, get_theme_root(), array( 'new_theme' ) ) ) === false ) {
			return true;
		}
		if ( ! WP_Filesystem( $creds, get_theme_root() ) ) {
			request_filesystem_credentials( $url, '', true, get_theme_root(), array( 'new_theme' ) );
			return true;
		}
		$this->create( $_POST['new_theme'], $theme );
	}

	/**
	 * Get the link to create a child theme from a theme
	 *
	 * @param WP_Theme $theme The template theme's directory
	 *
	 * @return string The url to create a child theme
	 */
	public function getLink( WP_Theme $theme ) {
		// If the current user can't install a theme, the theme doesn't exist
		if ( ! $this->isValidTheme( $theme ) ) {
			return '';
		}

		$args = array(
			'action'     => 'child-themify',
			'theme'      => $theme->get_stylesheet(),
			'_ctf_nonce' => $this->nonce( $theme ),
		);
		return add_query_arg( $args, $this->getBaseLink() );
	}

	/**
	 * Get the base link for the correct themes.php page
	 *
	 * If the site is a multisite network, it will use the network admin.
	 * Otherwise it'll just use the normal admin.
	 *
	 * @return string
	 */
	public function getBaseLink() {
		return is_multisite() ? network_admin_url( 'themes.php' ) : admin_url( 'themes.php' );
	}

	/**
	 * Add the link for creating a child theme to the theme action links
	 *
	 * @param array           $links
	 * @param string|WP_Theme $theme
	 *
	 * @return array An array of action links
	 */
	public function addActionLink( array $links, $theme ) {
		if ( ! ( $theme instanceof WP_Theme ) ) {
			$theme = wp_get_theme( $theme );
		}
		if ( ! $this->isValidTheme( $theme ) ) {
			return $links;
		}
		$link = $this->getLink( $theme );
		$html = sprintf( "<a href=\"$link\">%s</a>", __( 'Create a child theme', 'child-themify' ) );

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
	public function create( $new_theme, WP_Theme $template ) {
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

	public function loadThemesPage() {
		if ( $this->isChildThemifyPage() ) {
			$this->loadFile( ABSPATH . 'wp-admin/admin-header.php' );
			$this->showInterface();
			$this->loadFile( ABSPATH . 'wp-admin/admin-footer.php' );
			exit;
		}
	}

	public function loadFile( $file ) {
		require $file;
	}

	public function isChildThemifyPage() {
		return ( ! empty( $_GET['action'] ) && $_GET['action'] === 'child-themify' );
	}

	public function init() {
		load_plugin_textdomain( 'child-themify', false, basename( dirname( __FILE__ ) ) . '/languages' );
		add_filter( 'theme_action_links', array( $this, 'addActionLink' ), 10, 2 );
		add_action( 'load-themes.php', array( $this, 'loadThemesPage' ) );
		if ( version_compare( $GLOBALS['wp_version'], '4.1.9', '<' ) ) {
			add_action( 'admin_footer-themes.php', array( $this, 'override_tmpl_theme_single' ) );
		}
		add_action( 'tmpl-theme-single_actions', array( $this, 'tmpl_theme_single_actions' ) );
		add_filter( 'wp_prepare_themes_for_js', array( $this, 'prepare_themes' ) );
	}

	public function override_tmpl_theme_single() {
		?>
		<script>
			var tts = document.getElementById('tmpl-theme-single');
			if (tts.parentNode) {
				tts.parentNode.removeChild(tts);
			}
		</script>
		<script id="tmpl-theme-single" type="text/template">
			<div class="theme-backdrop"></div>
			<div class="theme-wrap">
				<div class="theme-header">
					<button class="left dashicons dashicons-no"><span class="screen-reader-text"><?php _e( 'Show previous theme' ); ?></span></button>
					<button class="right dashicons dashicons-no"><span class="screen-reader-text"><?php _e( 'Show next theme' ); ?></span></button>
					<button class="close dashicons dashicons-no"><span class="screen-reader-text"><?php _e( 'Close overlay' ); ?></span></button>
				</div>
				<div class="theme-about">
					<div class="theme-screenshots">
					<# if ( data.screenshot[0] ) { #>
						<div class="screenshot">
							<img src="{{ data.screenshot[0] }}" alt="" />
						</div>
					<# } else { #>
						<div class="screenshot blank"></div>
					<# } #>
					</div>

					<div class="theme-info">
						<# if ( data.active ) { #>
							<span class="current-label"><?php _e( 'Current Theme' ); ?></span>
						<# } #>
						<h3 class="theme-name">{{{ data.name }}}<span class="theme-version"><?php printf( __( 'Version: %s' ), '{{{ data.version }}}' ); ?></span></h3>
						<h4 class="theme-author"><?php printf( __( 'By %s' ), '{{{ data.authorAndUri }}}' ); ?></h4>

						<# if ( data.hasUpdate ) { #>
						<div class="theme-update-message">
							<h4 class="theme-update"><?php _e( 'Update Available' ); ?></h4>
							{{{ data.update }}}
						</div>
						<# } #>
						<p class="theme-description">{{{ data.description }}}</p>

						<# if ( data.parent ) { #>
							<p class="parent-theme"><?php printf( __( 'This is a child theme of %s.' ), '<strong>{{{ data.parent }}}</strong>' ); ?></p>
						<# } #>

						<# if ( data.tags ) { #>
							<p class="theme-tags"><span><?php _e( 'Tags:' ); ?></span> {{{ data.tags }}}</p>
						<# } #>
					</div>
				</div>

				<div class="theme-actions">
					<div class="active-theme">
						<a href="{{{ data.actions.customize }}}" class="button button-primary customize load-customize hide-if-no-customize"><?php _e( 'Customize' ); ?></a>
						<?php echo implode( ' ', $GLOBALS['current_theme_actions'] ); ?>
						<?php do_action( 'tmpl-theme-single_actions', 'active' ); ?>
					</div>
					<div class="inactive-theme">
						<# if ( data.actions.activate ) { #>
							<a href="{{{ data.actions.activate }}}" class="button button-primary activate"><?php _e( 'Activate' ); ?></a>
						<# } #>
						<a href="{{{ data.actions.customize }}}" class="button button-secondary load-customize hide-if-no-customize"><?php _e( 'Live Preview' ); ?></a>
						<a href="{{{ data.actions.preview }}}" class="button button-secondary hide-if-customize"><?php _e( 'Preview' ); ?></a>
						<?php do_action( 'tmpl-theme-single_actions', 'inactive' ); ?>
					</div>

					<# if ( ! data.active && data.actions['delete'] ) { #>
						<a href="{{{ data.actions['delete'] }}}" class="button button-secondary delete-theme"><?php _e( 'Delete' ); ?></a>
					<# } #>
				</div>
			</div>
		</script>
		<?php
		// End single theme template shim
	}

	public function tmpl_theme_single_actions() {
		?>
		<# if ( data.actions.childThemify ) { #>
			<a href="{{{ data.actions.childThemify }}}" class="button button-secondary" title="<?php esc_attr_e( 'Create a child theme', 'child-themify' ); ?>"><?php esc_attr_e( 'Create a child theme', 'child-themify' ); ?></a>
		<# } #>
		<?php
		// End single action for CTF
	}

	public function prepare_themes( $themes ) {
		if ( $this->checkCapability() ) {
			foreach ( $themes as $slug => $data ) {
				$theme         = wp_get_theme( $slug );
				$download_link = $this->getLink( $theme );

				$themes[$slug]['actions']['childThemify'] = $download_link ? $download_link : false;
			}
		}
		return $themes;
	}

}

