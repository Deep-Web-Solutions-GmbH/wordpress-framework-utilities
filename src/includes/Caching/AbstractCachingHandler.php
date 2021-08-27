<?php

namespace DeepWebSolutions\Framework\Utilities\Caching;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareInterface;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginAwareTrait;
use DeepWebSolutions\Framework\Foundations\Utilities\Handlers\AbstractHandler;

\defined( 'ABSPATH' ) || exit;

/**
 * Template for encapsulating some of the most often needed functionality of a caching handler.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\Caching
 */
abstract class AbstractCachingHandler extends AbstractHandler implements CachingHandlerInterface, PluginAwareInterface {
	// region TRAITS

	use PluginAwareTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the type of the handler.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  string
	 */
	public function get_type(): string {
		return 'caching';
	}

	// endregion
}
