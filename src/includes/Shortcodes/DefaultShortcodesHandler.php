<?php

namespace DeepWebSolutions\Framework\Utilities\Shortcodes;

use DeepWebSolutions\Framework\Foundations\Actions\{ ResettableInterface, RunnableInterface };
use DeepWebSolutions\Framework\Foundations\Actions\Resettable\{ ResetFailureException, ResettableTrait };
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\{ RunFailureException, RunnableTrait };

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for shortcodes.
 *
 * Maintain a list of all shortcodes that are registered throughout the plugin, and handles their registration with
 * the WordPress API after calling the run function.
 *
 * @see     https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/blob/master/plugin-name/includes/class-plugin-name-loader.php
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Shortcodes
 */
class DefaultShortcodesHandler extends AbstractShortcodesHandler implements RunnableInterface, ResettableInterface {
	// region TRAITS

	use RunnableTrait;
	use ResettableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * The shortcodes registered with WordPress that can be used after the service runs.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     array
	 */
	protected array $shortcodes = array();

	// endregion

	// region INHERITED FUNCTIONS

	/**
	 * Register the shortcodes with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( \is_null( $this->is_run ) ) {
			foreach ( $this->shortcodes as $hook ) {
				if ( empty( $hook['component'] ) ) {
					\add_shortcode( $hook['tag'], $hook['callback'] );
				} else {
					\add_shortcode( $hook['tag'], array( $hook['component'], $hook['callback'] ) );
				}
			}

			$this->is_run     = true;
			$this->run_result = $this->reset_result = $this->is_reset = null; // phpcs:ignore
		}

		return $this->run_result;
	}

	/**
	 * Un-registers the shortcodes with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailureException|null
	 */
	public function reset(): ?ResetFailureException {
		if ( \is_null( $this->is_reset ) ) {
			foreach ( $this->shortcodes as $shortcode ) {
				\remove_shortcode( $shortcode );
			}

			$this->is_reset     = true;
			$this->reset_result = $this->is_run = $this->run_result = null; // phpcs:ignore
		}

		return $this->reset_result;
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of shortcodes registered with WP by this service instance on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	public function get_shortcodes(): array {
		return $this->shortcodes;
	}

	// endregion

	// region METHODS

	/**
	 * Adds a new shortcode to the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $tag            The name of the WordPress shortcode that is being registered.
	 * @param   object|null     $component      A reference to the instance of the object on which the shortcode is defined.
	 * @param   string          $callback       The name of the function definition on the $component.
	 */
	public function add_shortcode( string $tag, ?object $component, string $callback ): void {
		$this->shortcodes[] = array(
			'tag'       => $tag,
			'component' => $component,
			'callback'  => $callback,
		);
	}

	/**
	 * Removes a shortcode from the collection to be registered with WordPress.
	 *
	 * @param   string          $tag            The name of the WordPress shortcode that is being deregistered.
	 * @param   object|null     $component      A reference to the instance of the object on which the shortcode is defined.
	 * @param   string          $callback       The name of the function definition on the $component.
	 */
	public function remove_shortcode( string $tag, ?object $component, string $callback ): void {
		foreach ( $this->shortcodes as $index => $hook_info ) {
			if ( $hook_info['tag'] === $tag && $hook_info['component'] === $component && $hook_info['callback'] === $callback ) {
				unset( $this->shortcodes[ $index ] );
				break;
			}
		}
	}

	/**
	 * Removes all shortcodes from the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_shortcodes(): void {
		$this->shortcodes = array();
	}

	// endregion
}
