<?php

namespace DeepWebSolutions\Framework\Utilities\Assets;

use DeepWebSolutions\Framework\Foundations\Actions\RunnableInterface;

\defined( 'ABSPATH' ) || exit;

/**
 * Describes an object that registers and/or enqueues assets.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Assets
 */
interface AssetsHandlerInterface extends RunnableInterface {
	/**
	 * Should return a unique name for the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_name(): string;
}
