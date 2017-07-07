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

add_filter( 'the_title', function ( $title ) {
	return "{$title} (1m)";
} );
