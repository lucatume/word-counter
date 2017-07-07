<?php

namespace WCounter;

use Prophecy\Argument;

include_once dirname( __FILE__ ) . '/../../../src/Counter.php';
include_once dirname( __FILE__ ) . '/../../../src/WP.php';

class CounterTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * Test count words
	 */
	public function test_count_words() {
		$content  = 'foo bar baz';
		$stripped = 'lorem dolor';
		$wp       = $this->prophesize( WP::class );
		$wp->strip_tags( $content, Argument::type( 'string' ) )->willReturn( $stripped );

		$counter = new Counter( $wp->reveal() );

		$this->assertEquals( 2, $counter->count_words( $content ) );
	}

	/**
	 * Test get_reading_time_for
	 */
	public function test_get_reading_time_for() {
		$content   = implode( ' ', array_fill( 1, 100, 'foo' ) );
		$stripped  = $content;
		$wpm_value = 10;

		$wp = $this->prophesize( WP::class );
		$wp->strip_tags( $content, Argument::type( 'string' ) )->willReturn( $stripped );
		$wp->apply_filters( 'wcounter_wpm_value', Argument::type( 'int' ) )->willReturn( $wpm_value );

		$counter = new Counter( $wp->reveal() );

		$this->assertEquals( 10, $counter->get_reading_time_for( $content ) );
	}
}