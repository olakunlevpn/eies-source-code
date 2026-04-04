<?php

use MasterStudy\Lms\Routing\Router;

/** @var Router $router */

/**
 * Middlewares for all routes
 */
$router->middleware(
	apply_filters(
		'masterstudy_lms_routes_middleware',
		array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
			\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
		)
	)
);

/**
 * Course Builder routes
 */
$router->get(
	'/healthcheck',
	\MasterStudy\Lms\Http\Controllers\HealthCheckController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\HealthCheck::class,
);

$router->get(
	'/course-builder/settings',
	\MasterStudy\Lms\Http\Controllers\CourseBuilder\GetSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\CourseBuilder\GetSettings::class,
);

$router->put(
	'/course-builder/custom-fields/{post_id}',
	\MasterStudy\Lms\Http\Controllers\CourseBuilder\UpdateCustomFieldsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\CourseBuilder\UpdateCustomFields::class,
);

$router->get(
	'/courses/new',
	\MasterStudy\Lms\Http\Controllers\Course\AddNewController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\AddNew::class
);

$router->get(
	'/instructor-courses',
	\MasterStudy\Lms\Http\Controllers\Course\GetInstructorCoursesController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetInstructorCourses::class
);

$router->post(
	'/courses/create',
	\MasterStudy\Lms\Http\Controllers\Course\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Create::class
);

$router->post(
	'/courses/category',
	\MasterStudy\Lms\Http\Controllers\Course\CreateCategoryController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\CreateCategory::class
);

$router->get(
	'/courses/categories/list',
	\MasterStudy\Lms\Http\Controllers\Course\GetCategoriesListController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetCategoriesList::class
);

$router->get(
	'/courses/{course_id}/edit',
	\MasterStudy\Lms\Http\Controllers\Course\EditController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Edit::class
);

$router->get(
	'/courses/{course_id}/settings',
	\MasterStudy\Lms\Http\Controllers\Course\GetSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetSettings::class
);

$router->put(
	'/courses/{course_id}/settings',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateSettings::class
);

$router->get(
	'/courses/{course_id}/settings/faq',
	\MasterStudy\Lms\Http\Controllers\Course\GetFaqSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetFaqSettings::class
);

$router->put(
	'/courses/{course_id}/settings/faq',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateFaqSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateFaqSettings::class
);

$router->put(
	'/courses/{course_id}/settings/certificate',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateCertificateSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateCertificateSettings::class
);

$router->put(
	'/courses/{course_id}/settings/course-page-style',
	\MasterStudy\Lms\Http\Controllers\Course\UpdatePageStyleSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdatePageStyleSettings::class
);

$router->get(
	'/courses/{course_id}/settings/pricing',
	\MasterStudy\Lms\Http\Controllers\Course\GetPricingSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetPricingSettings::class
);

$router->put(
	'/courses/{course_id}/settings/pricing',
	\MasterStudy\Lms\Http\Controllers\Course\UpdatePricingSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdatePricingSettings::class
);

$router->put(
	'/courses/{course_id}/settings/files',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateFilesSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateFilesSettings::class
);

$router->put(
	'/courses/{course_id}/settings/access',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateAccessSettingsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateAccessSettings::class
);

$router->put(
	'/courses/{course_id}/status',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateStatusController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateStatus::class
);

$router->get(
	'/courses/{course_id}/curriculum',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\GetCurriculumController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\GetCurriculum::class
);

$router->post(
	'/courses/{course_id}/curriculum/section',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\CreateSectionController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\CreateSection::class
);

$router->put(
	'/courses/{course_id}/curriculum/section',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\UpdateSectionController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\UpdateSection::class
);

$router->delete(
	'/courses/{course_id}/curriculum/section/{section_id}',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\DeleteSectionController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\DeleteSection::class
);

$router->post(
	'/courses/{course_id}/curriculum/material',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\CreateMaterialController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\CreateMaterial::class
);

$router->put(
	'/courses/{course_id}/curriculum/material',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\UpdateMaterialController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\UpdateMaterial::class
);

