<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResetFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Utilities\Hooks\AbstractHooksHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for filters and actions.
 *
 * Maintain a list of all hooks that are registered throughout the plugin, and handles their registration with
 * the WordPress API after calling the run function.
 *
 * @see     https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/blob/master/plugin-name/includes/class-plugin-name-loader.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Handlers
 */
class DefaultHooksHandler extends AbstractHooksHandler {
	// region MAGIC METHODS

	/**
	 * DefaultHooksHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler_id     The ID of the handler instance.
	 */
	public function __construct( string $handler_id = 'default' ) { // phpcs:ignore
		parent::__construct( $handler_id );
	}

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( \is_null( $this->is_run ) ) {
			\array_walk( $this->filters, array( $this, 'array_walk_add_hook' ) );
			\array_walk( $this->actions, array( $this, 'array_walk_add_hook' ) );

			$this->is_run     = true;
			$this->run_result = $this->reset_result = $this->is_reset = null; // phpcs:ignore
		}

		return $this->run_result;
	}

	/**
	 * De-registers the filters and actions with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailureException|null
	 */
	public function reset(): ?ResetFailureException {
		if ( \is_null( $this->is_reset ) ) {
			\array_walk( $this->filters, array( $this, 'array_walk_remove_hook' ) );
			\array_walk( $this->actions, array( $this, 'array_walk_remove_hook' ) );

			$this->is_reset     = true;
			$this->reset_result = $this->is_run = $this->run_result = null; // phpcs:ignore
		}

		return $this->reset_result;
	}

	// endregion

	// region HELPERS

	/**
	 * Registers an action/filter with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $hook   Filter to register.
	 *
	 * @return  bool    Whether registration was successful or not.
	 */
	protected function array_walk_add_hook( array $hook ): bool {
		if ( empty( $hook['component'] ) ) {
			return \add_filter( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		} else {
			return \add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}

	/**
	 * Un-registers an action/filter with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $hook   Filter to un-register.
	 *
	 * @return  bool    Whether un-registration was successful or not.
	 */
	protected function array_walk_remove_hook( array $hook ): bool {
		if ( empty( $hook['component'] ) ) {
			return \remove_filter( $hook['hook'], $hook['callback'], $hook['priority'] );
		} else {
			return \remove_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'] );
		}
	}

	// endregion
}
