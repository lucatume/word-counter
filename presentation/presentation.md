footer: @lucatume - Future WP, Milano 7/8/2017
slidenumbers: true

# TDD in WordPress with Codeception and wp-browser from scratch

*Getting started is half the work of test-drive development; the other half is running the tests.*

Luca Tumedei
@lucatume on Twitter and GitHub

Future WP
Milano, 7/8/2017

---

# whoami

* a freelance WordPress backend developer
* the creator of the [wp-browser](https://github.com/lucatume/wp-browser "lucatume/wp-browser · GitHub") library for [Codeception](http://codeception.com/ "Codeception - BDD-style PHP testing.")
* a nerd on a mission to democratize WordPress testing

---

# The mandatory "you should test your code" introduction

* no; let's write some code and reflect about our inner motivations later
* ask questions
* the best way to learn is teaching: tinker, get the job done then **help your neighbours**

---

# What are we building, then?

* a word counter plugin
* counts the words in a post content and displays the estimate reading time beside the title

```gherkin
Given the 'word-counter' plugin is active on the site
And I have post with 275 words in it
When I visit the single post page
Then I should see '1m' as estimated reading time beside the title 
```

---

# VVV is the base
 
* We will use the default WordPress installation provided by [VVV](!g) to create and test the plugin.  
* I'm assuming the site we are working with will be reachable at [local.wordpress.dev](http://local.wordpress.dev)

---

# Creating the plugin base files - 1

* navigate to the VVV folder
* navigate to the `www/wordpress-default/public_html/wp-content/plugins` folder
* create the `word-counter` folder

---

# Creating the plugin base files - 2

* create the `word-counter/word-counter.php` plugin file, e.g:

```php
/*
Plugin Name: Word Counter
Plugin URI: https://wordpress.org/plugins/word-counter
Description: A plugin that counts words
Version: 0.1.0
Author: Me
Author URI: http://example.com
Text Domain: word-counter
*/	
```

---

# Sanity check

* visit [the plugin admin screen](http://local.wordpress.dev/wp-admin/plugins.php?plugin_status=all&paged=1&s) and make sure you can see, activate and deactivate the "Word Counter" plugin
* initialize your `git` repository in the `word-counter` folder and make your first `git commit`

Obsessively `git commit`, please.

---

# Composer initialization

* access the vagrant box with `vagrant ssh` and navigate to the plugin folder:

	```bash 
	cd /vagrant/www/wordpress-default/public_html/wp-content/plugins/word-counter
	```

* initialize [Composer](https://getcomposer.org/), for the time being **do not define any dependency**:

```bash
composer init
```

---

# Adding wp-browser to the mix

* now let's add [wp-browser](https://github.com/lucatume/wp-browser "lucatume/wp-browser · GitHub") as a development dependency:

```bash
composer require --dev lucatume/wp-browser
```

and wait... wait... wait...

---

# Make your life easier

To avoid having to use `vendor/bin/codecept` every time add `vendor/bin` to the `PATH`:

```bash
export PATH=vendor/bin:$PATH \
&& echo "export PATH=vendor/bin:$PATH" >> ~/.bashrc
```

---

# Configuring wp-browser - 1

Initialize wp-browser with the `codecept init wpbrowser` and answer the questions:

* Where is WordPress installed? - `/vagrant/www/wordpress-default/public_html`
* What's the name of the database used by the WordPress installation? - `wordpress_default`
* Use `root` and `root` for database username and password

---

# Configuring wp-browser - 2

* What's the name of the database WPLoader should use? - `wordpress_unit_tests`
* What's the URL the WordPress installation? - `http://local.wordpress.dev`
* What is the folder/plugin.php name of the plugin? - `word-counter/word-counter.php`

---

# Create a starting database dump

```bash
mkdir backup
wp db export backups/db.backup.sql
wp plugin deactivate $(wp plugin list --status=active --field=name)
wp site empty --yes
wp plugin activate word-counter
wp db export tests/_data/dump.sql
```

---

# Build the suites

* make sure everything is working building the suites and running [Codeception](http://codeception.com/ "Codeception - BDD-style PHP testing."):

```bash
codecept build
codecept run
```

---

# The first test?

* got any ideas about the first test?
* what should we test first?
* at what level?

---

# Writing the first test - 1

Remember the scenario?

```gherkin
Given the 'word-counter' plugin is active on the site
And I have post with 275 words in it
When I visit the single post page
Then I should see '(1m)' as estimated reading time beside the title 
```
Let's write an acceptance test for it:

```bash
codecept generate:cept acceptance BasicReadingTime
```

---

# Writing the first test - 2

Something like this:

```php
$I = new AcceptanceTester( $scenario );
$I->wantTo( 'see the reading time beside a post title' );

$content = implode( ' ', array_fill( 0, 274, 'lorem' ) );
$post_id = $I->havePostInDatabase( [
	'post_title'   => 'A post',
	'post_content' => $content,
] );

$I->amOnPage("/?p={$post_id}");

$I->see('A post (1m)');

```

---

# Modules and methods

* look at the `tests/acceptance.suite.yml` file
* what **modules** are listed as active there?
* can you tell which method is provided by which module?

That's why we **built** the suites.

---

# Passing the first test

* write **as little code as possible**
* forget considerations about structure and standards

Run the test using:

```bash
codecept run acceptance
```

**(demo and solution)**

---

# My solution... is cheating

* we should put in place more tests like this one
* we should use more and less words using the 275 WPM ratio
* should we scaffold another `Cept` tests?

---

# Introducing Cest type tests

* like `Cept` but more flexible and less redundant
* PHPUnit-like with methods running before and after each test

```bash
codecept generate:cest acceptance BasicReadingTime
```

Port the code over and run the tests again (`codecept run acceptance`) to make sure it works.

**(demo and solution)**

---

# Use different number of words

* introducing **examples**; like PHPUnit **data providers**
* for our plugin minimum time will be 1 minute
* **ceil** the words on WPM result

---

# Cover more cases

* it should omit the reading time if post content is empty 
* it should expose the reading time on posts only (no pages)

Failures? Look into `tests/_output`...

**(demo and solution - step 4)**

---

# What about user options?

* we want to keep track of the average reading time of posts
* it will boil down to setting an option (e.g. `wcounter_average_wpm`)

Enter functional tests!
Wait: what? Why?

---

# Acceptance vs Functional - 1

* acceptance tests exercise the code through the UI (or API) users would use
* acceptance tests should not know/care/depend on the implementation
* blackbox approach using **inputs** to test **outputs**

---

# Acceptance vs Functional - 2

* due to WordPress nature (globals and side effects) the distinction is blurry
* functional tests know/care/depend on the implementation
* **whitebox** approach using **context** and **inputs** to test **state changes***

I draw the line where I have to look "under the hood" to make an assertion.

---

# Adding a functional test

* `codecept generate:cest functional AverageWPM`
* initial value on an empty site should be `n/a`

---

# First functional test

```php
class AverageWPMCest {
	public function should_have_an_average_wpm_value_of_n_a( FunctionalTester $I ) {
		$I->assertEquals( 'n/a', $I->grabOptionFromDatabase( 'wcounter_average_wpm' ) );
	}
}
```
---

# Activation hook and dump regeneration

* initial value should be set on activation
* we need to register an activation hook...
* ...re-activate the plugin and re-generate the dump

Really under the hood.

**(demo and solution - step 5)**

---

# Second functional test - 1

* we want the value in the db to be like `total-words/posts-count`
* and be updated when we create new posts

---

# Second functional test - 2


```php
class AverageWPMCest {
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
```
---

## What about allowing filtering of the WPM value?

* we could allow devs to filter the WPM value (default to `275`)
* how do we test that we will use and allow for filtering?

Time to talk about integration.

---

# Integration testing

* very much under the hood
* how does our code **integrate** with the app (WP, plugins, themes)?
* `unit` !== `integration`

---

# Creating a first integration test

```bash
codecept generate:wpunit wpunit WPMFiltering
```

---

# Filtering the WPM value

```php
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
```

---

# Passing the integration test

```php
add_filter( 'the_title', function ( $title, $post_id ) {
	$post = get_post( $post_id );

	if ( $post->post_type !== 'post' ) {
		return $title;
	}

	/**
	 * Filters the words per minute value.
	 *
	 * @param int $words_per_minute
	 */
	$words_per_minute = apply_filters( 'wcounter_wpm_value', 275 );

	$content = apply_filters( 'the_content', $post->post_content );
	$reading_time = ceil( $words / $words_per_minute );

	return $reading_time > 0
		? "{$title} ({$reading_time}m)"
		: $title;
}, 10, 2 );
```

---

# Let's try more values!

* [Codeception](http://codeception.com/ "Codeception - BDD-style PHP testing."), and [wp-browser](https://github.com/lucatume/wp-browser "lucatume/wp-browser · GitHub") by extension, uses [PhpUnit](https://phpunit.de/ "PHPUnit – The PHP Testing Framework")
* we can use data providers
* same logic used in the acceptance tests with examples

---

# Data provider code

```php
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
		// ...
	}
}
```

---

# Finally unit testing

* we have some duplicated code to go from content to reading times
* let's move the code into a utility class
* the `WCounter\Counter` will provide us with these methods:

```php
public function count_words( $content );
public function get_reading_time_for( $content );
```

Run the acceptance, functional and integration tests to make sure it still works.

---

# Some refactoring later - the Counter class clients

**(demo - step 9)**

* we are using two WordPress functions
* how do we control them in unit tests?

---

# Unit testing - 1

* test a single entity (e.g. a class)
* mock all its dependencies (WP functions in our case)
* control inputs and check outputs

## Refactor code to be in control!

---

# Unit testing - 2

* mock dependencies **we** own
* we do not own WordPress code
* namespace-based function mocking or [function-mocker](https://github.com/lucatume/function-mocker)-like solutions are still mocks of code we **do not** own

---

# Adapters

* if I can control the code I will opt for adapters
* an adapter is just a class providing proxy methods to WordPress functions
* but **we** control this code hence we can mock it
* in my experience the cleanest and most maintainable solution

---

# The WP class

```php
namespace WCounter;

class WP {

	public function strip_tags( $content, $allowable_tags = null ) {
		return strip_tags( $content, $allowable_tags );
	}

	public function apply_filters( $tag, $value ) {
		return apply_filters( $tag, $value );
	}
}
```

---

# Now a unit test

```bash
codecept generate:test unit "WCounter\Counter"
```

---

# Running the unit tests

```bash
codecept run unit
```

---

# Unit test code - 1

```php
namespace WCounter;

use Prophecy\Argument;

include_once dirname( __FILE__ ) . '/../../../src/Counter.php';
include_once dirname( __FILE__ ) . '/../../../src/WP.php';

class CounterTest extends \Codeception\Test\Unit {

	public function test_count_words() {
		$content  = 'foo bar baz';
		$stripped = 'lorem dolor';
		$wp       = $this->prophesize( WP::class );
		$wp->strip_tags( $content, Argument::type( 'string' ) )->willReturn( $stripped );

		$counter = new Counter( $wp->reveal() );

		$this->assertEquals( 2, $counter->count_words( $content ) );
	}
```

---

# Unit test code - 2

```php
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
```

---

# Final code

**(demo and solution - step 10)**

---

# Final words

* I tried to cover all the bases but I left a ton out!
* TDD is an habit, not a function of time
* 100% code coverage is for maniacs

---

# Thanks!

* Questions?
* ask me a question on Twitter (@lucatume)
* report issues if you find any on [wp-browser](https://github.com/lucatume/wp-browser "lucatume/wp-browser · GitHub")
