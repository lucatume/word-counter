<?php
/*
Plugin Name: Word Counter
Plugin URI: https://wordpress.org/plugins/word-counter
Description: A plugin that counts words
Version: 0.1.0
Author: Me
Author URI: http://example.com
Text Domain: word-counter
Domain Path: /languages
*/

register_activation_hook( __FILE__, function () {
	update_option( 'wcounter_average_wpm', 'n/a' );
} );

add_action( 'wp_insert_post', function ( $post_id, WP_Post $post, $update ) {
	if ( 'post' !== $post->post_type || ! empty( $post->post_parent ) ) {
		return;
	}

	$current_value = get_option( 'wcounter_average_wpm' );

	if ( 'n/a' === $current_value ) {
		$total_words = $total_posts = 0;
	} else {
		$frags       = explode( '/', $current_value );
		$total_words = count( $frags ) === 2 ? intval( $frags[0] ) : 0;
		$total_posts = count( $frags ) === 2 ? intval( $frags[1] ) : 0;
	}

	$content = apply_filters( 'the_content', $post->post_content );
	$words   = str_word_count( strip_tags( $content, '' ) );

	if ( $words === 0 ) {
		return;
	}

	$total_words += $words;
	$total_posts += 1;

	update_option( 'wcounter_average_wpm', "{$total_words}/{$total_posts}" );
}, 10, 3 );

add_filter( 'the_title', function ( $title, $post_id ) {
	$post = get_post( $post_id );

	if ( $post->post_type !== 'post' ) {
		return $title;
	}

	/**
	 * Filters the words per minute value.
	 *
	 * @param int $words_per_minute
	 */
	$words_per_minute = apply_filters( 'wcounter_wpm_value', 275 );

	$content = apply_filters( 'the_content', $post->post_content );
	$words   = str_word_count( strip_tags( $content, '' ) );

	$reading_time = ceil( $words / $words_per_minute );

	return $reading_time > 0
		? "{$title} ({$reading_time}m)"
		: $title;
}, 10, 2 );
