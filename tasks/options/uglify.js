/* jshint node:true */
module.exports = {
	options     : {
		// the banner is inserted at the top of the output
		banner: '/*! <%= pkg.wpData.displayName %> - v<%= pkg.version %>\n' +
				'* Copyright (c) <%= grunt.template.today("yyyy") %> ' +
				'John P. Bloch */'
	},
	childthemify: {
		files: {
			'assets/js/child-themify.min.js': ['assets/js/child-themify.js'],
			'assets/js/legacy.min.js'       : ['assets/js/legacy.js']
		}
	}
};
