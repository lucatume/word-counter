<?php


class AverageWPMCest {

	public function _before( FunctionalTester $I ) {
	}

	public function _after( FunctionalTester $I ) {
	}

	/**
	 * It should have an average WPM value of n/a when there are no posts
	 *
	 * @test
	 */
	public function should_have_an_average_wpm_value_of_n_a_when_there_are_no_posts( FunctionalTester $I ) {
		$I->assertEquals( 'n/a', $I->grabOptionFromDatabase( 'wcounter_average_wpm' ) );
	}
}
