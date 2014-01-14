/* jshint node:true */
module.exports = (function () {
	var grunt = require('grunt');
	return {
		options: grunt.file.readJSON('.jshintrc'),
		grunt  : {
			src: ['Gruntfile.js']
		},
		ctf    : {
			src: ['assets/js/src/**/*.js']
		}
	};
}());
