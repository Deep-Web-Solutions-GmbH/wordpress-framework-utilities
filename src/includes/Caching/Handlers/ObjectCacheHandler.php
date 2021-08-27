<?php

namespace DeepWebSolutions\Framework\Utilities\Caching\Handlers;

use DeepWebSolutions\Framework\Helpers\DataTypes\Integers;
use DeepWebSolutions\Framework\Utilities\Caching\AbstractCachingHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and the WP Object Cache.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Caching\Handlers
 */
class ObjectCacheHandler extends AbstractCachingHandler {
	// region FIELDS AND CONSTANTS

	/**
	 * The caching group that the data should be added to.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string|null
	 */
	protected ?string $group = null;

	// endregion

	// region MAGIC METHODS

	/**
	 * ObjectCacheHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $handler_id     The default ID of the handler.
	 * @param   string|null     $group          The group to assign values to. Defaults to the plugin's safe slug.
	 */
	public function __construct( string $handler_id = 'object', ?string $group = null ) {
		parent::__construct( $handler_id );
		$this->group = $group;
	}

	// endregion

	// region GETTERS

	/**
	 * Returns the object cache group.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_cache_group(): string {
		return $this->group ?: $this->get_plugin()->get_plugin_safe_slug(); // phpcs:ignore
	}

	// endregion

	// region METHODS

	/**
	 * Returns a cache value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 *
	 * @param   string      $key        The key under which the cache contents are stored.
	 * @param   bool|null   $found      Whether the key was found in the cache (passed by reference). Disambiguates a return of false, a storable value.
	 * @param   bool        $force      Whether to force an update of the local cache from the persistent cache.
	 *
	 * @return  mixed
	 */
	public function get_value( string $key, ?bool &$found, bool $force = false ) {
		$key .= "__{$this->get_keys_suffix()}";
		return \wp_cache_get( $key, $this->get_cache_group(), $force, $found );
	}

	/**
	 * Adds data to the cache. If the key already exists, it overrides the existing data.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key        The cache key to use for retrieval later.
	 * @param   mixed   $value      The contents to store in the cache.
	 * @param   int     $expire     When to expire the cache contents, in seconds. Default 0 (no expiration).
	 *
	 * @return  bool    True on success, false on failure.
	 */
	public function set_value( string $key, $value, int $expire = 0 ): bool {
		$key .= "__{$this->get_keys_suffix()}";
		return \wp_cache_set( $key, $value, $this->get_cache_group(), $expire );
	}

	/**
	 * Replaces the contents of the cache with new data.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string $key    The key for the cache data that should be replaced.
	 * @param   mixed  $value  The new data to store in the cache.
	 * @param   int    $expire When to expire the cache contents, in seconds. Default 0 (no expiration).
	 *
	 * @return  bool    False if original value does not exist, true if contents were replaced.
	 */
	public function replace_value( string $key, $value, int $expire = 0 ): bool {
		$key .= "__{$this->get_keys_suffix()}";
		return \wp_cache_replace( $key, $value, $this->get_cache_group(), $expire );
	}

	/**
	 * Adds data to the cache, if the cache key doesn't already exist.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string $key    The key for the cache data that should be replaced.
	 * @param   mixed  $value  The new data to store in the cache.
	 * @param   int    $expire When to expire the cache contents, in seconds. Default 0 (no expiration).
	 *
	 * @return bool     True on success, false if cache key already exists.
	 */
	public function add_value( string $key, $value, int $expire = 0 ): bool {
		$key .= "__{$this->get_keys_suffix()}";
		return \wp_cache_add( $key, $value, $this->get_cache_group(), $expire );
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
	public function delete_value( string $key ): bool {
		$key .= "__{$this->get_keys_suffix()}";
		return \wp_cache_delete( $key );
	}

	/**
	 * Invalidates all the cached entries belonging to the current group.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function delete_all_values(): bool {
		return ! ( ( false === \wp_cache_incr( $this->get_keys_suffix_key() ) ) );
	}

	/**
	 * Returns the key of the cache entry holding the cache invalidation suffix.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_keys_suffix_key(): string {
		return "{$this->get_cache_group()}_invalidation_suffix_key";
	}

	/**
	 * Returns the cache invalidation suffix.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  int
	 */
	public function get_keys_suffix(): int {
		$suffix_key = $this->get_keys_suffix_key();

		$suffix = \wp_cache_get( $suffix_key );
		if ( false === $suffix ) {
			\wp_cache_set( $suffix_key, 1 );
		}

		return Integers::maybe_cast( $suffix, 1 );
	}

	// endregion
}
