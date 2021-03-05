<?php

namespace DeepWebSolutions\Framework\Utilities\PluginComponent;

use DeepWebSolutions\Framework\Foundations\Hierarchy\NodeInterface;
use DeepWebSolutions\Framework\Foundations\Hierarchy\NodeTrait;
use DeepWebSolutions\Framework\Foundations\Plugin\PluginInterface;
use LogicException;
use Psr\Log\LogLevel;

defined( 'ABSPATH' ) || exit;

/**
 * Template for encapsulating some of the most often required abilities of a tree-like plugin component.
 *
 * @since   1.0.0
 * @version 1.0.0
 * @author  Antonius Hegyes <a.hegyes@deep-web-solutions.com>
 * @package DeepWebSolutions\WP-Framework\Core\PluginComponents
 */
abstract class AbstractPluginNode extends AbstractPluginComponent implements NodeInterface {
	// region TRAITS

	use NodeTrait;

	// endregion

	// region INHERITED METHODS

	/**
	 * Returns the plugin instance that the current node belongs to.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @noinspection PhpDocMissingThrowsInspection
	 * @throws  LogicException      Thrown if the node does NOT belong to a plugin tree.
	 *
	 * @return  PluginInterface
	 */
	public function get_plugin(): PluginInterface {
		$plugin = $this->get_closest( PluginInterface::class );
		if ( $plugin instanceof PluginInterface ) {
			return $plugin;
		}

		/* @noinspection PhpUnhandledExceptionInspection */
		throw $this->log_event_and_return_exception(
			LogLevel::ERROR,
			sprintf(
				'Could not find plugin root from within node. Node name: %s',
				$this->get_instance_name()
			),
			LogicException::class,
			null,
			'framework'
		);
	}

	/**
	 * Sets the protected plugin variable.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 *
	 * @param   PluginInterface|null    $plugin     NOT USED BY THIS IMPLEMENTATION.
	 */
	public function set_plugin( ?PluginInterface $plugin = null ) {
		$this->plugin = $this->get_plugin();
	}

	// endregion

	// region METHODS

	/**
	 * Method inspired by jQuery's 'closest' for getting the first parent node that is an instance of a given class.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @param   string  $class  The name of the class of the searched-for parent node.
	 *
	 * @return  NodeInterface|null
	 */
	public function get_closest( string $class ): ?NodeInterface {
		if ( is_a( $this, $class ) || ! $this->has_parent() ) {
			return null;
		}

		$current = $this;
		do {
			$current = $current->get_parent();
		} while ( $current->has_parent() && ! is_a( $current, $class ) );

		return is_a( $current, $class ) ? $current : null;
	}

	// endregion
}
