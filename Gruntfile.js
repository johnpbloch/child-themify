/* jshint node:true */
module.exports = function (grunt) {
	require('load-grunt-tasks')(grunt);

	grunt.util.linefeed = '\n';

	function loadConfig(path) {
		var glob = require('glob'),
				object = {},
				key;

		glob.sync('*', {cwd: path}).forEach(function (option) {
			key = option.replace(/\.js$/, '');
			object[key] = require(path + option);
		});

		return object;
	}

	var PACKAGE = grunt.file.readJSON('package.json'),
			config = {
				pkg   : PACKAGE,
				banner: '/*! <%= wp_readme.plugin.displayName %> - v<%= pkg.version %>\n' +
						'* Copyright (c) <%= grunt.template.today("yyyy") %> ' +
						'John P. Bloch */\n'
			};

	grunt.util._.extend(config, loadConfig('./tasks/options/'));

	grunt.initConfig(config);

	grunt.loadTasks('tasks');

	grunt.registerTask('i18n', ['copy:tmp', 'makepot']);
	grunt.registerTask('lint', ['jshint', 'phplint']);
	grunt.registerTask('cp', ['copy:files', 'copy:screenshots']);
	grunt.registerTask('light_build', ['lint', 'concat', 'wp_readme', 'uglify', 'clean:trunk', 'cp']);
	grunt.registerTask('build', ['lint', 'concat', 'wp_readme', 'uglify', 'i18n', 'clean:trunk', 'cp']);
	grunt.registerTask('release', ['build', 'copy:release']);
	grunt.registerTask('default', ['lint', 'concat', 'uglify']);
};
