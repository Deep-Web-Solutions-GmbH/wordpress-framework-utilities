<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\Hierarchy\ChildInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesService;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesServiceAwareTrait;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for setting the admin notices service on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeAdminNoticesServiceTrait {
	// region TRAITS

	use AdminNoticesServiceAwareTrait;
	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically set an admin notices service on the instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_admin_notices_service(): ?InitializationFailureException {
		if ( $this instanceof ChildInterface && $this->get_parent() instanceof AdminNoticesServiceAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_parent()->get_admin_notices_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( AdminNoticesService::class );
		} else {
			return new InitializationFailureException( 'Admin notices service initialization scenario not supported' );
		}

		$this->set_admin_notices_service( $service );
		return null;
	}

	// endregion
}
