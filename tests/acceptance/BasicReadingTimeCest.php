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
	 * @example [275, "1m"]
	 * @example [137, "1m"]
	 * @example [550, "2m"]
	 * @example [687, "3m"]
	 * @example [825, "3m"]
	 */
	public function should_show_the_reading_time_beside_the_title( AcceptanceTester $I, \Codeception\Example $data ) {
		$words_count   = $data[0];
		$expected_time = $data[1];

		$content = implode( ' ', array_fill( 0, $words_count, 'lorem' ) );
		$post_id = $I->havePostInDatabase( [
			'post_title'   => 'A post',
			'post_content' => $content,
		] );

		$I->amOnPage( "/?p={$post_id}" );

		$I->see( "A post ({$expected_time})" );
	}

	/**
	 * It should omit the reading time if post content is empty
	 *
	 * @test
	 */
	public function should_omit_the_reading_time_if_post_content_is_empty( AcceptanceTester $I ) {
		$post_id = $I->havePostInDatabase( [
			'post_title'   => 'A post',
			'post_content' => '',
		] );

		$I->amOnPage( "/?p={$post_id}" );

		$I->see( "A post" );
		$I->dontSee( "A post (" );
	}

	/**
	 * It should expose the reading time on posts only
	 *
	 * @test
	 */
	public function should_expose_the_reading_time_on_posts_only( AcceptanceTester $I ) {
		$content = implode( ' ', array_fill( 0, 550, 'lorem' ) );
		$page_id = $I->havePageInDatabase( [
			'post_title'   => 'A page',
			'post_content' => $content,
		] );

		$I->amOnPage( "/?p={$page_id}" );

		$I->see( "A page" );
		$I->dontSee( "A page (" );
	}

}
