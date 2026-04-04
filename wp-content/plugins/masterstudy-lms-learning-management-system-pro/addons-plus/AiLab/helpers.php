<?php

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Model;

function ms_lms_ai_get_available_languages() {
	if ( ! function_exists( 'wp_get_available_translations' ) ) {
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
	}

	$translations = wp_get_available_translations();

	$languages = array(
		'en_US' => 'English (United States)',
	);

	foreach ( $translations as $locale => $data ) {
		$languages[ $locale ] = $data['native_name'];
	}

	return $languages;
}

function get_max_input_tokens_by_model( string $model ): int {
	switch ( $model ) {
		case Model::GPT_35_TURBO_INSTRUCT:
		case Model::GPT_35_TURBO:
			return 4096;
		case Model::GPT_35_TURBO_16K:
			return 16384;
		case Model::GPT_4:
			return 8192;
		case Model::GPT_4_TURBO_LATEST_MINI:
		case Model::GPT_4_TURBO_LATEST:
		case Model::GPT_4_TURBO:
		case Model::GPT_41_NANO:
		case Model::GPT_41_MINI:
		case Model::GPT_41:
		case Model::GPT_4O_MINI:
		case Model::GPT_4O:
			return 128000;
		case Model::GPT_5_NANO:
		case Model::GPT_5_MINI:
		case Model::GPT_5:
			return 272000;
	}

	return 4096;
}
