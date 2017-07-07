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

add_filter( 'the_title', function ( $title, $post_id ) {
	$post = get_post( $post_id );

	if ( $post->post_type !== 'post' ) {
		return;
	}

	$words_per_minute = 275;
	$content          = apply_filters( 'the_content', $post->post_content );
	$words            = str_word_count( strip_tags( $content, '' ) );

	$reading_time = ceil( $words / $words_per_minute );

	return $reading_time > 0
		? "{$title} ({$reading_time}m)"
		: $title;
}, 10, 2 );
