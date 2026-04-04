<?php

namespace MasterStudy\Lms\Pro\RestApi\Context;

class StudentContext {
	private static $instance = null;

	private $student_id = null;

	private function __construct() {}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	// Set Current Instructor ID
	public function set_student_id( $student_id ) {
		$this->student_id = $student_id;
	}

	// Get Current Instructor ID
	public function get_student_id() {
		return $this->student_id;
	}
}