$router->delete(
	'/courses/{course_id}/curriculum/material/{material_id}',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\DeleteMaterialController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\DeleteMaterial::class
);

$router->get(
	'/courses/{course_id}/curriculum/import',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\ImportSearchController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\ImportSearch::class
);

$router->post(
	'/courses/{course_id}/curriculum/import',
	\MasterStudy\Lms\Http\Controllers\Course\Curriculum\ImportMaterialsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\Curriculum\ImportMaterials::class
);

$router->get(
	'/courses/{course_id}/announcement',
	\MasterStudy\Lms\Http\Controllers\Course\GetAnnouncementController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetAnnouncement::class
);

$router->put(
	'/courses/{course_id}/announcement',
	\MasterStudy\Lms\Http\Controllers\Course\UpdateAnnouncementController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Course\UpdateAnnouncement::class
);

$router->get(
	'/students/',
	\MasterStudy\Lms\Http\Controllers\Student\GetStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudents::class
);

$router->delete(
	'/students/delete/',
	\MasterStudy\Lms\Http\Controllers\Student\DeleteStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\DeleteStudents::class
);

$router->get(
	'/students/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\GetStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudents::class
);

$router->get(
	'/export/students/',
	\MasterStudy\Lms\Http\Controllers\Student\ExportStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\ExportStudents::class
);

$router->get(
	'/students/export/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\ExportStudentsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\ExportStudents::class
);

$router->post(
	'/student/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\AddStudentController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\AddStudent::class
);

$router->post(
	'/student/bulk/{course_id}',
	\MasterStudy\Lms\Http\Controllers\Student\AddStudentsBulkController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\AddStudentsBulk::class
);

$router->put(
	'/student/progress/{course_id}/{student_id}',
	\MasterStudy\Lms\Http\Controllers\Student\SetStudentProgressController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\SetStudentProgress::class
);

$router->delete(
	'/student/progress/{course_id}/{student_id}',
	\MasterStudy\Lms\Http\Controllers\Student\ResetStudentProgressController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\ResetStudentProgress::class
);

$router->delete(
	'/student/{course_id}/{student_id}',
	\MasterStudy\Lms\Http\Controllers\Student\DeleteStudentController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Student\DeleteStudent::class
);

$router->post(
	'/lessons',
	\MasterStudy\Lms\Http\Controllers\Lesson\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\Create::class
);

$router->put(
	'/lessons/{lesson_id}',
	\MasterStudy\Lms\Http\Controllers\Lesson\UpdateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\Update::class
);

$router->get(
	'/lessons/{lesson_id}',
	\MasterStudy\Lms\Http\Controllers\Lesson\GetController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Lesson\Get::class
);

$router->post(
	'/quizzes',
	\MasterStudy\Lms\Http\Controllers\Quiz\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Create::class
);

$router->get(
	'/quizzes/{quiz_id}',
	\MasterStudy\Lms\Http\Controllers\Quiz\GetController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Get::class
);

$router->put(
	'/quizzes/{quiz_id}',
	\MasterStudy\Lms\Http\Controllers\Quiz\UpdateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Update::class
);

$router->delete(
	'/quizzes/{quiz_id}',
	\MasterStudy\Lms\Http\Controllers\Quiz\DeleteController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\Delete::class
);

$router->put(
	'/quizzes/{quiz_id}/questions',
	\MasterStudy\Lms\Http\Controllers\Quiz\UpdateQuestionsController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\UpdateQuestions::class
);

$router->get(
	'/questions/categories',
	\MasterStudy\Lms\Http\Controllers\Question\GetCategoriesController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\GetCategories::class
);

$router->post(
	'/questions/category',
	\MasterStudy\Lms\Http\Controllers\Question\CreateCategoryController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\CreateCategory::class
);

$router->post(
	'/questions',
	\MasterStudy\Lms\Http\Controllers\Question\CreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Create::class
);

$router->post(
	'/questions/bulk',
	\MasterStudy\Lms\Http\Controllers\Question\BulkCreateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\BulkCreate::class
);

