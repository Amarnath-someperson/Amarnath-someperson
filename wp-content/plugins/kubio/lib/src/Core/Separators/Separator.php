<?php

namespace Kubio\Core\Separators;

use Kubio\Config;
use Kubio\Core\Element;
use Kubio\Core\LodashBasic;
use Kubio\Core\StyleManager\ParserUtils;
use Kubio\Core\Styles\Utils;
use function file_exists;
use function file_get_contents;
use function sanitize_file_name;
use const KUBIO_ROOT_DIR;

class Separator extends Element {

	function __construct( $tag_name, $props, $children, $block ) {
		parent::__construct( $tag_name, $props, $children, $block );

		$position = $this->getProp( 'position' );
		$negative = $this->getProp( 'negative' );

		$enabledByMedia = $this->getProp( 'enabledByMedia' );

		$visibilityPerMedia = $this->getVisibilityPerMedia( $enabledByMedia );

		$style = array(
			'fill'   => $this->getProp( 'color' ),
			'height' => ParserUtils::toValueUnitString( $this->getProp( 'height' ) ),
		);

		if ( ! $this->getProp( 'overlap' ) ) {
			$style['position'] = 'relative';
		}

		$style[ $position ] = 'calc(0px)';

		$type = sanitize_file_name( $this->getProp( 'type' ) );

		$top               = $position === 'top';
		$shouldUseNegative = $negative && file_exists( KUBIO_ROOT_DIR . "lib/shapes/separators/${type}-negative.svg" );

		$supportsNegative = file_exists( KUBIO_ROOT_DIR . "lib/shapes/separators/${type}-negative.svg" );

		if ( ( $shouldUseNegative && $top ) || ( ! $shouldUseNegative && ! $top ) ) {
			$style['transform'] = 'rotateX(180deg)';
		}

		if ( $negative && $supportsNegative ) {
			$type = $type . '-negative';
		}

		$html = file_get_contents( KUBIO_ROOT_DIR . 'lib/shapes/separators/' . $type . '.svg' );
		$this->extendProps(
			array(
				'className' => array_merge( array( 'h-separator' ), $visibilityPerMedia ),
				'style'     => $style,
			)
		);

		$this->setChildren( array( $html ) );
	}

	public function getVisibilityPerMedia( $enabledByMedia = array() ) {
		$classes = array();
		$prefix  = 'h-separator--display';
		foreach ( $enabledByMedia as $media => $enabled ) {
			$value         = $enabled ? 'flex' : 'none';
			$mediaPrefix   = utils::getMediaPrefix( $media );
			$values        = LodashBasic::compactWithExceptions( array( $prefix, $value, $mediaPrefix ), array( '0', 0 ) );
			$prefixedClass = implode( '-', $values );

			$classes[] = $prefixedClass;
		}
		return $classes;
	}
}

