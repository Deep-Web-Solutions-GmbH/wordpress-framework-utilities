<?php
/**
 * The DWS WordPress Framework Utilities Test Plugin bootstrap file.
 *
 * @since               1.0.0
 * @version             1.0.0
 * @package             DeepWebSolutions\WP-Framework\Utilities\Tests
 * @author              Deep Web Solutions GmbH
 * @copyright           2022 Deep Web Solutions GmbH
 * @license             GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:         DWS WordPress Framework Utilities Test Plugin
 * Description:         A WP plugin used to run automated tests against the DWS WP Framework Utilities package.
 * Version:             1.0.0
 * Requires PHP:        7.4
 * Author:              Deep Web Solutions GmbH
 * Author URI:          https://www.deep-web-solutions.com
 * License:             GPL-3.0+
 * License URI:         http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:         dws-wp-utilities-test-plugin
 */

namespace DeepWebSolutions\Plugins;

\defined( 'ABSPATH' ) || exit;

// Register autoloader for testing dependencies.
\is_file( __DIR__ . '/vendor/autoload.php' ) && require_once __DIR__ . '/vendor/autoload.php';
if ( ! \defined( 'DeepWebSolutions\Framework\DWS_WP_FRAMEWORK_BOOTSTRAPPER_NAME' ) ) {
	require __DIR__ . '/vendor/deep-web-solutions/wp-framework-bootstrapper/bootstrap.php';
	require __DIR__ . '/vendor/deep-web-solutions/wp-framework-helpers/bootstrap.php';
	require __DIR__ . '/vendor/deep-web-solutions/wp-framework-foundations/bootstrap.php';
	require __DIR__ . '/vendor/deep-web-solutions/wp-framework-utilities/bootstrap.php';
}

// Load plugin-specific bootstrapping functions.
require_once __DIR__ . '/bootstrap-functions.php';

// Define plugins' constants.
\define( __NAMESPACE__ . '\DWS_UTILITIES_TEST_PLUGIN_BASE_PATH', plugin_dir_path( __FILE__ ) );

// Start the plugin.
require_once __DIR__ . '/functions.php';
add_action( 'plugins_loaded', 'dws_utilities_test_plugin_instance_initialize' );
