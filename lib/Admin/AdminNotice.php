<?php


namespace MobinDev\Novin_Commerce\Admin;


class AdminNotice {
	const NAME = 'novin_commerce_notice';
	private static $instance = null;
	private $cookies = [];

	public function __construct() {
		$this->cookies = json_decode( $_COOKIE[ self::NAME ], true );
		if ( ! $this->cookies ) {
			$this->cookies = [];
		}
	}

	private static function getInstance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function hook() {
		$instance = self::getInstance();
		add_action( 'admin_notices', [ $instance, 'show' ] );
	}

	public static function get() {
		$instance = self::getInstance();

		return $instance->cookies;
	}

	public static function add( $message, $type = 'success', $is_dismissible = true, $number_of_show = 1 ) {
		$instance = self::getInstance();

		$instance->cookies[] = compact( 'message', 'type', 'is_dismissible', 'number_of_show' );

		return $instance->save();
	}

	private function save() {
		return setcookie( self::NAME, json_encode( $this->cookies ) );
	}

	private function deleteAll() {
		setcookie( self::NAME, json_encode( [] ) );
		$instance          = self::getInstance();
		$instance->cookies = [];

	}

	public static function addSuccessDismissible( $message, $number_of_show = 1 ) {
		self::add( $message, 'success', true, $number_of_show );
	}

	public static function addErrorDismissible( $message, $number_of_show = 1 ) {
		self::add( $message, 'error', true, $number_of_show );
	}

	public static function addWarningDismissible( $message, $number_of_show = 1 ) {
		self::add( $message, 'warning', true, $number_of_show );
	}

	public static function addSuccessPermanent( $message, $number_of_show = 1 ) {
		self::add( $message, 'success', false, $number_of_show );
	}

	public static function addErrorPermanent( $message, $number_of_show = 1 ) {
		self::add( $message, 'error', false, $number_of_show );
	}

	public static function addWarningPermanent( $message, $number_of_show = 1 ) {
		self::add( $message, 'warning', false, $number_of_show );
	}


	public function show() {
		$instance = self::getInstance();
		$notices  = $instance->cookies;
		foreach ( $instance->cookies as $key => $notice ) {
			$message        = $notice['message'];
			$type           = $notice['type'];
			$is_dismissible = $notice['is_dismissible']?'is-dismissible':'';
			$class          = 'notice notice-' . $type;
			printf( '<div class="%1$s %2$s"><p>%3$s</p></div>', esc_attr( $class ), esc_attr( $is_dismissible ), esc_html( $message ) );

			$notices[$key]['number_of_show'] --;
			if ( $notices[$key]['number_of_show'] <= 0 ) {
				unset( $notices[ $key ] );
			}

		}
		$this->cookies = $notices;
		$this->save();

	}
}
