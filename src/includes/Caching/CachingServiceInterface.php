<?php

namespace DeepWebSolutions\Framework\Utilities\Caching;

use DeepWebSolutions\Framework\Foundations\Exceptions\NotFoundException;
use DeepWebSolutions\Framework\Foundations\Services\ServiceInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes an instance of a cache service.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Caching
 */
interface CachingServiceInterface extends ServiceInterface {
	/**
	 * Returns a cached value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $key    The name of the cached value.
	 *
	 * @return  mixed|NotFoundException
	 */
	public function get_value( string $key );

	/**
	 * Sets a cached value under a given name.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The name of the cached value.
	 * @param   mixed   $value  The data to cache.
	 * @param   int     $expire When the data should expire. Default never.
	 *
	 * @return  bool
	 */
	public function set_value( string $key, $value, int $expire = 0 ): bool;

	/**
	 * Deletes a cached value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    The name of the cached value.
	 *
	 * @return  bool
	 */
	public function delete_value( string $key ): bool;

	/**
	 * Deletes all the cached values.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function delete_all_values(): bool;
}
