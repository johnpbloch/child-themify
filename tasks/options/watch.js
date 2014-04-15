/* jshint node:true */
module.exports = {
	grunt : {
		files: ['Gruntfile.js', 'tasks/options/**/*.js'],
		tasks: ['jshint:grunt']
	},
	plugin: {
		files: ['<%= concat.plugin.src %>'],
		tasks: ['phplint:plugin', 'concat:plugin']
	},
	php   : {
		files: ['<%= phplint.plugin %>', '!plugin/**'],
		tasks: ['phplint:plugin']
	},
	src   : {
		files: ['assets/js/src/**/*.js'],
		tasks: ['jshint:ctf', 'concat:legacy', 'uglify:childthemify']
	}
};
