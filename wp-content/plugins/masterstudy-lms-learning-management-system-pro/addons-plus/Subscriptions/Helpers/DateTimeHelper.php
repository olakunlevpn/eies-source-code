<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Helpers;

use DateInterval;
use DateTime;
use DateTimeZone;

class DateTimeHelper {
	const FORMAT_MYSQL     = 'Y-m-d H:i:s';
	const FORMAT_DATE_TIME = 'Y-m-d H:i:s';
	const FORMAT_DATE      = 'Y-m-d';
	const FORMAT_TIME      = 'H:i:s';
	const FORMAT_TIMESTAMP = 'U';

	const INTERVAL_HOUR  = 'hour';
	const INTERVAL_DAY   = 'day';
	const INTERVAL_WEEK  = 'week';
	const INTERVAL_MONTH = 'month';
	const INTERVAL_YEAR  = 'year';

	private $datetime;

	private static function instance() {
		return new self();
	}

	public static function now() {
		$instance           = self::instance();
		$instance->datetime = new DateTime();

		return $instance;
	}

	public static function create( $datetime, $timezone = null ) {
		$instance           = self::instance();
		$instance->datetime = new DateTime(
			$datetime,
			$timezone instanceof DateTimeZone
				? $timezone
				: ( $timezone ? new DateTimeZone( $timezone ) : null )
		);

		return $instance;
	}

	public function set_timezone( $timezone ) {
		$timezone = is_string( $timezone ) ? new DateTimeZone( $timezone ) : $timezone;

		$this->datetime->setTimezone( $timezone );

		return $this;
	}
	public function add( $number, $interval ) {
		$this->datetime->add( DateInterval::createFromDateString( "{$number} {$interval}" ) );

		return $this;
	}


	public function sub( $number, $interval ) {
		$this->datetime->sub( DateInterval::createFromDateString( "{$number} {$interval}" ) );

		return $this;
	}

	public function is_past() {
		return $this->datetime->getTimestamp() < time();
	}

	public function is_future() {
		return $this->datetime->getTimestamp() > time();
	}

	public function get_timezone() {
		return $this->datetime->getTimezone();
	}

	public function get_timezone_string() {
		return $this->datetime->getTimezone()->getName();
	}

	public function get_date_time_string() {
		return $this->datetime->format( self::FORMAT_MYSQL );
	}

	public function format( $format = null, $translation = true ) {
		if ( $translation ) {
			$result = wp_date(
				$format ? $format : self::FORMAT_MYSQL,
				$this->to_timestamp(),
				$this->datetime->getTimezone()
			);
		} else {
			$result = $this->datetime->format( $format ? $format : self::FORMAT_MYSQL );
		}

		return $result;
	}

	public function to_timestamp() {
		return $this->datetime->getTimestamp();
	}

	public function to_date_time_string() {
		return $this->format( self::FORMAT_DATE_TIME, false );
	}

	public function to_date_string() {
		return $this->format( self::FORMAT_DATE, false );
	}

	public function to_time_string() {
		return $this->format( self::FORMAT_TIME, false );
	}
}
