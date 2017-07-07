<?php

namespace WCounter;

class Counter {
	/**
	 * @var \WCounter\WP
	 */
	protected $wp;

	public function __construct( WP $wp ) {
		$this->wp = $wp;
	}

	public function count_words( $content ) {
		return str_word_count( $this->wp->strip_tags( $content, '' ) );
	}

	public function get_reading_time_for( $content ) {
		/**
		 * Filters the words per minute value.
		 *
		 * @param int $words_per_minute
		 */
		$words_per_minute = $this->wp->apply_filters( 'wcounter_wpm_value', 275 );

		$words = $this->count_words( $content );

		return ceil( $words / $words_per_minute );
	}
}
