<?php


class BasicReadingTimeCest {

	public function _before( AcceptanceTester $I ) {
	}

	public function _after( AcceptanceTester $I ) {
	}

	/**
	 * It should show the reading time beside the title
	 *
	 * @test
	 */
	public function should_show_the_reading_time_beside_the_title( AcceptanceTester $I ) {
		$content = implode( ' ', array_fill( 0, 274, 'lorem' ) );
		$post_id = $I->havePostInDatabase( [
			'post_title'   => 'A post',
			'post_content' => $content,
		] );

		$I->amOnPage( "/?p={$post_id}" );

		$I->see( 'A post (1m)' );
	}
}
