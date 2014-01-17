/* jshint node:true */
module.exports = function (grunt) {
	grunt.registerMultiTask('makepot', function () {
		grunt.util.spawn({
			cmd : this.data.cmd,
			args: this.data.args,
			opts: {stdio: 'inherit'}
		}, this.async());
	});
};
