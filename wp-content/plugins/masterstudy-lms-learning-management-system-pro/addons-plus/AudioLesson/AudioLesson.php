<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\AudioLesson;

use MasterStudy\Lms\Plugin;
use MasterStudy\Lms\Plugin\Addon;
use MasterStudy\Lms\Plugin\Addons;

final class AudioLesson implements Addon {
	public function get_name(): string {
		//@TODO Remove condition
		return defined( 'Addons::AUDIO_LESSON' )
			? Addons::AUDIO_LESSON
			: 'audio_lesson';
	}

	public function register( Plugin $plugin ): void {
		$plugin->load_file( __DIR__ . '/filters.php' );
	}
}
