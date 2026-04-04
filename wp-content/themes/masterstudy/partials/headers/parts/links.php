<?php

/**
 * @var $args
 */

$link_1_title = esc_html__( 'Become an Instructor', 'masterstudy' );
$link_2_title = esc_html__( 'For Enterprise', 'masterstudy' );
$link_1_icon  = array(
	'value' => 'stmlms-lnr-bullhorn',
);
$link_2_icon  = array(
	'value' => 'stmlms-case',
);

$show_enterprise = class_exists( 'STM_LMS_Options' ) ? STM_LMS_Options::get_option( 'have_a_question_form', true ) : false;

if ( ! empty( $args ) ) {
	extract( $args );
}

if ( ! empty( $link_1_title ) && is_user_logged_in() ) :
	$current_user = wp_get_current_user();
	if ( ! in_array( 'stm_lms_instructor', $current_user->roles, true ) && ! in_array( 'administrator', $current_user->roles, true ) && class_exists( 'STM_LMS_User' ) ) :
		?>
		<span class="masterstudy-become-instructor-modal-parent">
		<a href="#" class="stm_lms_bi_link normal_font" data-masterstudy-modal="masterstudy-become-instructor-modal">
			<i class="<?php echo esc_attr( $link_1_icon['value'] ); ?> secondary_color"></i>
			<span><?php echo esc_html( sanitize_text_field( $link_1_title ) ); ?></span>
		</a>
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/modals/become-instructor',
			array(
				'current_user' => $current_user,
			)
		);
		?>
		</span>
		<?php
	endif;
	else :
		if ( class_exists( 'STM_LMS_User' ) ) :
			?>
		<a href="<?php echo esc_url( STM_LMS_User::login_page_url() ); ?>" class="stm_lms_bi_link normal_font">
			<i class="<?php echo esc_attr( $link_1_icon['value'] ); ?> secondary_color"></i>
			<span><?php echo esc_html( sanitize_text_field( $link_1_title ) ); ?></span>
		</a>
			<?php
		endif;
	endif;
	if ( ! empty( $link_2_title ) && $show_enterprise ) :
		?>
	<span class="masterstudy-enterprise-modal-parent">
		<a href="#" class="stm_lms_bi_link normal_font" data-masterstudy-modal="masterstudy-enterprise-modal">
			<i class="<?php echo esc_attr( $link_2_icon['value'] ); ?> secondary_color"></i>
			<span><?php echo esc_html( sanitize_text_field( $link_2_title ) ); ?></span>
		</a>
	</span>
		<?php
endif;
