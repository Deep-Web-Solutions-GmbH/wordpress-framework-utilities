<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Utilities\States\Activeable\Dependencies\DependenciesAdminNoticesTrait as ActiveableDependenciesAdminNoticesTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering admin notices of missing dependencies of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Setupable
 */
trait SetupDependenciesAdminNoticesTrait {
	// region TRAITS

	use ActiveableDependenciesAdminNoticesTrait;
	use SetupAdminNoticesTrait { setup_admin_notices as setup_admin_notices_trait; }
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the admin notices registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_dependencies_admin_notices(): ?SetupFailureException {
		return $this->setup_admin_notices_trait();
	}

	// endregion
}
