/* jshint node:true */
var os = require('os'),
		fs = require('fs');
module.exports = (function () {
	var file = fs.realpathSync('vendor/bin/makepot.php');
	if (/^win/.test(os.platform())) {
		file += '.bat';
	}
	return {
		'default': {
			cmd : file,
			args: [
				'wp-plugin',
				'<%= copy.tmpDir %>',
				'languages/child-themify.pot'
			]
		}
	};
}());