$router->get(
	'/questions/{question_id}',
	\MasterStudy\Lms\Http\Controllers\Question\GetController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Get::class
);

$router->put(
	'/questions/{question_id}',
	\MasterStudy\Lms\Http\Controllers\Question\UpdateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Update::class
);

$router->delete(
	'/questions/{question_id}',
	\MasterStudy\Lms\Http\Controllers\Question\DeleteController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Question\Delete::class
);

$router->get(
	'/certificates',
	\MasterStudy\Lms\Http\Controllers\Certificates\GetCertificatesController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Certificates\GetCertificates::class
);

$router->delete(
	'/certificates/{certificate_id}',
	\MasterStudy\Lms\Http\Controllers\Certificates\DeleteCertificateController::class,
	\MasterStudy\Lms\Routing\Swagger\Routes\Certificates\DeleteCertificate::class
);

/**
 * Media routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
		),
	),
	function ( Router $router ) {
		$router->post(
			'/media',
			\MasterStudy\Lms\Http\Controllers\Media\UploadController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Media\Upload::class
		);

		$router->delete(
			'/media/{media_id}',
			\MasterStudy\Lms\Http\Controllers\Media\DeleteController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Media\Delete::class
		);

		$router->post(
			'/media/from-url',
			\MasterStudy\Lms\Http\Controllers\Media\UploadFromUrlController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Media\UploadFromUrl::class,
		);
	}
);

/**
 * Course Template Routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
		),
		'prefix'     => '/course-templates',
	),
	function ( Router $router ) {
		$router->post(
			'/modify-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\ModifyCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Modify::class
		);
		$router->put(
			'/update-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\UpdateCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Update::class
		);
		$router->post(
			'/duplicate-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\DuplicateCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Duplicate::class
		);
		$router->post(
			'/page-to-course-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\SavePageToCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\SavePage::class
		);
		$router->post(
			'/assign-category-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\AssignCategoryToTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\AssignCategory::class
		);
		$router->delete(
			'/delete-template/{template_id}',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\DeleteCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Delete::class
		);
		$router->post(
			'/create-template',
			\MasterStudy\Lms\Http\Controllers\Course\CourseTemplate\CreateCourseTemplateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\CourseTemplate\Create::class
		);
	}
);

/**
 * Comments routes
 */
$router->group(
	array(
		'middleware' => apply_filters(
			'masterstudy_lms_routes_middleware',
			array(
				\MasterStudy\Lms\Routing\Middleware\Authentication::class,
				\MasterStudy\Lms\Routing\Middleware\Instructor::class,
				\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
				\MasterStudy\Lms\Routing\Middleware\CommentGuard::class,
			)
		),
		'prefix'     => '/comments',
	),
	function ( Router $router ) {
		$router->get(
			'/{post_id}',
			\MasterStudy\Lms\Http\Controllers\Comment\GetController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Get::class,
		);

		$router->post(
			'/{post_id}',
			\MasterStudy\Lms\Http\Controllers\Comment\CreateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Create::class,
		);

		$router->post(
			'/{comment_id}/reply',
			\MasterStudy\Lms\Http\Controllers\Comment\ReplyController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Reply::class,
		);

		$router->post(
			'/{comment_id}/approve',
			\MasterStudy\Lms\Http\Controllers\Comment\ApproveController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Approve::class,
		);

		$router->post(
			'/{comment_id}/unapprove',
			\MasterStudy\Lms\Http\Controllers\Comment\UnapproveController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Unapprove::class,
		);

		$router->post(
			'/{comment_id}/spam',
			\MasterStudy\Lms\Http\Controllers\Comment\SpamController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Spam::class,
		);

		$router->post(
			'/{comment_id}/unspam',
			\MasterStudy\Lms\Http\Controllers\Comment\UnspamController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Unspam::class,
		);

		$router->post(
			'/{comment_id}/trash',
			\MasterStudy\Lms\Http\Controllers\Comment\TrashController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Trash::class,
		);

		$router->post(
			'/{comment_id}/untrash',
			\MasterStudy\Lms\Http\Controllers\Comment\UntrashController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Untrash::class,
		);

		$router->post(
			'/{comment_id}/update',
			\MasterStudy\Lms\Http\Controllers\Comment\UpdateController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Comment\Update::class,
		);
	}
);
/**
 * Gutenberg Blocks routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\PostGuard::class,
		),
		'prefix'     => '/blocks',
	),
	function ( Router $router ) {
		$router->get(
			'/course-levels',
			\MasterStudy\Lms\Http\Controllers\Blocks\Course\GetLevelsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course\GetLevels::class,
		);
		$router->get(
			'/course-statuses',
			\MasterStudy\Lms\Http\Controllers\Blocks\Course\GetStatusesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course\GetStatuses::class,
		);
		$router->get(
			'/settings',
			\MasterStudy\Lms\Http\Controllers\Blocks\GetSettingsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\GetSettings::class,
		);
	}
);

/**
 * Order routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Administrator::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/all-orders',
			\MasterStudy\Lms\Http\Controllers\Order\GetOrdersController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\GetOrders::class
		);
		$router->get(
			'/orders/{order_id}',
			\MasterStudy\Lms\Http\Controllers\Order\GetOrderController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\GetOrder::class
		);
		$router->post(
			'/orders-bulk-update',
			\MasterStudy\Lms\Http\Controllers\Order\BulkUpdateOrdersController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\BulkUpdateOrder::class
		);
	}
);

/**
 * Public routes
 */
