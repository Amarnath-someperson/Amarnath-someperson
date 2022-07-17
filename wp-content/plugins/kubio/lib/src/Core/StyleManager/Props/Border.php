<?php

namespace Kubio\Core\StyleManager\Props;

use Kubio\Config;
use Kubio\Core\LodashBasic;
use Kubio\Core\StyleManager\ParserUtils;

class Border extends PropertyBase {

	public function parse( $value, $options ) {
		$borderWithRadius = $this->valueWithDefault( $value );
		$style            = array();
		foreach ( $borderWithRadius as $side => $borderSide ) {
			$borderWidth = LodashBasic::get( $borderSide, 'width.value' );
			if ( is_numeric( $borderWidth ) ) {
				foreach ( array( 'color', 'width', 'style' ) as $prop ) {
					if ( isset( $borderSide[ $prop ] ) ) {
						ParserUtils::addValueUnitString(
							$style,
							'border-' . $side . '-' . $prop,
							$borderSide[ $prop ]
						);
					}
				}
			}
		}

		$radiuses = Config::value( 'props.border.radiusMap' );
		foreach ( $radiuses as $path => $___ ) {
			$radius = LodashBasic::get(
				$borderWithRadius,
				$radiuses[ $path ],
				null
			);
			if ( $radius !== null ) {
				ParserUtils::addValueUnitString( $style, $path, $radius );
			}
		}
		return $style;
	}
}
