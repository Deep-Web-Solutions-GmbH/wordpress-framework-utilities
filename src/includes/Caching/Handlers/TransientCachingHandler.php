<?php

namespace DeepWebSolutions\Framework\Utilities\Caching\Handlers;

use DeepWebSolutions\Framework\Helpers\DataTypes\Integers;
use DeepWebSolutions\Framework\Utilities\Caching\AbstractCachingHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Compatibility layer between the framework and the WP Transient Cache.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Caching\Handlers
 */
class TransientCachingHandler extends AbstractCachingHandler {
	// region FIELDS AND CONSTANTS

	/**
	 * The prefix that should be added to all the data.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @var     string|null
	 */
	protected ?string $prefix = null;

	// endregion

	// region MAGIC METHODS

	/**
	 * TransientCachingHandler constructor.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string          $handler_id     The default ID of the handler.
	 * @param   string|null     $prefix         The prefix for all values. Defaults to the plugin's safe slug.
	 */
	public function __construct( string $handler_id = 'transient', ?string $prefix = null ) {
		parent::__construct( $handler_id );
		$this->prefix = $prefix;
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
	public function get_keys_prefix(): string {
		return $this->prefix ?: $this->get_plugin()->get_plugin_safe_slug(); // phpcs:ignore
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

		$suffix = \get_option( $suffix_key, false );
		if ( false === $suffix ) {
			\update_option( $suffix_key, 1, true );
		}

		return Integers::maybe_cast( $suffix, 1 );
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
		return "{$this->get_keys_prefix()}_invalidation_suffix_key";
	}

	// endregion

	// region METHODS

	/**
	 * Returns a value from the transient cache.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string      $key    Transient name. Expected to not be SQL-escaped.
	 * @param   bool|null   $found  Whether the value was found or not. Disambiguates between a stored value of false and simply failure.
	 *
	 * @return  mixed
	 */
	public function get_value( string $key, ?bool &$found = null ) {
		$key = "{$this->get_keys_prefix()}/{$key}__{$this->get_keys_suffix()}";

		$value = \get_transient( $key );
		$found = ( false !== $value );

		return $value;
	}

	/**
	 * Sets a transient value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key        Transient name. Expected to not be SQL-escaped. Must be 172 characters or fewer in length.
	 * @param   mixed   $value      Transient value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @param   int     $expire     Time until expiration in seconds. Default 0 (no expiration).
	 *
	 * @return  bool
	 */
	public function set_value( string $key, $value, int $expire = 0 ): bool {
		$key = "{$this->get_keys_prefix()}/{$key}__{$this->get_keys_suffix()}";
		return \set_transient( $key, $value, $expire );
	}

	/**
	 * Deletes a transient value.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $key    Transient name. Expected to not be SQL-escaped.
	 *
	 * @return  bool
	 */
	public function delete_value( string $key ): bool {
		$key = "{$this->get_keys_prefix()}/{$key}__{$this->get_keys_suffix()}";
		return \delete_transient( $key );
	}

	/**
	 * Deletes all the transients set by this handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool
	 */
	public function delete_all_values(): bool {
		foreach ( $this->get_all_keys() as $full_key ) {
			// If stored in the database, this will delete them.
			// If stored in an external cache, incrementing the suffix will let the garbage collector deal with them.
			\delete_transient( $full_key );
		}

		return \update_option( $this->get_keys_suffix_key(), $this->get_keys_suffix() + 1, true );
	}

	// endregion

	// region HELPERS

	/**
	 * Returns all the transient names from the database of transients that have been inserted by this handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     https://kellenmace.com/delete-transients-with-prefix-in-wordpress/
	 *
	 * @return  array
	 */
	protected function get_all_keys(): array {
		global $wpdb;

		$prefix = $wpdb->esc_like( '_transient_' . $this->get_keys_prefix() . '/' );
		$sql    = "SELECT `option_name` FROM $wpdb->options WHERE `option_name` LIKE '%s'";
		$keys   = $wpdb->get_results( $wpdb->prepare( $sql, $prefix . '%' ), ARRAY_A ); // phpcs:ignore

		$keys = \is_wp_error( $keys ) ? array() : $keys;

		// Remove '_transient_' from the option name.
		return \array_map( fn( array $key ) => ltrim( $key['option_name'], '_transient_' ), $keys );
	}

	// endregion
}
