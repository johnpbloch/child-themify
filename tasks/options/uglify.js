/* jshint node:true */
module.exports = {
	options     : {
		// the banner is inserted at the top of the output
		banner: '<%= banner %>'
	},
	childthemify: {
		files: {
			'assets/js/legacy.min.js'       : ['assets/js/legacy.js']
		}
	}
};
