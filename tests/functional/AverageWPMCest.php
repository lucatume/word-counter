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

	/**
	 * It should correctly store the average WPM value when creating posts
	 *
	 * @test
	 */
	public function should_correctly_store_the_average_wpm_value_when_creating_posts( FunctionalTester $I ) {
		$I->loginAsAdmin();

		$i = $words = 0;
		foreach ( [ 6, 4, 2 ] as $n ) {
			$words += $n;
			$I->amOnAdminPage( '/post-new.php' );
			$I->submitForm( '#post', [
				'post_title' => 'Post ' . ++ $i,
				'content'    => implode( ' ', array_fill( 1, $n, 'lorem' ) ),
			] );

			$I->assertEquals( "{$words}/{$i}", $I->grabOptionFromDatabase( 'wcounter_average_wpm' ) );
		}
	}
}
