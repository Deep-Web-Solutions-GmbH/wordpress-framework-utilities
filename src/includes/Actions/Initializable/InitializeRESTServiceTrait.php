<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\Hierarchy\ChildInterface;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\REST\RESTServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\REST\RESTServiceAwareTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for setting the REST service on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeRESTServiceTrait {
	// region TRAITS

	use RESTServiceAwareTrait;
	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically set a REST service on the instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_rest_service(): ?InitializationFailureException {
		if ( $this instanceof ChildInterface && $this->get_parent() instanceof RESTServiceAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_parent()->get_rest_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( RESTServiceAwareTrait::class );
		} else {
			return new InitializationFailureException( 'REST service initialization scenario not supported' );
		}

		$this->set_rest_service( $service );
		return null;
	}

	// endregion
}
