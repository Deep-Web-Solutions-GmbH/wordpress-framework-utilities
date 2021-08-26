<?php

namespace DeepWebSolutions\Framework\Utilities\Caching;

\defined( 'ABSPATH' ) || exit;

/**
 * Basic implementation of the caching-service-aware interface.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Caching
 */
trait CachingServiceAwareTrait {
	// region FIELDS AND CONSTANTS

	/**
	 * Caching service instance.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @access  protected
	 * @var     CachingService
	 */
	protected CachingService $caching_service;

	// endregion

	// region GETTERS

	/**
	 * Gets the current caching service instance set on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  CachingService
	 */
	public function get_caching_service(): CachingService {
		return $this->caching_service;
	}

	// endregion

	// region SETTERS

	/**
	 * Sets a caching service instance on the object.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   CachingService       $service        Caching service instance to use from now on.
	 */
	public function set_caching_service( CachingService $service ) {
		$this->caching_service = $service;
	}

	// endregion

	// region METHODS

	/**
	 * Wrapper around the service's own method.
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
		return $this->get_caching_service()->get_cache_value( $key, $force, $found );
	}

	/**
	 * Wrapper around the service's own method.
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
		return $this->get_caching_service()->set_cache_value( $key, $data, $expire );
	}

	// endregion
}
