<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Actions;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Foundations\Hierarchy\ChildInterface;
use DeepWebSolutions\Framework\Foundations\PluginAwareInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesService;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\AdminNotices\AdminNoticesServiceAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for setting the admin notices service on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Actions
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
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters some other error.
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_admin_notices_service(): ?InitializationFailureException {
		if ( $this instanceof ChildInterface && $this->get_parent() instanceof AdminNoticesServiceAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_parent()->get_admin_notices_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( AdminNoticesService::class );
		} elseif ( $this instanceof PluginAwareInterface && $this->get_plugin() instanceof ContainerAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_plugin()->get_container()->get( AdminNoticesService::class );
		} else {
			return new InitializationFailureException( 'Admin notices service initialization scenario not supported' );
		}

		$this->set_admin_notices_service( $service );
		return null;
	}

	// endregion
}
