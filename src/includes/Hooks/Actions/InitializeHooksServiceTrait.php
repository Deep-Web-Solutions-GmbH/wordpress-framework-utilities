<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Actions;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Foundations\Hierarchy\ChildInterface;
use DeepWebSolutions\Framework\Foundations\PluginAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for setting the hooks service on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Actions
 */
trait InitializeHooksServiceTrait {
	// region TRAITS

	use HooksServiceAwareTrait;
	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically set a hooks service on the instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @throws  NotFoundExceptionInterface      Thrown if the container can't find an entry.
	 * @throws  ContainerExceptionInterface     Thrown if the container encounters some other error.
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_hooks_service(): ?InitializationFailureException {
		if ( $this instanceof ChildInterface && $this->get_parent() instanceof HooksServiceAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_parent()->get_hooks_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( HooksService::class );
		} elseif ( $this instanceof PluginAwareInterface && $this->get_plugin() instanceof ContainerAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_plugin()->get_container()->get( HooksService::class );
		} else {
			return new InitializationFailureException( 'Hooks service initialization scenario not supported' );
		}

		$this->set_hooks_service( $service );
		return null;
	}

	// endregion
}
