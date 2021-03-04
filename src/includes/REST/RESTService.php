<?php

namespace DeepWebSolutions\Framework\Utilities\REST;

use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunFailureException;
use DeepWebSolutions\Framework\Foundations\Actions\Runnable\RunnableTrait;
use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksService;
use DeepWebSolutions\Framework\Utilities\Hooks\HooksServiceRegisterTrait;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingService;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareInterface;
use DeepWebSolutions\Framework\Utilities\Logging\LoggingServiceAwareTrait;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' REST API.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Shortcodes
 */
class RESTService implements LoggingServiceAwareInterface, PluginAwareInterface, RunnableInterface {
	// region TRAITS

	use HooksServiceRegisterTrait;
	use LoggingServiceAwareTrait;
	use PluginAwareTrait;
	use RunnableTrait;

	// endregion

	// region FIELDS AND CONSTANTS

	/**
	 * Subscribers to register on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     RESTServiceRegisterInterface[]
	 */
	protected array $subscribers;

	// endregion

	// region MAGIC METHODS

	/**
	 * RESTService constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface                     $plugin             Instance of the plugin.
	 * @param   LoggingService                      $logging_service    Instance of the logging service.
	 * @param   HooksService                        $hooks_service      Instance of the hooks service.
	 * @param   RESTServiceRegisterInterface[]      $subscribers        Subscribers to call the registration method on run.
	 */
	public function __construct( PluginInterface $plugin, LoggingService $logging_service, HooksService $hooks_service, array $subscribers = array() ) {
		$this->set_plugin( $plugin );
		$this->set_logging_service( $logging_service );

		$this->register_hooks( $hooks_service );
		$this->set_subscribers( $subscribers );
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the list of subscribers registered to call on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RESTServiceRegisterInterface[]
	 */
	public function get_subscribers(): array {
		return $this->subscribers;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets the list of subscribers to call on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   array   $subscribers   Collection of handlers to run.
	 *
	 * @return  $this
	 */
	public function set_subscribers( array $subscribers ): RESTService {
		$this->subscribers = array();

		foreach ( $subscribers as $subscriber ) {
			if ( $subscriber instanceof RESTServiceRegisterInterface ) {
				$this->add_subscriber( $subscriber );
			}
		}

		return $this;
	}

	// endregion

	// region INHERITED METHODS

	/**
	 * Registers hooks with the hooks service.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   HooksService    $hooks_service      Instance of the hooks service.
	 */
	public function register_hooks( HooksService $hooks_service ): void {
		$hooks_service->add_action( 'rest_api_init', $this, 'run', PHP_INT_MAX );
	}

	/**
	 * Register the REST configuration.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  RunFailureException|null
	 */
	public function run(): ?RunFailureException {
		if ( is_null( $this->is_run ) ) {
			foreach ( $this->get_subscribers() as $subscriber ) {
				$subscriber->register_rest_config( $this );
			}

			$this->is_run     = true;
			$this->run_result = null;
		} else {
			/* @noinspection PhpIncompatibleReturnTypeInspection */
			return $this->log_event_and_doing_it_wrong_and_return_exception(
				__FUNCTION__,
				'The REST service has already been run.',
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

	// endregion

	// region METHODS

	/**
	 * Adds a subscribers to the list of subscribers to call on run.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   RESTServiceRegisterInterface        $subscriber     Subscriber to add.
	 *
	 * @return  $this
	 */
	public function add_subscriber( RESTServiceRegisterInterface $subscriber ): RESTService {
		$this->subscribers[] = $subscriber;
		return $this;
	}

	// endregion
}
