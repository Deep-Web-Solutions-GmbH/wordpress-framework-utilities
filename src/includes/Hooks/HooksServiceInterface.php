<?php

namespace DeepWebSolutions\Framework\Utilities\Hooks;

use DeepWebSolutions\Framework\Foundations\Services\ServiceInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes an instance of a hooks service.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Hooks
 */
interface HooksServiceInterface extends ServiceInterface, HooksAdapterInterface {
	/* empty on purpose */
}
