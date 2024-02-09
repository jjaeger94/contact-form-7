<?php

namespace Contactable\SWV;

use WPCF7_SWV_Rule as Rule;

class MaxNumberRule extends Rule {

	const rule_name = 'maxnumber';

	public function matches( $context ) {
		if ( false === parent::matches( $context ) ) {
			return false;
		}

		if ( empty( $context['text'] ) ) {
			return false;
		}

		return true;
	}

	public function validate( $context ) {
		$field = $this->get_property( 'field' );
		$input = isset( $_POST[$field] ) ? $_POST[$field] : '';
		$input = wpcf7_array_flatten( $input );
		$input = wpcf7_exclude_blank( $input );

		$threshold = $this->get_property( 'threshold' );

		if ( ! wpcf7_is_number( $threshold ) ) {
			return true;
		}

		foreach ( $input as $i ) {
			if ( wpcf7_is_number( $i ) and (float) $threshold < (float) $i ) {
				return $this->create_error();
			}
		}

		return true;
	}

}
