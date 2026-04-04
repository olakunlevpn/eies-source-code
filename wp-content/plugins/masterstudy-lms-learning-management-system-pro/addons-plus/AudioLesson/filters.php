<?php
function masterstudy_lms_lesson_audio_sources() {
	$options                          = array();
	$masterstudy_lesson_audio_sources = array(
		'course_lesson_audio_type_file'      => array(
			'key'   => 'file',
			'label' => esc_html__( 'Audio FIle (mp3,ogg,wav)', 'masterstudy-lms-learning-management-system-pro' ),
		),
		'course_lesson_audio_type_ext_link'  => array(
			'key'   => 'ext_link',
			'label' => esc_html__( 'External Link', 'masterstudy-lms-learning-management-system-pro' ),
		),
		'course_lesson_audio_type_embed'     => array(
			'key'   => 'embed',
			'label' => esc_html__( 'Embed (Apple Podcasts, Soundcloud, Deezer, Spotify)', 'masterstudy-lms-learning-management-system-pro' ),
		),
		'course_lesson_audio_type_shortcode' => array(
			'key'   => 'shortcode',
			'label' => esc_html__( 'Shortcode', 'masterstudy-lms-learning-management-system-pro' ),
		),
	);

	foreach ( $masterstudy_lesson_audio_sources as $source_key => $source_value ) {
		$source_data = STM_LMS_Options::get_option( $source_key, false );
		if ( $source_data ) {
			$options[ $source_value['key'] ] = $source_value['label'];
		}
	}

	return $options;
}

add_filter( 'masterstudy_lms_lesson_audio_sources_arr', 'masterstudy_lms_lesson_audio_sources' );

add_filter(
	'masterstudy_lms_lesson_audio_sources',
	function () {
		return array_map(
			function ( $id, $label ) {
				return array(
					'id'    => $id,
					'label' => $label,
				);
			},
			array_keys( apply_filters( 'masterstudy_lms_lesson_audio_sources_arr', array() ) ),
			array_values( apply_filters( 'masterstudy_lms_lesson_audio_sources_arr', array() ) )
		);
	}
);
function masterstudy_lms_audio_lesson_course_settings_fields_fallback( $fields ) {
	return array(
		'course_lesson_audio_types'          => array(
			'group'       => 'started',
			'type'        => 'notice',
			'label'       => esc_html__( 'Preferred Audio Source', 'masterstudy-lms-learning-management-system-pro' ),
			'description' => esc_html__( 'Choose the main type/types of audio sources to use', 'masterstudy-lms-learning-management-system-pro' ),
			'value'       => false,
		),
		'course_lesson_audio_type_file'      => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'File', 'masterstudy-lms-learning-management-system-pro' ),
			'toggle'  => false,
			'columns' => '33',
			'value'   => false,
		),
		'course_lesson_audio_type_ext_link'  => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'External link', 'masterstudy-lms-learning-management-system-pro' ),
			'toggle'  => false,
			'columns' => '33',
			'value'   => false,
		),
		'course_lesson_audio_type_embed'     => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Embed', 'masterstudy-lms-learning-management-system-pro' ),
			'toggle'  => false,
			'columns' => '33',
			'value'   => false,
		),
		'course_lesson_audio_type_shortcode' => array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Shortcode', 'masterstudy-lms-learning-management-system-pro' ),
			'toggle'  => false,
			'columns' => '33',
			'group'   => 'ended',
			'value'   => false,
		),
	);
}

add_filter( 'masterstudy_lms_audio_lesson_course_settings_fields', 'masterstudy_lms_audio_lesson_course_settings_fields_fallback' );

add_filter(
	'masterstudy_lms_lesson_types',
	function ( $types ) {
		if ( apply_filters( 'masterstudy_lms_audio_allowed', true ) ) {
			$types[] = 'audio';
		}

		return $types;
	}
);
