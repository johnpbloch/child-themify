/* jshint node:true */
module.exports = (function () {
	var grunt = require('grunt'),
			PACKAGE = grunt.file.readJSON('package.json');
	return{
		childthemify: {
			src : ['assets/js/src/main.js'],
			dest: 'assets/js/child-themify.js'
		},
		legacy      : {
			src : ['assets/js/src/legacy.js'],
			dest: 'assets/js/legacy.js'
		},
		plugin      : {
			options: {
				banner : '<?php\n/*\n' +
						' * Plugin Name: Child Themify\n' +
						' * Description: Enables the quick creation of child themes ' +
						'from any non-child theme you have installed.\n' +
						' * Version: <%= pkg.version %>\n' +
						' * Plugin URI: https://github.com/johnpbloch/child-themify\n' +
						' * Author: John P. Bloch\n' +
						' * License: GPLv2 or later\n' +
						' */\n',
				process: function (src) {
					// Remove leading php open tags
					src = src.replace('<?php\n', '');
					// Replace version strings
					src = src.replace('%%VERSION%%', '' + PACKAGE.version);
					return src;
				}
			},
			src    : ['plugin/constants.php', 'plugin/main.php'],
			dest   : 'child-themify.php'
		}
	};
}());
