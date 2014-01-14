/* jshint node:true */
module.exports = (function () {
	return {
		'default': {
			cmd : 'php',
			args: [
				'vendor/bin/makepot.php',
				'wp-plugin',
				'<%= copy.tmpDir %>',
				'languages/child-themify.pot'
			]
		}
	};
}());
