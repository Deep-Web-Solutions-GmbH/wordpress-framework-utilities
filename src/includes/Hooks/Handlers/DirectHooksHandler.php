<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Handlers;

use DeepWebSolutions\Framework\Utilities\Hooks\AbstractHooksHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for filters and actions. Maintains an internal list of
 * hooks and actions registered.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks\Handlers
 */
class DirectHooksHandler extends AbstractHooksHandler {
	// region MAGIC METHODS

	/**
	 * DirectHooksHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $handler_id     The ID of the handler instance.
	 */
	public function __construct( string $handler_id = 'internal' ) { // phpcs:ignore
		parent::__construct( $handler_id );
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers a new action with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string         $hook           The name of the WordPress action that is being registered.
	 * @param    object|null    $component      A reference to the instance of the object on which the action is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int            $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		parent::add_action( $hook, $component, $callback, $priority, $accepted_args );

		if ( empty( $component ) ) {
			\add_action( $hook, $callback, $priority, $accepted_args );
		} else {
			\add_action( $hook, array( $component, $callback ), $priority, $accepted_args );
		}
	}

	/**
	 * Removes an action registered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string         $hook           The name of the WordPress filter that is being deregistered.
	 * @param    object|null    $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       Optional. he priority at which the function should be fired. Default is 10.
	 */
	public function remove_action( string $hook, ?object $component, string $callback, int $priority = 10 ): void {
		parent::remove_action( $hook, $component, $callback, $priority );

		if ( empty( $component ) ) {
			\remove_action( $hook, $callback, $priority );
		} else {
			\remove_action( $hook, array( $component, $callback, $priority ) );
		}
	}

	/**
	 * Removes all registered actions from WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_actions(): void {
		foreach ( $this->actions as $action ) {
			$this->remove_action( $action['hook'], $action['component'], $action['callback'], $action['priority'] );
		}

		parent::remove_all_actions();
	}

	/**
	 * Registers a new filter with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string         $hook           The name of the WordPress action that is being registered.
	 * @param    object|null    $component      A reference to the instance of the object on which the action is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int            $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		parent::add_filter( $hook, $component, $callback, $priority, $accepted_args );

		if ( empty( $component ) ) {
			\add_filter( $hook, $callback, $priority, $accepted_args );
		} else {
			\add_filter( $hook, array( $component, $callback ), $priority, $accepted_args );
		}
	}

	/**
	 * Removes a filter registered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string         $hook           The name of the WordPress filter that is being deregistered.
	 * @param    object|null    $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       Optional. he priority at which the function should be fired. Default is 10.
	 */
	public function remove_filter( string $hook, ?object $component, string $callback, int $priority = 10 ): void {
		parent::remove_filter( $hook, $component, $callback, $priority );

		if ( empty( $component ) ) {
			\remove_filter( $hook, $callback, $priority );
		} else {
			\remove_filter( $hook, array( $component, $callback, $priority ) );
		}
	}

	/**
	 * Removes all registered filters from WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_filters(): void {
		foreach ( $this->filters as $filter ) {
			$this->remove_filter( $filter['hook'], $filter['component'], $filter['callback'], $filter['priority'] );
		}

		parent::remove_all_filters();
	}

	// endregion
}
