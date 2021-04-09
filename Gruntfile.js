module.exports = function( grunt ) {
	'use strict';

	// Load all grunt tasks matching the `grunt-*` pattern
	require( 'load-grunt-tasks' )( grunt );

	// Show elapsed time
	require( 'time-grunt' )( grunt );

	// Project configuration
	grunt.initConfig(
		{
			package : grunt.file.readJSON( 'package.json' ),
			dirs    : {
				lang : 'src/languages',
				code : 'src'
			},

			glotpress_download : {
				dist : {
					options : {
						domainPath : '<%= dirs.lang %>',
						url		   : 'https://translate.deep-web-solutions.com/glotpress/',
						slug 	   : 'dws-wp-framework/utilities',
						textdomain : 'dws-wp-framework-utilities'
					}
				}
			},
			makepot 		   : {
				dist : {
					options : {
						cwd				: '<%= dirs.code %>',
						domainPath		: 'languages',
						exclude			: [],
						potFilename		: 'dws-wp-framework-utilities.pot',
						mainFile		: 'bootstrap.php',
						potHeaders		: {
							'report-msgid-bugs-to'	: 'https://github.com/deep-web-solutions/wordpress-framework-utilities/issues',
							'project-id-version'	: '<%= package.title %> <%= package.version %>',
							'poedit'     		    : true,
							'x-poedit-keywordslist' : true,
						},
						processPot		: function( pot ) {
							delete pot.headers['x-generator'];

							// include the default value of the constant DWS_WP_FRAMEWORK_UTILITIES_NAME
							pot.translations['']['DWS_WP_FRAMEWORK_UTILITIES_NAME'] = {
								msgid: 'Deep Web Solutions: Framework Utilities',
								comments: { reference: 'bootstrap.php:42' },
								msgstr: [ '' ]
							};

							return pot;
						},
						type   			: 'wp-plugin',
						updateTimestamp : false,
						updatePoFiles   : true
					}
				}
			},

			replace 		   : {
				readme_md     : {
					src 	     : [ 'README.md' ],
					overwrite    : true,
					replacements : [
						{
							from : /\*\*Stable tag:\*\* (.*)/,
							to   : "**Stable tag:** <%= package.version %>  "
						}
					]
				},
				bootstrap_php : {
					src 		 : [ 'bootstrap.php' ],
					overwrite 	 : true,
					replacements : [
						{
							from : /Version:(\s*)(.*)/,
							to   : "Version:$1<%= package.version %>"
						},
						{
							from : /define\( __NAMESPACE__ \. '\\DWS_WP_FRAMEWORK_UTILITIES_VERSION', '(.*)' \);/,
							to   : "define( __NAMESPACE__ . '\\DWS_WP_FRAMEWORK_UTILITIES_VERSION', '<%= package.version %>' );"
						}
					]
				}
			}
		}
	);

	grunt.registerTask( 'i18n', [ 'makepot', 'glotpress_download' ] );
	grunt.registerTask( 'version_number', [ 'replace:readme_md', 'replace:bootstrap_php' ] );
}
