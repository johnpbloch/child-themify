/* jshint node:true */
module.exports = {
	grunt: {
		files: ['Gruntfile.js', 'tasks/options/**/*.js'],
		tasks: ['jshint:grunt']
	},
	src  : {
		files: ['assets/js/src/**/*.js'],
		tasks: ['jshint:ctf', 'concat:childthemify', 'uglify:childthemify']
	}
};
