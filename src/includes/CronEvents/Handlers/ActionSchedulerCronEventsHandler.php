<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResetFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Utilities\CronEvents\AbstractCronEventsHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of cron events with the Action Scheduler's API.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents\Handlers
 */
class ActionSchedulerCronEventsHandler extends AbstractCronEventsHandler implements PluginAwareInterface {
	// region TRAITS

	use PluginAwareTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers cron events with the Action Scheduler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run_local(): ?RunFailureException {
		$run_result = null;

		$events = \array_merge( $this->single_events, $this->recurring_events );
		foreach ( $events as $event ) {
			if ( \as_next_scheduled_action( $event['hook'], $event['args'] ) ) {
				continue;
			}

			if ( isset( $event['recurrence'] ) ) {
				$result = \as_schedule_recurring_action( $event['timestamp'], $event['recurrence'], $event['hook'], $event['args'], $this->get_plugin()->get_plugin_slug() );
			} else {
				$result = \as_schedule_single_action( $event['timestamp'], $event['hook'], $event['args'], $this->get_plugin()->get_plugin_slug() );
			}

			if ( 0 === $result ) {
				$run_result = new RunFailureException( \sprintf( 'Failed to schedule event %s', \wp_json_encode( $event ) ) );
				break;
			}
		}

		return $run_result;
	}

	/**
	 * Clears the registered cron events from the Action Scheduler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailureException|null
	 */
	public function reset_local(): ?ResetFailureException {
		$reset_result = null;

		$events = \array_merge( $this->single_events, $this->recurring_events );
		foreach ( $events as $event ) {
			$result = \as_unschedule_action( $event['hook'], $event['args'], $this->get_plugin()->get_plugin_slug() );

			if ( false === $result ) {
				$reset_result = new ResetFailureException( \sprintf( 'Failed to unschedule event %s', \wp_json_encode( $event ) ) );
				break;
			}
		}

		return $reset_result;
	}

	// endregion
}
