<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\QuestionMedia;

use MasterStudy\Lms\Plugin;
use MasterStudy\Lms\Plugin\Addon;
use MasterStudy\Lms\Plugin\Addons;

final class QuestionMedia implements Addon {
	public function get_name(): string {
		//@TODO Remove condition
		return defined( 'Addons::QUESTION_MEDIA' )
			? Addons::QUESTION_MEDIA
			: 'question_media';
	}

	public function register( Plugin $plugin ): void {
		$plugin->load_file( __DIR__ . '/filters.php' );
	}
}
