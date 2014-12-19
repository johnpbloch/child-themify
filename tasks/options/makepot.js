/* jshint node:true */
module.exports = (function () {
	return {
		'default': {
			cmd : 'php',
			args: [
				'vendor/wordpress/i18n-tools/tools/i18n/makepot.php',
				'wp-plugin',
				'<%= copy.tmpDir %>',
				'languages/child-themify.pot'
			]
		}
	};
}());
