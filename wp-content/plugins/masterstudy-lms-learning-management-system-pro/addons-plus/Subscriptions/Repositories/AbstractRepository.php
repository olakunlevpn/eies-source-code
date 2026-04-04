<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Subscriptions\Repositories;

use MasterStudy\Lms\Pro\RestApi\Context\InstructorContext;
use MasterStudy\Lms\Pro\RestApi\Context\StudentContext;

abstract class AbstractRepository {
	protected $db;

	protected ?int $current_instructor_id;

	protected ?int $current_student_id;

	public function __construct() {
		global $wpdb;

		$this->current_instructor_id = InstructorContext::get_instance()->get_instructor_id();
		$this->current_student_id    = StudentContext::get_instance()->get_student_id();

		$this->db = $wpdb;
	}

	abstract public function table_name(): string;
	abstract public function get_fields_format(): array;
	abstract public function get_fields_values( array $data ): array;

	public function find( int $id ): ?int {
		return $this->db->get_var(
			$this->db->prepare( "SELECT id FROM {$this->table_name()} WHERE id = %d", $id )
		);
	}

	public function is_current_user_instructor(): bool {
		return null !== $this->current_instructor_id;
	}

	public function is_current_user_student(): bool {
		return null !== $this->current_student_id;
	}
}
