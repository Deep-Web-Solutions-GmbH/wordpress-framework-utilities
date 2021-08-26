<?php

namespace DeepWebSolutions\Framework\Utilities\Caching;

use DeepWebSolutions\Framework\Foundations\Utilities\Services\AbstractService;
use DeepWebSolutions\Framework\Helpers\DataTypes\Integers;

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
class CachingService extends AbstractService {
	// region METHODS

	/**
	 * Returns the group that the cache contents should be added/read/deleted to/from.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_caching_group(): string {
		return $this->get_plugin()->get_plugin_safe_slug();
	}

	/**
	 * Returns the key of the cache entry holding the cache invalidation suffix.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_cache_keys_suffix_key(): string {
		return "{$this->get_caching_group()}_invalidation_suffix_key";
	}

	/**
	 * Returns the cache invalidation suffix.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  int
	 */
	public function get_cache_keys_suffix(): int {
		$suffix_key = $this->get_cache_keys_suffix_key();

		$suffix = \wp_cache_get( $suffix_key );
		if ( false === $suffix ) {
			\wp_cache_set( $suffix, 1 );
		}

		return Integers::maybe_cast( $suffix, 1 );
	}

	/**
	 * Invalidates all the cached entries belonging to the current plugin's group.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function clear_cache(): bool {
		return ! ( ( false === \wp_cache_incr( $this->get_cache_keys_suffix_key() ) ) );
	}

	/**
	 * Returns a cache value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string      $key        The key under which the cache contents are stored.
	 * @param   bool        $force      Whether to force an update of the local cache from the persistent cache.
	 * @param   bool|null   $found      Whether the key was found in the cache (passed by reference). Disambiguates a return of false, a storable value.
	 *
	 * @return  false|mixed
	 */
	public function get_cache_value( string $key, bool $force = false, ?bool &$found = null ) {
		$key .= "__{$this->get_cache_keys_suffix()}";
		return \wp_cache_get( $key, $this->get_plugin()->get_plugin_safe_slug(), $force, $found );
	}

	/**
	 * Adds data to the cache. If the key already exists, it overrides the existing data.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key        The cache key to use for retrieval later.
	 * @param   mixed   $data       The contents to store in the cache.
	 * @param   int     $expire     When to expire the cache contents, in seconds. Default 0 (no expiration).
	 *
	 * @return  bool    True on success, false on failure.
	 */
	public function set_cache_value( string $key, $data, int $expire = 0 ): bool {
		$key .= "__{$this->get_cache_keys_suffix()}";
		return \wp_cache_set( $key, $data, $this->get_caching_group(), $expire );
	}

	/**
	 * Replaces the contents of the cache with new data.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key        The key for the cache data that should be replaced.
	 * @param   mixed   $data       The new data to store in the cache.
	 * @param   int     $expire     When to expire the cache contents, in seconds. Default 0 (no expiration).
	 *
	 * @return  bool    False if original value does not exist, true if contents were replaced.
	 */
	public function replace_cache_value( string $key, $data, int $expire = 0 ): bool {
		$key .= "__{$this->get_cache_keys_suffix()}";
		return \wp_cache_replace( $key, $data, $this->get_caching_group(), $expire );
	}

	/**
	 * Adds data to the cache, if the cache key doesn't already exist.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key        The key for the cache data that should be replaced.
	 * @param   mixed   $data       The new data to store in the cache.
	 * @param   int     $expire     When to expire the cache contents, in seconds. Default 0 (no expiration).
	 *
	 * @return bool     True on success, false if cache key already exists.
	 */
	public function add_cache_value( string $key, $data, int $expire = 0 ): bool {
		$key .= "__{$this->get_cache_keys_suffix()}";
		return \wp_cache_add( $key, $data, $this->get_caching_group(), $expire );
	}

	/**
	 * Removes the cache contents matching key.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    What the contents in the cache are called.
	 *
	 * @return  bool    True on successful removal, false on failure.
	 */
	public function delete_cache_value( string $key ): bool {
		$key .= "__{$this->get_cache_keys_suffix()}";
		return \wp_cache_delete( $key, $this->get_caching_group() );
	}

	// endregion
}
