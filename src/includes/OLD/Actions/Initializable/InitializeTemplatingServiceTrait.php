<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\Hierarchy\ChildInterface;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Templating\TemplatingService;
use DeepWebSolutions\Framework\Utilities\Templating\TemplatingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Templating\TemplatingServiceAwareTrait;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for setting the templating service on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeTemplatingServiceTrait {
	// region TRAITS

	use TemplatingServiceAwareTrait;
	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically set a templating service on the instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_templating_service(): ?InitializationFailureException {
		if ( $this instanceof ChildInterface && $this->get_parent() instanceof TemplatingServiceAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_parent()->get_templating_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( TemplatingService::class );
		} else {
			return new InitializationFailureException( 'Templating service initialization scenario not supported' );
		}

		$this->set_templating_service( $service );
		return null;
	}

	// endregion
}
