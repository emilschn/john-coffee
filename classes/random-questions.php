<?php
/**
 * Random questions builder
 */
class JohnCoffee_Random_Question {
	private static $list_intro = array(
		"Hello everyone!",
		"A little question for today!",
		"Warm greetings!",
		"Hello!",
		"Hello everybody!",
		"Helloooooo!",
		"Yo!",
	);

	private static $list_question = array(
		"Can you choose",
		"Can you share",
		"Can you speak about",
		"Would you like to share",
		"Would you like to speak about",
	);

	private static $index_random_theme;
	private static $list_theme = array(
		'a song',
		'a movie',
		'a TV serie or an anime',
		'a quote (or lyrics from a song)',
		'a recipe',
		'a restaurant',
		'a town',
		'a country',
		'an activity',
		'a sport',
		'a photo',
		'a video',
		'a personnality',
		'a book or a comic book',
		'a painting',
		'someone close to you',
		'a cartoon',
		'a singer',
		'a coworker',
		'a childhood memory',
		'a memory from your yooth',
		'something stupid',
		'a dream',
		'a game',
		'a video game',
		'an animal',
	);

	private static $index_random_action;
	private static $list_actions = array(
		'that you recommend',
		'that can freak you out',
		'that you find splendid',
		'that can make you sad',
		'that is a guilty pleasure',
		'that you liked when you were younger but not anymore',
		'that is a great memory',
		'that can boost your spirit',
		'that can drive you crazy',
		'that affects you',
		'that questions you',
		'that disgusts you',
		'that inspires you',
		'from your childhood region or your hometown',
		'that everyone like but not you',
		'that everyone hates but that you like',
	);

	public static function get_funky() {
		$intro_str = self::get_intro();
		$message = $intro_str . "\n";

		$question_str = self::get_question();
		$theme_str = self::get_theme();
		$action_str = self::get_action();
		$message .= $question_str . " " . $theme_str . " " . $action_str . " ?";

		return $message;
	}

	public static function get_id_choosen() {
		return 'T' . self::$index_random_theme . 'A' . self::$index_random_action;
	}

	private static function get_intro() {
		$index_random = rand( 0, count( self::$list_intro ) - 1 );
		$result = self::$list_intro[ $index_random ];
		return __( $result, 'johncoffee' );
	}

	private static function get_question() {
		$index_random = rand( 0, count( self::$list_question ) - 1 );
		$result = self::$list_question[ $index_random ];
		return __( $result, 'johncoffee' );
	}

	private static function get_theme() {
		self::$index_random_theme = rand( 0, count( self::$list_theme ) - 1 );
		$result = self::$list_theme[ self::$index_random_theme ];
		return __( $result, 'johncoffee' );
	}

	private static function get_action() {
		self::$index_random_action = rand( 0, count( self::$list_actions ) - 1 );
		$action_str = self::$list_actions[ self::$index_random_action ];
		return __( $action_str, 'johncoffee' );
	}
}