<?php

namespace MasterStudy\Lms\Pro\addons\email_manager;

class EmailDataCompiler {
	/**
	 * @param array $data
	 */
	public static function compile( $data ): array {
		if ( ! isset( $data['vars'] ) || empty( $data['filter_name'] ) ) {
			return $data;
		}

		$vars        = $data['vars'];
		$filter_name = $data['filter_name'];
		$settings    = EmailManagerSettings::get_all();

		// Compile subject
		if ( isset( $settings[ "{$filter_name}_subject" ] ) ) {
			$subject = $settings[ "{$filter_name}_subject" ];
			preg_match_all( '~\{\{\s*(.*?)\s*\}\}~', $subject, $subject_matches );

			$skip_keys = array(
				'certificate_preview',
				'button',
				'site_url',
				'dashboard_url',
				'course_edit_url',
				'course_url',
				'analytics_url',
				'assignment_url',
				'attempt_url',
				'text',
				'reset_url',
				'password',
				'student_email',
				'quiz_url',
				'login_url',
				'mail',
			);

			if ( ! empty( $subject_matches[0] ) && ! empty( $subject_matches[1] ) ) {
				foreach ( $subject_matches[1] as $index => $match ) {
					if ( isset( $vars[ $match ] ) && ! in_array( $match, $skip_keys, true ) ) {
						$subject = str_replace( $subject_matches[0][ $index ], $vars[ $match ], $subject );
					}
				}
			}

			$data['subject'] = $subject;
		}

		// Compile message
		if ( empty( $settings[ $filter_name ] ) ) {
			return $data;
		}

		$data['enabled'] = ( ! empty( $settings[ "{$filter_name}_enable" ] ) && $settings[ "{$filter_name}_enable" ] );
		$message         = $settings[ $filter_name ];

		preg_match_all( '~\{\{\s*(.*?)\s*\}\}~', $message, $matches );
		if ( ! empty( $matches[0] ) && ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $index => $match ) {
				if ( isset( $vars[ $match ] ) ) {
					// Check if this match is inside an <a ...href="...{{tag}}..."> or between <a ...>{{tag}}</a>
					$placeholder = $matches[0][ $index ];
					$pattern     = '/<a[^>]+href=[\'"][^\'"]*' . preg_quote( $placeholder, '/' ) . '[^\'"]*[\'"][^>]*>/i';

					if ( preg_match( $pattern, $message ) ) {
						// If variable is a full <a ...>, extract only the href URL if possible
						if ( preg_match( '/href=[\'"]([^\'"]+)[\'"]/', $vars[ $match ], $hrefMatch ) ) {
							$message = str_replace( $placeholder, $hrefMatch[1], $message );
						} else {
							// If not a link, just insert as-is
							$message = str_replace( $placeholder, $vars[ $match ], $message );
						}
					} else {
						// Not inside an <a> href, normal replace
						$message = str_replace( $placeholder, $vars[ $match ], $message );
					}
				}
			}
		}

		$data['message'] = $message;

		return $data;
	}
}
