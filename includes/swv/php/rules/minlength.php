<?php

namespace Contactable\SWV;

use WPCF7_SWV_Rule as Rule;

class MinLengthRule extends Rule {

	const rule_name = 'minlength';

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

		if ( empty( $input ) ) {
			return true;
		}

		$total = 0;

		foreach ( $input as $i ) {
			$total += wpcf7_count_code_units( $i );
		}

		$threshold = (int) $this->get_property( 'threshold' );

		if ( $threshold <= $total ) {
			return true;
		} else {
			return $this->create_error();
		}
	}

}
