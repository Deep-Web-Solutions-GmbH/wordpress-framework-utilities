<?php

namespace DeepWebSolutions\Framework\Utilities\Actions\Initializable;

use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializableExtensionTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Initializable\InitializationFailureException;
use DeepWebSolutions\Framework\Foundations\Hierarchy\ChildInterface;
use DeepWebSolutions\Framework\Utilities\CronEvents\CronEventsService;
use DeepWebSolutions\Framework\Utilities\CronEvents\CronEventsServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\CronEvents\CronEventsServiceAwareTrait;
use DeepWebSolutions\Framework\Utilities\DependencyInjection\ContainerAwareInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Trait for setting the cron events service on the using instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Actions\Initializable
 */
trait InitializeCronEventsServiceTrait {
	// region TRAITS

	use CronEventsServiceAwareTrait;
	use InitializableExtensionTrait;

	// endregion

	// region METHODS

	/**
	 * Try to automagically set a cron events service on the instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  InitializationFailureException|null
	 */
	public function initialize_cron_events_service(): ?InitializationFailureException {
		if ( $this instanceof ChildInterface && $this->get_parent() instanceof CronEventsServiceAwareInterface ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$service = $this->get_parent()->get_cron_events_service();
		} elseif ( $this instanceof ContainerAwareInterface ) {
			$service = $this->get_container()->get( CronEventsService::class );
		} else {
			return new InitializationFailureException( 'CRON events service initialization scenario not supported' );
		}

		$this->set_cron_events_service( $service );
		return null;
	}

	// endregion
}
