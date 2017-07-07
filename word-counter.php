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

	$words_per_minute = 275;
	$content          = apply_filters( 'the_content', $post->post_content );
	$words            = str_word_count( strip_tags( $content, '' ) );

	$minutes = ceil( $words / $words_per_minute );

	return "{$title} ({$minutes}m)";
}, 10, 2 );
