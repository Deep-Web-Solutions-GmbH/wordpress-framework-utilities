<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Setupable;

use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Setupable\SetupFailureException;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;
use DeepWebSolutions\Framework\Utilities\REST\RESTService;
use DeepWebSolutions\Framework\Utilities\REST\RESTServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\REST\RESTServiceRegisterInterface;
use DeepWebSolutions\Framework\Utilities\REST\RESTServiceRegisterTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Trait for registering the REST config of using instances.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Setupable
 */
trait SetupRESTConfigTrait {
	// region TRAITS

	use RESTServiceRegisterTrait;
	use SetupableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically call the REST config registration method.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  SetupFailureException|null
	 */
	public function setup_rest_config(): ?SetupFailureException {
		if ( $this instanceof RESTServiceAwareInterface ) {
			$service = $this->get_rest_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( RESTService::class );
		} else {
			return new SetupFailureException( 'REST config registration setup scenario not supported' );
		}

		if ( ! $this instanceof RESTServiceRegisterInterface ) {
			return new SetupFailureException( sprintf( 'Cannot add REST service subscriber that is not an instance of %s', RESTServiceRegisterInterface::class ) );
		}

		$service->add_subscriber( $this );
		return null;
	}

	// endregion
}
