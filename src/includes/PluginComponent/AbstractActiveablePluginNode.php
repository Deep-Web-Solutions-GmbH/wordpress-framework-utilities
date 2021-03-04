<?php

namespace DeepWebSolutions\Framework\Utilities\PluginComponent;

use DeepWebSolutions\Framework\Foundations\States\Activeable\ActiveableTrait;
use DeepWebSolutions\Framework\Foundations\States\ActiveableInterface;
use DeepWebSolutions\Framework\Foundations\States\Disableable\DisableableTrait;
use DeepWebSolutions\Framework\Foundations\States\DisableableInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Template for encapsulating some of the most often required abilities of a tree-like, activeable plugin component.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Core\PluginComponents
 */
abstract class AbstractActiveablePluginNode extends AbstractPluginNode implements ActiveableInterface, DisableableInterface {
	// region TRAITS

	use ActiveableTrait { is_active as is_active_trait; }
	use DisableableTrait { is_disabled as is_disabled_trait; }

	// endregion

	// region INHERITED METHODS

	/**
	 * Checks whether the current node is active, and also all of its ancestors.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     ActiveableInterface::is_active()
	 * @see     ActiveableTrait::is_active()
	 *
	 * @return  bool
	 */
	public function is_active(): bool {
		if ( is_null( $this->is_active ) ) {
			$this->is_active = ( $this->has_parent() && $this->get_parent()->is_active() )
				? true
				: $this->is_active_trait();
		}

		return $this->is_active;
	}

	/**
	 * Checks whether the current node is disabled, and also all of its ancestors.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     DisableableInterface::is_disabled()
	 * @see     DisableableTrait::is_disabled()
	 *
	 * @return  bool
	 */
	public function is_disabled(): bool {
		if ( is_null( $this->is_disabled ) ) {
			$this->is_disabled = ( $this->has_parent() && $this->get_parent()->is_disabled() )
				? true
				: $this->is_disabled_trait();
		}

		return $this->is_disabled;
	}

	// endregion
}
