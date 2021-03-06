<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\Hierarchy\ChildInterface;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsService;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Assets\AssetsServiceAwareTrait;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Validation\ValidationService;
use DeepWebSolutions\Framework\Utilities\Validation\ValidationServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Validation\ValidationServiceAwareTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for setting the validation service on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeValidationServiceTrait {
	// region TRAITS

	use ValidationServiceAwareTrait;
	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically set a validation service on the instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_validation_service(): ?InitializationFailureException {
		if ( $this instanceof ChildInterface && $this->get_parent() instanceof ValidationServiceAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_parent()->get_validation_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( ValidationService::class );
		} else {
			return new InitializationFailureException( 'Validation service initialization scenario not supported' );
		}

		$this->set_validation_service( $service );
		return null;
	}

	// endregion
}
