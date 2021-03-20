<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks;

use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResettableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\Resettable\ResetFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\ResettableInterface;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractService;
use DeepWebSolutions\Framework\Utilities\Hooks\Handlers\HooksHandler;
use DeepWebSolutions\Framework\Utilities\Hooks\Handlers\HooksHandlerAwareInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\Handlers\HooksHandlerAwareTrait;
use Psr\Log\LogLevel;

\defined( 'ABSPATH' ) || exit;

/**
 * A wrapper around a singleton hooks handler instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks
 */
class HooksService extends AbstractService implements HooksHandlerAwareInterface, RunnableInterface, ResettableInterface {
	// region TRAITS

	use HooksHandlerAwareTrait;
	use RunnableTrait;
	use ResettableTrait;

	// endregion

	// region MAGIC METHODS

	/**
	 * HooksService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface     $plugin             Instance of the plugin.
	 * @param   LoggingService      $logging_service    Instance of the logging service.
	 * @param   HooksHandler        $hooks_handler      Instance of the hooks handler.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, HooksHandler $hooks_handler ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );
		$this->set_hooks_handler( $hooks_handler );
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
		if ( \is_null( $this->get_hooks_handler()->is_run() ) ) {
			$this->run_result   = $this->get_hooks_handler()->run();
			$this->is_run       = \is_null( $this->run_result );
			$this->reset_result = $this->is_reset = null; // phpcs:ignore
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The hooks service has been ran already. Please reset it before running it again.',
				'1.0.0',
				RunFailureException::class,
				null,
				LogLevel::NOTICE,
				'framework'
			);
		}

		if ( $this->run_result instanceof RunFailureException ) {
			$this->log_event( LogLevel::ERROR, $this->run_result->getMessage(), 'framework' );
		}

		return $this->run_result;
	}

	/**
	 * Un-registers the filters and actions with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  ResetFailureException|null
	 */
	public function reset(): ?ResetFailureException {
		if ( \is_null( $this->get_hooks_handler()->is_reset() ) ) {
			$this->reset_result = $this->get_hooks_handler()->reset();
			$this->is_reset     = \is_null( $this->reset_result );
			$this->is_run       = $this->run_result = null; // phpcs:ignore
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The hooks service has been reset already. Please run it before resetting it again.',
				'1.0.0',
				ResetFailureException::class,
				null,
				LogLevel::NOTICE,
				'framework'
			);
		}

		if ( $this->reset_result instanceof ResetFailureException ) {
			$this->log_event( LogLevel::ERROR, $this->reset_result->getMessage(), 'framework' );
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
		$this->get_hooks_handler()->add_action( $hook, $component, $callback, $priority, $accepted_args );
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
		$this->get_hooks_handler()->remove_action( $hook, $component, $callback, $priority );
	}

	/**
	 * Removes all actions from the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_actions(): void {
		$this->get_hooks_handler()->remove_all_actions();
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
		$this->get_hooks_handler()->add_filter( $hook, $component, $callback, $priority, $accepted_args );
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
		$this->get_hooks_handler()->remove_filter( $hook, $component, $callback, $priority );
	}

	/**
	 * Removes all filters from the collection to be registered with WordPress.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 */
	public function remove_all_filters(): void {
		$this->get_hooks_handler()->remove_all_filters();
	}

	// endregion
}
