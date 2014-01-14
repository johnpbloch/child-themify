/* jshint node:true */
module.exports = function (grunt) {
	require('load-grunt-tasks')(grunt);

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
				pkg: PACKAGE
			};

	grunt.util._.extend(config, loadConfig('./tasks/options/'));

	grunt.initConfig(config);

	grunt.loadTasks('tasks');

	grunt.registerTask('i18n', ['copy:tmp', 'makepot']);
	grunt.registerTask('build', ['jshint', 'concat', 'uglify', 'clean:trunk', 'copy:files']);
	grunt.registerTask('default', ['jshint', 'concat', 'uglify']);
};
