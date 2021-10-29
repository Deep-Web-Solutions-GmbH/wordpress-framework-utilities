<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Actions;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Foundations\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Foundations\PluginAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering hooks of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Actions
 */
trait SetupHooksTrait {
	// region TRAITS

	use HooksServiceRegisterTrait;
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the hooks registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters some other error.
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_hooks(): ?SetupFailureException {
		if ( $this instanceof HooksServiceAwareInterface ) {
			$service = $this->get_hooks_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( HooksService::class );
		} elseif ( $this instanceof PluginAwareInterface && $this->get_plugin() instanceof ContainerAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_plugin()->get_container()->get( HooksService::class );
		} else {
			return new SetupFailureException( 'Hooks registration setup scenario not supported' );
		}

		$this->register_hooks( $service );
		return null;
	}

	// endregion
}
