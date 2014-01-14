/* jshint node:true */
module.exports = (function () {
	var SRC = [
				'**',
				// No source control should be copied
				'!**/.git**/**',
				// No Github files should be copied
				'!**/README.md',
				// No Grunt stuff should get copied
				'!**/node_modules/**',
				'!**/tasks/**',
				'!**/package.json',
				'!**/Gruntfile.js',
				// No composer stuff should get copied
				'!**/vendor/**',
				'!**/composer.json',
				'!**/composer.lock',
				'!**/composer.phar',
				// No PHPUnit stuff should get copied
				'!phpunit.xml**',
				'!bootstrap.php',
				'!tests/**',
				// Only the compiled js
				'!assets/js/{src,vendor}/**',
				// Don't copy the build folder into itself
				'!build/**',
				// Don't copy the main plugin file's parts
				'!plugin/**'
			],
			temp = require('temp'),
			path = require('path'),
			tempDir;

	temp.track();

	tempDir = path.join(temp.mkdirSync(), 'child-themify');

	return {
		files : {
			files: [
				{
					dot   : false,
					expand: true,
					cwd   : '.',
					src   : SRC,
					dest  : 'build/trunk/'
				}
			]
		},
		tmp   : {
			files: [
				{
					dot   : false,
					expand: true,
					cwd   : '.',
					src   : SRC,
					dest  : tempDir
				}
			]
		},
		tmpDir: tempDir
	};
}());
