/* jshint node:true */
module.exports = function (grunt) {
	var fs = require('fs'),
			pkg = grunt.config('pkg'),
			wp = pkg.wpData;

	grunt.registerTask('readme', function () {
		var readmeContents,
				index,
				i2,
				item;

		grunt.log.write('Writing to readme.txt...');

		readmeContents = '=== ' + wp.displayName + ' ===\n' +
				'Contributors: ' + wp.contributors.join(', ') + '\n' +
				'Tags: ' + pkg.keywords.join(', ') + '\n' +
				'Requires at least: ' + wp.minimumVersion + '\n' +
				'Tested up to: ' + wp.testtedUpTo + '\n' +
				'Stable tag: ' + pkg.version + '\n' +
				'License: ' + pkg.license + '\n' +
				'License URI: http://www.gnu.org/licenses/gpl-2.0.html\n\n' +
				wp.shortDescription + '\n\n' +
				'== Description ==\n\n' + pkg.description + '\n\n';

		if (wp.installation.length) {
			readmeContents += '== Installation ==\n\n';
			for (index = 0; index < wp.installation.length; index += 1) {
				item = wp.installation[index];
				readmeContents += '1. ' + item + '\n';
			}
		} else {
			readmeContents += 'None yet.\n';
		}

		readmeContents += '\n== Frequently Asked Questions ==\n\n';

		if (wp.faq.length) {
			for (index = 0; index < wp.faq.length; index += 1) {
				item = wp.faq[index];
				readmeContents += '= ' + item.question + ' =\n\n';
				readmeContents += item.answer + '\n\n';
			}
		} else {
			readmeContents += 'None yet.\n\n';
		}

		if (wp.screenshots.length) {
			readmeContents += '== Screenshots ==\n\n';
			for (index = 1; index <= wp.screenshots.length; index += 1) {
				item = wp.screenshots[index - 1];
				readmeContents += index + '. ' + item.caption + '\n';
			}
			readmeContents += '\n';
		}

		readmeContents += '== Changelog ==\n\n';
		for (index = 0; index < wp.changelog.length; index += 1) {
			item = wp.changelog[index];
			readmeContents += '= ' + item.version + ' =\n' +
					'* Released: ' + item.releaseDate + '\n';
			for (i2 = 0; i2 < item.changes.length; i2 += 1) {
				readmeContents += '* ' + item.changes[i2] + '\n';
			}
			readmeContents += '\n';
		}

		readmeContents += '== Upgrade Notice ==\n\n' +
				wp.changelog[0].releaseMessage + '\n';

		fs.writeFileSync('readme.txt', readmeContents);
		grunt.log.writeln(' Done!');
	});
};
