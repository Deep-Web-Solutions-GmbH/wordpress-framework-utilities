<?php

namespace DeepWebSolutions\Framework\Utilities\WordPress\IsDisabled;

use DeepWebSolutions\Framework\Utilities\Lifecycle\IsDisabled\IsDisabledExtensionTrait;

/**
 * Functionality trait for dependent disablement of integration classes.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\WordPress\IsDisabled
 */
trait IntegrationTrait {
	use IsDisabledExtensionTrait;

	/**
	 * Using class should define the logic for determining whether the integration is applicable or not in here.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool    True if NOT applicable, for otherwise.
	 */
	abstract public function is_disabled_integration(): bool;
}
