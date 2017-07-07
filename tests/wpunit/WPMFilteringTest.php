<?php

class WPMFilteringTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * It should allow filtering the WPM value
	 *
	 * @test
	 */
	public function should_allow_filtering_the_wpm_value() {
		$post = $this->factory()->post->create_and_get( [
			'post_title'   => 'A post',
			'post_content' => implode( ' ', array_fill( 1, 300, 'lorem' ) ),
		] );

		add_filter( 'wcounter_wpm_value', function () {
			return 100; // slow readers
		} );

		$this->assertEquals( 'A post (3m)', apply_filters( 'the_title', $post->post_title, $post->ID ) );
	}
}