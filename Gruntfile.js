/* jshint node:true */
module.exports = function (grunt) {
	grunt.initConfig({
		pkg   : grunt.file.readJSON('package.json'),
		uglify: {
			options: {
				// the banner is inserted at the top of the output
				banner: '/*! Child Themify - v<%= pkg.version %>\n' +
						'* Copyright (c) <%= grunt.template.today("yyyy") %> ' +
						'John P. Bloch */'
			},
			dist   : {
				'ctf.min.js': ['ctf.js']
			}
		},
		jshint: {
			options: grunt.file.readJSON('.jshintrc'),
			grunt  : {
				src: ['Gruntfile.js']
			},
			ctf    : {
				src: ['ctf.js']
			}
		},
		watch : {
			grunt: {
				files: 'Gruntfile.js',
				tasks: ['jshint:grunt']
			},
			src  : {
				files: ['ctf.js'],
				tasks: ['jshint:ctf', 'uglify:dist']
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['jshint', 'uglify']);
};
