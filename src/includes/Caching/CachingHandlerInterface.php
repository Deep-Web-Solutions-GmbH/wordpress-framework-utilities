<?php

namespace DeepWebSolutions\Framework\Utilities\Caching;

use DeepWebSolutions\Framework\Foundations\Services\HandlerInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes an instance of a cache handler compatible with the cache service.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Caching
 */
interface CachingHandlerInterface extends HandlerInterface, CachingServiceInterface {
	/* empty on purpose */
}
