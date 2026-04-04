<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\Grades\Repositories;

final class StudentGradesRepository extends GradesRepository {
	protected function get_extra_query() {
		return $this->db->prepare(
			' AND user_courses.user_id = %d ',
			get_current_user_id()
		);
	}
}
