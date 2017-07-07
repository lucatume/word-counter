<?php

namespace WCounter;

class WP {

	public function strip_tags( $content, $allowable_tags = null ) {
		return strip_tags( $content, $allowable_tags );
	}

	public function apply_filters( $tag, $value ) {
		return apply_filters( $tag, $value );
	}
}