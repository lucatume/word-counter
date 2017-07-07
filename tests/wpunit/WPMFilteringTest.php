<?php

class WPMFilteringTest extends \Codeception\TestCase\WPTestCase {

	public function wpm_values_and_reading_times() {
		return [
			[ '100', '3m' ],
			[ '50', '6m' ],
			[ '25', '12m' ],
			[ '300', '1m' ],
			[ '500', '1m' ],
		];
	}

	/**
	 * It should allow filtering the WPM value
	 *
	 * @test
	 * @dataProvider wpm_values_and_reading_times
	 */
	public function should_allow_filtering_the_wpm_value( $wpm, $expected ) {
		$post = $this->factory()->post->create_and_get( [
			'post_title'   => 'A post',
			'post_content' => implode( ' ', array_fill( 1, 300, 'lorem' ) ),
		] );

		add_filter( 'wcounter_wpm_value', function () use ( $wpm ) {
			return $wpm;
		} );

		$this->assertEquals( "A post ({$expected})", apply_filters( 'the_title', $post->post_title, $post->ID ) );
	}
}