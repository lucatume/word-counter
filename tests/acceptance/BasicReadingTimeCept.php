<?php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'see the reading time beside a post title' );

$content = implode( ' ', array_fill( 0, 274, 'lorem' ) );
$post_id = $I->havePostInDatabase( [
	'post_title'   => 'A post',
	'post_content' => $content,
] );

$I->amOnPage("/?p={$post_id}");

$I->see('A post (1m)');