$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Guest::class,
		),
	),
	function ( Router $router ) {
		$router->get(
			'/courses',
			\MasterStudy\Lms\Http\Controllers\Course\GetCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetCourses::class
		);
		$router->get(
			'/course-categories',
			\MasterStudy\Lms\Http\Controllers\Blocks\Course\GetCategoriesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Blocks\Course\GetCategories::class,
		);
		$router->get(
			'/users',
			'\MasterStudy\Lms\Http\Controllers\User\UserController@search',
		);
		$router->get(
			'/orders',
			\MasterStudy\Lms\Http\Controllers\Order\GetUserOrdersController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\GetUserOrders::class
		);
		$router->get(
			'/enrolled-quizzes',
			\MasterStudy\Lms\Http\Controllers\Quiz\GetEnrolledQuizzesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\GetEnrolledQuizzes::class
		);
		$router->get(
			'/quiz/attempts',
			\MasterStudy\Lms\Http\Controllers\Quiz\GetQuizAttemptsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\GetQuizAttempts::class
		);
		$router->get(
			'/quiz/attempt',
			\MasterStudy\Lms\Http\Controllers\Quiz\GetQuizAttemptController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Quiz\GetQuizAttempt::class
		);
		$router->get(
			'/instructor-public-courses',
			\MasterStudy\Lms\Http\Controllers\Course\GetInstructorPublicCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetInstructorPublicCourses::class
		);
		$router->get(
			'/instructor-reviews',
			\MasterStudy\Lms\Http\Controllers\Review\GetInstructorReviewsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Review\GetInstructorReviews::class
		);
		$router->get(
			'/student-courses',
			\MasterStudy\Lms\Http\Controllers\Course\GetStudentCoursesController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Course\GetStudentCourses::class
		);
		$router->get(
			'/student/stats/{student_id}',
			\MasterStudy\Lms\Http\Controllers\Student\GetStudentStatsController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Student\GetStudentStats::class
		);
	}
);

$router->group(
	array(
		'middleware' => array(
			\MasterStudy\Lms\Routing\Middleware\Authentication::class,
			\MasterStudy\Lms\Routing\Middleware\Instructor::class,
		),
	),
	function ( Router $router ) {
		$router->put(
			'/orders/{order_id}',
			\MasterStudy\Lms\Http\Controllers\Order\UpdateOrderController::class,
			\MasterStudy\Lms\Routing\Swagger\Routes\Order\UpdateOrder::class
		);
	}
);
