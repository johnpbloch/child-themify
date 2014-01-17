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
			grunt = require('grunt'),
			temp = require('temp'),
			path = require('path'),
			tempDir;

	function createRenameFunction(counter, mimetype) {
		return function (dest) {
			return dest + 'screenshot-' + counter + mimetype;
		};
	}

	function getScreenshotsConfig() {
		var screenshots = grunt.file.readJSON('package.json').wpData.screenshots,
				index,
				screenshot,
				counter,
				config = {
					files: []
				},
				mimeType;
		for (index = 0; index < screenshots.length; index += 1) {
			screenshot = screenshots[index];
			counter = index + 1;
			mimeType = screenshot.file.substr(screenshot.file.lastIndexOf('.'));
			config.files.push({
				expand: true,
				cwd   : 'assets/img/screenshots/',
				src   : screenshot.file,
				dest  : 'build/assets/',
				rename: createRenameFunction(counter, mimeType)
			});
		}
		return config;
	}

	temp.track();

	tempDir = path.join(temp.mkdirSync(), 'child-themify');

	return {
		files      : {
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
		tmp        : {
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
		screenshots: getScreenshotsConfig(),
		tmpDir     : tempDir
	};
}());
