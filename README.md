# Child Themify
This WordPress plugin lets you create child themes from within WordPress. Click the "Create Child Theme" link, name the new theme, and you're done!

### Contributors

Many thanks to everybody who's contributed (directly or indirectly) to this plugin. Thanks!

* Thanks @georgestephanis for the theme action links template override!

### Unit Tests
This plugin has unit tests, but you will need to have [PHP Test Helpers](https://github.com/sebastianbergmann/php-test-helpers) installed. You also need to make sure you are set up to run WordPress tests locally.

To run tests locally, first, set up the WP testing framework: [http://make.wordpress.org/core/handbook/automated-testing/#installation](http://make.wordpress.org/core/handbook/automated-testing/#installation)

Then, add some environment variables to your `.bashrc` file:

```bash
export WP_TESTS_DIR=~/wordpress-tests
export WP_CORE_DIR=~/path/to/wordpress-core
```

Finally, run the tests:

```bash
cd /path/to/child-themify/tests
phpunit
```

If you want to run the tests in Multisite, set the environment variable for it:

```bash
export WP_MULTISITE=1
```

I have a few shortcut aliases set up in my `.bashrc` file which you may want to use:

```bash
alias testsingle="export WP_MULTISITE=0; phpunit"
alias testmultisite="export WP_MULTISITE=1; phpunit"
alias testall="export WP_MULTISITE=0; phpunit; export WP_MULTISITE=1; phpunit"
```

### License
Child Themify is licensed under the [GPL, version 2 or later](http://www.gnu.org/licenses/gpl-2.0.txt).

Copyright (C) 2012  John P. Bloch

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
