<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks\Handlers;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResetFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResettableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\ResettableInterface;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\PluginUtilities\Handlers\HandlerInterface;

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
class HooksHandler implements HandlerInterface, RunnableInterface, ResettableInterface {
	// region TRAITS

	use RunnableTrait;
	use ResettableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * The actions registered with WordPress to fire when the service runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $actions = array();

	/**
	 * The filters registered with WordPress to fire when the service runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $filters = array();

	// endregion

	// region GETTERS

	/**
	 * Returns the list of actions registered with WP by this handler instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_actions(): array {
		return $this->actions;
	}

	/**
	 * Returns the list of filters registered with WP by this handler instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_filters(): array {
		return $this->filters;
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
			\array_walk( $this->filters, array( $this, 'array_walk_add_filter' ) );
			\array_walk( $this->actions, array( $this, 'array_walk_add_action' ) );

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
			\array_walk( $this->filters, array( $this, 'array_walk_remove_filter' ) );
			\array_walk( $this->actions, array( $this, 'array_walk_remove_action' ) );

			$this->is_reset     = true;
			$this->reset_result = $this->is_run = $this->run_result = null; // phpcs:ignore
		}

		return $this->reset_result;
	}

	// endregion

	// region METHODS

	/**
	 * Add a new action to the collection to be registered with WordPress.
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
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove an action from the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string         $hook           The name of the WordPress action that is being deregistered.
	 * @param    object|null    $component      A reference to the instance of the object on which the action is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       Optional. he priority at which the function should be fired. Default is 10.
	 */
	public function remove_action( string $hook, ?object $component, string $callback, int $priority = 10 ): void {
		$this->actions = $this->remove( $this->actions, $hook, $component, $callback, $priority );
	}

	/**
	 * Removes all actions from the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_actions(): void {
		$this->actions = array();
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $hook           The name of the WordPress filter that is being registered.
	 * @param   object|null     $component      A reference to the instance of the object on which the filter is defined.
	 * @param   string          $callback       The name of the function definition on the $component.
	 * @param   int             $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param   int             $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove a filter from the collection to be registered with WordPress.
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
		$this->filters = $this->remove( $this->filters, $hook, $component, $callback, $priority );
	}

	/**
	 * Removes all filters from the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_filters(): void {
		$this->filters = array();
	}

	// endregion

	// region HELPERS

	/**
	 * A utility function that is used to register the actions and hooks into a single collection.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @access   protected
	 *
	 * @param    array          $hooks          The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string         $hook           The name of the WordPress filter that is being registered.
	 * @param    object|null    $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       The priority at which the function should be fired.
	 * @param    int            $accepted_args  The number of arguments that should be passed to the $callback.
	 *
	 * @return   array      The collection of actions and filters registered with WordPress.
	 */
	protected function add( array $hooks, string $hook, ?object $component, string $callback, int $priority, int $accepted_args ): array {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;
	}

	/**
	 * A utility function that is used to remove the actions and hooks from the single collection.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @access   protected
	 *
	 * @param    array          $hooks          The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string         $hook           The name of the WordPress filter that is being registered.
	 * @param    object|null    $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       The priority at which the function should be fired.
	 *
	 * @return   array      The collection of actions and filters registered with WordPress.
	 */
	protected function remove( array $hooks, string $hook, ?object $component, string $callback, int $priority ) : array {
		foreach ( $hooks as $index => $hook_info ) {
			if ( $hook_info['hook'] === $hook && $hook_info['component'] === $component && $hook_info['callback'] === $callback && $hook_info['priority'] === $priority ) {
				unset( $hooks[ $index ] );
				break;
			}
		}

		return $hooks;
	}

	/**
	 * Registers a filter with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $hook   Filter to register.
	 *
	 * @return  bool    Whether registration was successful or not.
	 */
	protected function array_walk_add_filter( array $hook ): bool {
		if ( empty( $hook['component'] ) ) {
			return \add_filter( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		} else {
			return \add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}

	/**
	 * Un-registers a filter with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $hook   Filter to un-register.
	 *
	 * @return  bool    Whether un-registration was successful or not.
	 */
	protected function array_walk_remove_filter( array $hook ): bool {
		if ( empty( $hook['component'] ) ) {
			return \remove_filter( $hook['hook'], $hook['callback'], $hook['priority'] );
		} else {
			return \remove_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'] );
		}
	}

	/**
	 * Registers an action with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $hook   Action to register.
	 *
	 * @return  bool    Whether registration was successful or not.
	 */
	protected function array_walk_add_action( array $hook ): bool {
		if ( empty( $hook['component'] ) ) {
			return \add_action( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		} else {
			return \add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}

	/**
	 * Un-registers an action with WP.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $hook   Action to un-register.
	 *
	 * @return  bool    Whether un-registration was successful or not.
	 */
	protected function array_walk_remove_action( array $hook ): bool {
		if ( empty( $hook['component'] ) ) {
			return \remove_action( $hook['hook'], $hook['callback'], $hook['priority'] );
		} else {
			return \remove_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'] );
		}
	}

	// endregion
}
