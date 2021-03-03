<?php

namespace DeepWebSolutions\Framework\Utilities\CronEvents\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResetFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Utilities\CronEvents\CronIntervalsEnum;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the registration of cron events with the Action Scheduler's API.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @package DeepWebSolutions\WP-Framework\Utilities\CronEvents\Handlers
 */
class ActionSchedulerHandler extends AbstractHandler implements PluginAwareInterface {
	// region TRAITS

	use PluginAwareTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the handler's type.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'action-scheduler';
	}

	/**
	 * Registers cron events with the Action Scheduler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( is_null( $this->is_run ) ) {
			$this->run_result = null;

			$events = array_merge( $this->single_events, $this->recurring_events );
			foreach ( $events as $event ) {
				if ( as_next_scheduled_action( $event['hook'], $event['args'] ) ) {
					continue;
				}

				if ( isset( $event['recurrence'] ) ) {
					$result = as_schedule_recurring_action( $event['timestamp'], $event['recurrence'], $event['hook'], $event['args'], $this->get_plugin()->get_plugin_slug() );
				} else {
					$result = as_schedule_single_action( $event['timestamp'], $event['hook'], $event['args'], $this->get_plugin()->get_plugin_slug() );
				}

				if ( 0 === $result ) {
					$this->run_result = new RunFailureException( sprintf( 'Failed to schedule event %s', wp_json_encode( $event ) ) );
					break;
				}
			}

			$this->is_run       = is_null( $this->run_result );
			$this->reset_result = $this->is_reset = null; // phpcs:ignore
		} else {
			return new RunFailureException( 'Handler has already been run. Please reset it before running it again.' );
		}

		return $this->run_result;
	}

	/**
	 * Clears the registered cron events from the Action Scheduler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailureException|null
	 */
	public function reset(): ?ResetFailureException {
		if ( is_null( $this->is_reset ) ) {
			$this->reset_result = null;

			$events = array_merge( $this->single_events, $this->recurring_events );
			foreach ( $events as $event ) {
				$result = as_unschedule_action( $event['hook'], $event['args'], $this->get_plugin()->get_plugin_slug() );

				if ( false === $result ) {
					$this->reset_result = new ResetFailureException( sprintf( 'Failed to unschedule event %s', wp_json_encode( $event ) ) );
					break;
				}
			}

			$this->is_reset   = is_null( $this->reset_result );
			$this->run_result = $this->is_run = null; // phpcs:ignore
		} else {
			return new ResetFailureException( 'Handler has already been reset. Please run it before resetting again.' );
		}

		return $this->reset_result;
	}

	// endregion
}
