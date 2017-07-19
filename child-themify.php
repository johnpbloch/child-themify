<?php
/*
 * Plugin Name: Child Themify
 * Description: Create child themes at the click of a button
 * Version: {{VERSION}}
 * Plugin URI: https://github.com/johnpbloch/child-themify
 * Author: John P. Bloch
 * License: GPL-2.0+
 * Text Domain: child-themify
 * Domain Path: /languages
 */

define( 'CTF_PATH', __FILE__ );
define( 'CTF_URL', plugin_dir_url( CTF_PATH ) );
define( 'CTF_VERSION', '{{VERSION}}' );

function ctf_plugins_loaded() {
}

add_action( 'plugins_loaded', 'ctf_plugins_loaded' );
