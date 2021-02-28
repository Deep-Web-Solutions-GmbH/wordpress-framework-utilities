<?php

namespace DeepWebSolutions\Framework\Utilities\Handlers;

use DeepWebSolutions\Framework\Utilities\Interfaces\Actions\Exceptions\ResetFailure;
use DeepWebSolutions\Framework\Utilities\Interfaces\Actions\Exceptions\RunFailure;
use DeepWebSolutions\Framework\Utilities\Interfaces\Actions\RunnableInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Modified version of the Loader handler that differs by keeping the hooks registered only within a certain scope
 * delimited by certain start and end hooks, respectively.
 *
 * @see     https://github.com/andykeith/barn2-lib/blob/master/lib/class-wp-scoped-hooks.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Handlers
 */
class HooksHandlerScoped extends HooksHandler implements RunnableInterface {
	// region FIELDS

	/**
	 * The hook on which the actions and filters should be registered.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array   $start
	 */
	protected array $start;

	/**
	 * The hook on which the actions and filters should be un-registered.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     array   $end
	 */
	protected array $end;

	/**
	 * Whether the current instance has been run already or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     bool
	 */
	protected bool $is_run;

	// endregion

	// region MAGIC METHODS

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $start  The hook on which the actions and filters should be registered.
	 * @param   array   $end    The hook on which the actions and filters should be un-registered.
	 */
	protected function __construct( array $start = array(), array $end = array() ) {
		$this->parse_scope( $start, $end );
		$this->initialize();
	}

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailure|null
	 */
	public function run(): ?RunFailure {
		if ( false === $this->is_run ) {
			if ( is_string( $this->start['hook'] ) && ! empty( $this->start['hook'] ) ) {
				if ( 'action' === $this->start['type'] ) {
					$this->array_walk_add_action( $this->start );
				} else {
					$this->array_walk_add_filter( $this->start );
				}
			}
			if ( is_string( $this->end['hook'] ) && ! empty( $this->end['hook'] ) ) {
				if ( 'action' === $this->end['type'] ) {
					$this->array_walk_add_action( $this->end );
				} else {
					$this->array_walk_add_filter( $this->end );
				}
			}

			$this->is_run = true;
		} else {
			array_walk( $this->filters['added'], array( $this, 'array_walk_add_filter' ) );
			$this->filters['removed'] = array_filter( $this->filters['removed'], array( $this, 'array_walk_remove_filter' ) );

			array_walk( $this->actions['added'], array( $this, 'array_walk_add_action' ) );
			$this->actions['removed'] = array_filter( $this->actions['removed'], array( $this, 'array_walk_remove_action' ) );
		}

		return null;
	}

	/**
	 * Undo what the 'run' function did above.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailure|null
	 */
	public function reset(): ?ResetFailure {
		array_walk( $this->filters['added'], array( $this, 'array_walk_remove_filter' ) );
		array_walk( $this->filters['removed'], array( $this, 'array_walk_add_filter' ) );

		array_walk( $this->actions['added'], array( $this, 'array_walk_remove_action' ) );
		array_walk( $this->actions['removed'], array( $this, 'array_walk_add_action' ) );

		$this->initialize();
		return null;
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
		$this->actions['added'] = $this->add( $this->actions['added'], $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove an action from the collection to be registered with WordPress.
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
	public function remove_added_action( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions['added'] = $this->remove( $this->actions['added'], $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new action to the collection to be unregistered with WordPress.
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
	public function remove_action( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions['removed'] = $this->add( $this->actions['removed'], $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove an action from the collection to be unregistered with WordPress.
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
	public function remove_removed_action( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->actions['removed'] = $this->remove( $this->actions['removed'], $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be unregistered with WordPress.
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
		$this->filters['added'] = $this->add( $this->filters['added'], $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove a filter from the collection to be registered with WordPress.
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
	public function remove_added_filter( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters['added'] = $this->remove( $this->filters['added'], $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be unregistered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string         $hook           The name of the WordPress filter that is being registered.
	 * @param    object|null    $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int            $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function remove_filter( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters['removed'] = $this->add( $this->filters['removed'], $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove a filter to the collection to be unregistered with WordPress.
	 *
	 * @since    1.0.0
	 * @version  1.0.0
	 *
	 * @param    string         $hook           The name of the WordPress filter that is being registered.
	 * @param    object|null    $component      A reference to the instance of the object on which the filter is defined.
	 * @param    string         $callback       The name of the function definition on the $component.
	 * @param    int            $priority       Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int            $accepted_args  Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function remove_removed_filter( string $hook, ?object $component, string $callback, int $priority = 10, int $accepted_args = 1 ): void {
		$this->filters['removed'] = $this->remove( $this->filters['removed'], $hook, $component, $callback, $priority, $accepted_args );
	}

	// endregion

	// region HELPERS

	/**
	 * Sets the scoped loader into a valid state.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	protected function initialize(): void {
		$this->actions = array(
			'added'   => array(),
			'removed' => array(),
		);
		$this->filters = array(
			'added'   => array(),
			'removed' => array(),
		);
		$this->is_run  = false;

		if ( isset( $this->start['hook'] ) && is_string( $this->start['hook'] ) && ! empty( $this->start['hook'] ) ) {
			if ( 'action' === $this->start['type'] ) {
				$this->array_walk_remove_action( $this->start );
			} else {
				$this->array_walk_remove_filter( $this->start );
			}
		}
		if ( isset( $this->end['hook'] ) && is_string( $this->end['hook'] ) && ! empty( $this->end['hook'] ) ) {
			if ( 'action' === $this->end['type'] ) {
				$this->array_walk_remove_action( $this->end );
			} else {
				$this->array_walk_remove_filter( $this->end );
			}
		}
	}

	/**
	 * Parses the start and end hooks parameters.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $start  The hook on which the actions and filters should be registered.
	 * @param   array   $end    The hook on which the actions and filters should be un-registered.
	 */
	protected function parse_scope( array $start, array $end ): void {
		$this->start = array_merge(
			wp_parse_args( $start, self::get_scope_hook_defaults() ),
			array(
				'component'     => $this,
				'callback'      => 'run',
				'accepted_args' => 0,
			)
		);
		$this->end   = array_merge(
			wp_parse_args( $end, self::get_scope_hook_defaults() ),
			array(
				'component'     => $this,
				'callback'      => 'reset',
				'accepted_args' => 0,
			)
		);
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
			return add_filter( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		} else {
			return add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
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
			return remove_filter( $hook['hook'], $hook['callback'], $hook['priority'] );
		} else {
			return remove_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'] );
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
			return add_action( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		} else {
			return add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
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
			return remove_action( $hook['hook'], $hook['callback'], $hook['priority'] );
		} else {
			return remove_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'] );
		}
	}

	/**
	 * Gets a default hook configuration.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	protected static function get_scope_hook_defaults(): array {
		return array(
			'hook'     => '',
			'type'     => 'action',
			'priority' => 10,
		);
	}

	// endregion
}
