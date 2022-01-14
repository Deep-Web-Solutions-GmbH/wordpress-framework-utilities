<?php

namespace DeepWebSolutions\Framework\Tests\Utilities;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\Integrations\MaybeSetupOnInitializationTrait;
use DeepWebSolutions\Framework\Foundations\Hierarchy\Actions\InitializeChildrenTrait;
use DeepWebSolutions\Framework\Foundations\Hierarchy\Actions\MaybeSetupChildrenTrait;
use DeepWebSolutions\Framework\Foundations\Hierarchy\Plugin\AbstractPluginRoot;
use function DeepWebSolutions\Plugins\dws_utilities_test_base_path;

\defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 */
class Plugin extends AbstractPluginRoot {
	// region TRAITS

	use InitializeChildrenTrait;
	use MaybeSetupOnInitializationTrait;
	use MaybeSetupChildrenTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * {@inheritDoc}
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_plugin_file_path(): string {
		return dws_utilities_test_base_path() . 'bootstrap.php';
	}

	// endregion
}
