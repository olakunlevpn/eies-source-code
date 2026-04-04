<?php

namespace MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Http\Serializers;

use MasterStudy\Lms\Http\Serializers\AbstractSerializer;
use WP_Post;

final class MeetingsListSerializer extends AbstractSerializer {
	public function toArray( $data ): array {
		$meeting_id = $data instanceof WP_Post ? $data->ID : 0;

		if ( ! $meeting_id ) {
			return array();
		}

		$meet_post_metas = get_post_meta( $meeting_id );
		$meet_name       = get_the_title( $meeting_id ) ?? '';

		$meet_gma_date     = $meet_post_metas['stm_gma_start_date'][0] ?? '';
		$meet_gma_time     = $meet_post_metas['stm_gma_start_time'][0] ?? '';
		$meet_gma_date_end = $meet_post_metas['stm_gma_end_date'][0] ?? '';
		$meet_gma_time_end = $meet_post_metas['stm_gma_end_time'][0] ?? '';
		$meet_link         = $meet_post_metas['google_meet_link'][0] ?? '';

		$meet_gma_date     = $this->format_date( $meet_gma_date );
		$meet_gma_time     = $this->format_time( $meet_gma_time );
		$meet_gma_date_end = $this->format_date( $meet_gma_date_end );
		$meet_gma_time_end = $this->format_time( $meet_gma_time_end );

		return array(
			'meeting_id'      => $meeting_id,
			'title'           => $meet_name,
			'date_time'       => trim( $meet_gma_date . ' — ' . $meet_gma_time, ' —' ),
			'date_time_end'   => trim( $meet_gma_date_end . ' — ' . $meet_gma_time_end, ' —' ),
			'meeting_url'     => $meet_link,
			'is_meet_started' => masterstudy_lms_is_google_meet_started( $meeting_id ),
			'delete_url'      => get_delete_post_link( $meeting_id ),
		);
	}

	private function format_date( $timestamp ): string {
		if ( empty( $timestamp ) ) {
			return '';
		}

		return gmdate( 'j M Y', strtotime( gmdate( 'Y-m-d H:i:s', (int) $timestamp / 1000 ) ) );
	}

	private function format_time( $time ): string {
		if ( empty( $time ) ) {
			return '';
		}

		return gmdate( 'g:i A', strtotime( $time ) );
	}
}
