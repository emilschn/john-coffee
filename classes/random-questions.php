<?php
/**
 * Random questions builder
 */
class JohnCoffee_Random_Question {
	private static $question_type_theme = 'theme';
	private static $question_type_discovery = 'discovery';
	private static $question_type_favorite = 'favorite';

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
		'a pokemon',
	);

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
		'that everyone likes but not you',
		'that everyone hates but that you like',
	);

	private static $list_discovery_themes = array(
		'Could you share a song that you discovered recently?',
		'What is the song that you can&nbsp;t stop listening to at the moment?',
		'Who is the singer that you can&nbsp;t stop listening to at the moment?',
		'What is the last movie that you saw and liked?',
		'What is the last movie that you saw and hated?',
		'Are you watching a TV serie or anime at the moment? Do you like it?',
		'Are you playing a game or video game at the moment? Do you like it?',
		'What is the last cartoon that you saw and liked?',
		'What is the last cartoon that you saw and hated?',
		'What is the last recipe you cooked?',
		'What is the last recipe you failed?',
		'Have you discovered a great restaurant recently?',
		'Is there an activity that you like to practice these days?',
		'Is there a sport that you discovered recently?',
		'Could you share a video that you liked this month?',
		'What is the last book or comic book that you read and liked?',
		'What is the last book or comic book that you read and hated?',
		'Have you discovered a painting recently?',
		'Did you take a good picture recently?',
		'Have you discovered a strange animal recently?',
	);

	private static $list_favorite_themes = array(
		'What is your all time favorite song?',
		'What is your all time favorite music album?',
		'What is your all time favorite singer or band?',
		'What is your all time favorite movie?',
		'What is the movie that you watched the most times?',
		'What is your all time favorite cartoon?',
		'What is the cartoon that you watched the most times?',
		'What TV show or anime can you watch over and over?',
		'What recipe could you eat all the time?',
		'Which country do you like to visit the most?',
		'In which town do you feel like you are at home?',
		'What sport do you like to practice the most?',
		'What sport do you like to watch the most?',
		'Is there a picture that is your favorite?',
		'Is there a personnality that you would like to meet?',
		'Is there a dead personnality that you would like to resurrect?',
		'What is your all time favorite book?',
		'What is your all time favorite comic book?',
		'What is your all time favorite painting?',
		'What is your all time favorite game?',
		'What is your all time favorite video game?',
		'What is the video game that you played the most?',
		'What is the animal that you find the cutest?',
	);

	private $list_questions_asked_previously;
	private $message;

	private $index_random_theme;
	private $index_random_action;
	private $index_random_discovery_theme;
	private $index_random_favorite_theme;
	private $question_type;

	public function __construct( $list_questions_asked_previously ) {
		$this->list_questions_asked_previously = $list_questions_asked_previously;
		$this->message = $this->get_funky();
	}

	public function get_message() {
		return $this->message;
	}

	public function get_id_choosen() {
		switch ( $this->question_type ) {
			case self::$question_type_theme:
				if ( empty( $this->index_random_theme ) || empty( $this->index_random_action ) ) {
					return '';
				}
				return 'T' . $this->index_random_theme . 'A' . $this->index_random_action;
				break;

			case self::$question_type_discovery:
				if ( empty( $this->index_random_discovery_theme ) ) {
					return '';
				}
				return 'D' . $this->index_random_discovery_theme;
				break;

			case self::$question_type_favorite:
				if ( empty( $this->index_random_favorite_theme ) ) {
					return '';
				}
				return 'F' . $this->index_random_favorite_theme;
				break;
		}

		return '';
	}

	/**
	 * Dance, John, Dance!
	 */
	private function get_funky() {
		$intro_str = $this->get_intro();

		while ( !$this->can_ask_question() ) {
			$question = $this->get_question_content();
		}

		$the_music = $intro_str . "\n";
		$the_music .= $question;

		return $the_music;
	}

	/**
	 * Which random question will you ask, John?
	 */
	private function get_question_content() {
		$this->init_question_type();
		
		switch ( $this->question_type ) {
			case self::$question_type_theme:
				$question_str = $this->get_question();
				$theme_str = $this->get_theme();
				$action_str = $this->get_action();
				return $question_str . " " . $theme_str . " " . $action_str . " ?";
				break;

			case self::$question_type_discovery:
				$theme_str = $this->get_discovery_theme();
				return $theme_str;
				break;

			case self::$question_type_favorite:
				$theme_str = $this->get_favorite_theme();
				return $theme_str;
				break;
		}

		return '';
	}

	/**
	 * Which delay is necessary to ask a question another time?
	 */
	private function get_expiration_delay_by_question_type() {
		switch ( $this->question_type ) {
			case self::$question_type_theme:
				return new DateInterval( 'P182D' );
				break;

			case self::$question_type_discovery:
				return new DateInterval( 'P91D' );
				break;

			case self::$question_type_favorite:
				return new DateInterval( 'P365D' );
				break;
		}

		return new DateInterval( 'P91D' );
	}

	/**
	 * John, can you ask this question? Verifying previously asked questions
	 */
	private function can_ask_question() {
		if ( $this->get_id_choosen() == '' ) {
			return false;
		}

		// Never asked the question, of course, you can
		if ( empty( $this->list_questions_asked_previously[ $this->get_id_choosen() ] ) ) {
			return true;
		}

		// You asked it before... Maybe it was a long time ago?!
		date_default_timezone_set( 'Europe/Paris' );
		$str_date_when_sent = $this->list_questions_asked_previously[ $this->get_id_choosen() ];
		$date_when_sent = new DateTime( $str_date_when_sent );
		$date_when_sent->add( $this->get_expiration_delay_by_question_type() );
		$date_today = new DateTime();
		if ( $date_today > $date_when_sent ) {
			return true;
		}

		// Sorry, ask something else
		return false;
	}


/*********************************************
 * Random initializations
 *********************************************/
	/**
	 * Randomize the question type
	 */
	private function init_question_type() {
		$index_random = rand( 0, 5 );
		if ( $index_random < 2 ) {
			$this->question_type = self::$question_type_theme;
		} elseif ( $index_random < 4 ) {
			$this->question_type = self::$question_type_discovery;
		} else {
			$this->question_type = self::$question_type_favorite;
		}
	}

	private function get_intro() {
		$index_random = rand( 0, count( self::$list_intro ) - 1 );
		$result = self::$list_intro[ $index_random ];
		return __( $result, 'johncoffee' );
	}

	private  function get_question() {
		$index_random = rand( 0, count( self::$list_question ) - 1 );
		$result = self::$list_question[ $index_random ];
		return __( $result, 'johncoffee' );
	}

	private function get_theme() {
		$this->index_random_theme = rand( 0, count( self::$list_theme ) - 1 );
		$result = self::$list_theme[ $this->index_random_theme ];
		return __( $result, 'johncoffee' );
	}

	private function get_action() {
		$this->index_random_action = rand( 0, count( self::$list_actions ) - 1 );
		$action_str = self::$list_actions[ $this->index_random_action ];
		return __( $action_str, 'johncoffee' );
	}

	private function get_discovery_theme() {
		$this->index_random_discovery_theme = rand( 0, count( self::$list_discovery_themes ) - 1 );
		$result = self::$list_discovery_themes[ $this->index_random_discovery_theme ];
		return __( $result, 'johncoffee' );
	}

	private function get_favorite_theme() {
		$this->index_random_favorite_theme = rand( 0, count( self::$list_favorite_themes ) - 1 );
		$result = self::$list_favorite_themes[ $this->index_random_favorite_theme ];
		return __( $result, 'johncoffee' );
	}
}