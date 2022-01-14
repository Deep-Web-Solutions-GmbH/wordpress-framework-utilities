<?php
/**
 * Defines module-specific getters and functions.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 *
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace DeepWebSolutions\Plugins;

\defined( 'ABSPATH' ) || exit;

/**
 * Returns the base path of the plugin.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function dws_utilities_test_base_path() {
	return \constant( __NAMESPACE__ . '\DWS_UTILITIES_TEST_PLUGIN_BASE_PATH' );
}
