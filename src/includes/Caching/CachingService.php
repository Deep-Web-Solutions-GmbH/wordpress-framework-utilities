<?php

namespace DeepWebSolutions\Framework\Utilities\Caching;

use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractMultiHandlerService;
use DeepWebSolutions\Framework\Utilities\Caching\Handlers\ObjectCacheHandler;
use DeepWebSolutions\Framework\Utilities\Caching\Handlers\TransientCachingHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and WordPress' API for caching.
 *
 * @see     https://core.trac.wordpress.org/ticket/4476#comment:10
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Caching
 */
class CachingService extends AbstractMultiHandlerService {
	// region INHERITED METHODS

	/**
	 * Returns the instance of a given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handler_id     The ID of the handler to retrieve.
	 *
	 * @return  CachingHandlerInterface
	 */
	public function get_handler( string $handler_id ): ?CachingHandlerInterface { // phpcs:ignore
		/* @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::get_handler( $handler_id );
	}

	// endregion

	// region METHODS

	/**
	 * Returns a cached value from the given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $key            The name of the cached value.
	 * @param   bool|null   $found          Whether the value was found or not. Disambiguates between a stored value of false and simply failure.
	 * @param   string      $handler_id     ID of the handler to return the value from.
	 *
	 * @return  mixed
	 */
	public function get_value( string $key, ?bool $found = null, string $handler_id = 'object' ) {
		return $this->get_handler( $handler_id )->get_value( $key, $found );
	}

	/**
	 * Sets a cached value under a given name.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key            The name of the cached value.
	 * @param   mixed   $value          The data to cache.
	 * @param   int     $expire         When the data should expire. Default never.
	 * @param   string  $handler_id     ID of the handler to set the value with.
	 *
	 * @return  bool
	 */
	public function set_value( string $key, $value, int $expire = 0, string $handler_id = 'object' ): bool {
		return $this->get_handler( $handler_id )->set_value( $key, $value, $expire );
	}

	/**
	 * Deletes a cached value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key            The name of the cached value.
	 * @param   string  $handler_id     ID of the handler to delete the value from.
	 *
	 * @return  bool
	 */
	public function delete_value( string $key, string $handler_id = 'object' ): bool {
		return $this->get_handler( $handler_id )->delete_value( $key );
	}

	/**
	 * Deletes all the cached values using the given handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $handler_id     ID of the handler to delete the values from.
	 *
	 * @return  bool
	 */
	public function delete_all_values( string $handler_id = 'object' ): bool {
		return $this->get_handler( $handler_id )->delete_all_values();
	}

	// endregion

	// region HELPERS

	/**
	 * Returns the class name of the default handlers.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  array
	 */
	protected function get_default_handlers_classes(): array {
		return array( ObjectCacheHandler::class, TransientCachingHandler::class );
	}

	/**
	 * Returns the class name of the used handler for better type-checking.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	protected function get_handler_class(): string {
		return CachingHandlerInterface::class;
	}

	// endregion
}
