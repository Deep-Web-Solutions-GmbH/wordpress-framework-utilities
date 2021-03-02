<?php

namespace DeepWebSolutions\Framework\Utilities\AdminNotices\Notices;

use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Definition of a regular notice.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Utilities\AdminNotices\Notices
 */
class Notice extends AbstractNotice {
	// region METHODS

	/**
	 * Checks whether the notice should be outputted or not.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @return  bool    Whether the notice should be outputted or not.
	 */
	public function should_output(): bool {
		if ( isset( $this->args['capability'] ) && is_string( $this->args['capability'] ) ) {
			return current_user_can( $this->args['capability'] );
		}

		return true;
	}

	/**
	 * Outputs the notice.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   PluginInterface     $plugin     Instance of the plugin.
	 * @param   string              $store      Store that the notice is stored in.
	 */
	public function output( PluginInterface $plugin, string $store ): void {
		if ( ! $this->should_output() ) {
			return;
		}

		$args = wp_parse_args( $this->args, array( 'html' => false ) );

		echo sprintf(
			'<div id="%1$s" data-handle="%1$s" data-store="%2$s" data-plugin-slug="%3$s" class="%4$s">%5$s</div>',
			esc_attr( $this->get_handle() ),
			esc_attr( $store ),
			esc_attr( $plugin->get_plugin_slug() ),
			esc_attr( implode( ' ', $this->get_classes( $plugin ) ) ),
			wp_kses_post( $args['html'] ? "<p>{$this->message}</p>" : $this->message )
		);
	}

	// endregion
}
