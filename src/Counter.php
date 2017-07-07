<?php

namespace WCounter;

class Counter {

	public function count_words( $content ) {
		return str_word_count( strip_tags( $content, '' ) );
	}

	public function get_reading_time_for( $content ) {
		/**
		 * Filters the words per minute value.
		 *
		 * @param int $words_per_minute
		 */
		$words_per_minute = apply_filters( 'wcounter_wpm_value', 275 );

		$words = $this->count_words( $content );

		return ceil( $words / $words_per_minute );
	}
}
