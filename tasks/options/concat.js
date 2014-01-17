/* jshint node:true */
module.exports = (function () {
	var grunt = require('grunt');
	return{
		childthemify: {
			options: {
				banner: '<%= banner %>'
			},
			src    : ['assets/js/src/main.js'],
			dest   : 'assets/js/child-themify.js'
		},
		legacy      : {
			options: {
				banner: '<%= banner %>'
			},
			src    : ['assets/js/src/legacy.js'],
			dest   : 'assets/js/legacy.js'
		},
		plugin      : {
			options: {
				banner : '<?php\n/*\n' +
						' * Plugin Name: <%= pkg.wpData.displayName %>\n' +
						' * Description: <%= pkg.wpData.shortDescription %>\n' +
						' * Version: <%= pkg.version %>\n' +
						' * Plugin URI: https://github.com/johnpbloch/child-themify\n' +
						' * Author: John P. Bloch\n' +
						' * License: <%= pkg.license %>\n' +
						' */\n',
				process: function (src) {
					// Remove leading php open tags
					src = src.replace('<?php\n', '');
					// Replace version strings
					src = src.replace('%%VERSION%%', '' + grunt.config('pkg').version);
					return src;
				}
			},
			src    : ['plugin/constants.php', 'plugin/main.php'],
			dest   : 'child-themify.php'
		}
	};
}());
