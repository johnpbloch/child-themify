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
		JPB_CTF_URL,
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
	return '{{VERSION}}' === JPB_CTF_VERSION ? time() : JPB_CTF_VERSION;
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

/**
 * @param string $slug
 * @param WP_Theme $parent
 * @param string $name
 * @param string $author
 * @param array $extra_files
 * @param array $creds
 */
function child_themify_create_theme(
	$slug,
	$parent,
	$name = '',
	$author = '',
	$extra_files = array(),
	$creds = array()
) {
	$fs = child_themify_get_fs_object( (array) $creds );
	if ( ! $fs ) {
		throw new InvalidArgumentException( esc_html__( 'Could not write to server! Please check your credentials and try again.', 'child-themify' ), 1 );
	}

	$parent_directory    = $parent->get_stylesheet_directory();
	$new_theme_directory = trailingslashit( get_theme_root() ) . $slug;

	if ( ! child_themify_mkdir_p( $fs, $new_theme_directory ) ) {
		throw new LogicException( esc_html__( 'Could not create new theme directory!', 'child-themify' ), 2 );
	}

	$new_stylesheet = trailingslashit( $new_theme_directory ) . 'style.css';
	if ( ! $fs->touch( $new_stylesheet ) ) {
		throw new LogicException( esc_html__( 'Could not create new theme stylesheet!', 'child-themify' ), 3 );
	}

	$stylesheet_contents = child_themify_get_stylesheet_contents(
		$name,
		$author,
		$parent->get( 'Name' ),
		$parent->get_stylesheet(),
		child_themify_relative_url(
			trailingslashit( get_theme_root_uri() ) . "$slug/style.css",
			trailingslashit( $parent->get_stylesheet_directory_uri() ) . 'style.css'
		)
	);
	if ( ! $fs->put_contents( $new_stylesheet, $stylesheet_contents ) ) {
		throw new LogicException( esc_html__( 'Could not write to the new theme stylesheet!', 'child-themify' ), 4 );
	}

	if ( $fs->exists( "$parent_directory/screenshot.png" ) ) {
		$fs->copy( "$parent_directory/screenshot.png", "$new_theme_directory/screenshot.png" );
	}

	$functions = trailingslashit( $new_theme_directory ) . 'functions.php';
	if ( ! $fs->touch( $functions ) ) {
		throw new LogicException( esc_html__( 'Could not create the new functions.php file!', 'child-themify' ), 5 );
	}
	$fs->put_contents( $functions, "<?php\n" );

	foreach ( $extra_files as $extra_file ) {
		if ( false !== strpos( $extra_file, '../' ) ) {
			continue;
		}
		$original = "$parent_directory/$extra_file";
		$target   = "$new_theme_directory/$extra_file";
		if ( ! $fs->exists( $original ) ) {
			continue;
		}
		if ( ! $fs->exists( dirname( $target ) ) && ! child_themify_mkdir_p( $fs, dirname( $target ) ) ) {
			continue;
		}
		$fs->copy( "$parent_directory/$extra_file", "$new_theme_directory/$extra_file" );
	}
}

/**
 * Returns a relative url from one URL to another
 *
 * This assumes the domain is the same
 *
 * @param string $from
 * @param string $to
 *
 * @return string
 */
function child_themify_relative_url( $from, $to ) {
	$from = parse_url( $from, PHP_URL_PATH );
	$to   = parse_url( $to, PHP_URL_PATH );
	$from = explode( '/', $from );
	$to   = explode( '/', $to );
	$rel  = $to;
	foreach ( $from as $pos => $part ) {
		if ( isset( $to[ $pos ] ) && $to[ $pos ] === $part ) {
			array_shift( $rel );
		} else {
			$remaining = count( $from ) - $pos;
			if ( $remaining > 1 ) {
				$pad_length = ( count( $rel ) + $remaining - 1 ) * - 1;
				$rel        = array_pad( $rel, $pad_length, '..' );
			} else {
				$rel[0] = './' . $rel[0];
			}
			break;
		}
	}

	return implode( '/', array_filter( $rel ) );
}

/**
 * Get the contents of the new stylesheet
 *
 * @param string $name
 * @param string $author
 * @param string $parent_name
 * @param string $parent_stylesheet
 * @param string $import
 *
 * @return string
 */
function child_themify_get_stylesheet_contents( $name, $author, $parent_name, $parent_stylesheet, $import ) {
	/* translators: The placeholder is the parent theme's name */
	$description = sprintf( esc_html__( 'A child theme of %s', 'child-themify' ), $parent_name );
	if ( $author ) {
		$description .= "\nAuthor: $author";
	}
	$stylesheet_contents = <<<EOF
/*
Theme Name: $name
Version: 1.0
Description: $description
Template: $parent_stylesheet
*/

@import url("$import");

EOF;

	return $stylesheet_contents;
}

/**
 * Recursive mkdir using the WP filesystem wrapper
 *
 * @param WP_Filesystem_Base $fs
 * @param string $dir
 *
 * @return bool
 */
function child_themify_mkdir_p( $fs, $dir ) {
	$new = array();
	while ( ! $fs->exists( $dir ) ) {
		array_unshift( $new, basename( $dir ) );
		if ( $dir === dirname( $dir ) ) {
			// This would mean the filesystem doesn't exist
			// Probably not a scenario that will be encountered,
			// but I also want to avoid an infinite loop.
			return false;
		}
		$dir = dirname( $dir );
	}
	foreach ( $new as $part ) {
		$dir = trailingslashit( $dir ) . $part;
		if ( ! $fs->mkdir( $dir ) ) {
			return false;
		}
	}

	return true;
}
