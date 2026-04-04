<?php

use MasterStudy\Lms\Pro\AddonsPlus\GoogleMeet\Services\GoogleOpenAuth;

$meeting_table_columns = array(
	array(
		'title' => esc_html__( 'Title', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'title',
	),
	array(
		'title' => esc_html__( 'Date & Time', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'date_time',
	),
	array(
		'title' => esc_html__( 'Status', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'status',
	),
	array(
		'title' => esc_html__( 'Actions', 'masterstudy-lms-learning-management-system-pro' ),
		'data'  => 'actions',
	),
);

wp_enqueue_style( 'masterstudy-account-google-meets-meetings' );
wp_enqueue_style( 'masterstudy-account-google-meets-main' );
wp_enqueue_script(
	'masterstudy-account-google-meets-form',
	STM_LMS_PRO_URL . 'assets/js/google-meet/stm-google-meet-form.js',
	array( 'jquery', 'masterstudy-datepicker-component', 'masterstudy-datatables-component' ),
	STM_LMS_PRO_VERSION,
	true
);
wp_localize_script(
	'masterstudy-account-google-meets-form',
	'stm_gm_front_ajax_variable',
	array(
		'url'                   => admin_url( 'admin-ajax.php' ),
		'nonce'                 => wp_create_nonce( 'gm_front_meet_ajax' ),
		'meeting_table_columns' => $meeting_table_columns,
		'i18n'                  => array(
			'pending' => esc_html__( 'Pending', 'masterstudy-lms-learning-management-system-pro' ),
			'expired' => esc_html__( 'Expired', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);
?>

<div class="masterstudy-account-google-meets-meetings">
	<div class="masterstudy-account-google-meets-meetings__header">
		<span class="masterstudy-account-google-meets-meetings__title">
			<?php echo esc_html__( 'Google Meet', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</span>
		<div class="masterstudy-account-google-meets-meetings__header-buttons">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => esc_html__( 'Create Meeting', 'masterstudy-lms-learning-management-system-pro' ),
					'id'    => 'masterstudy-account-google-meets-meetings__header-button-create',
					'class' => 'masterstudy-account-google-meets-meetings__create-btn',
					'link'  => '#',
					'style' => 'primary',
					'size'  => 'sm',
				)
			);
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'title' => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system-pro' ),
					'link'  => '#',
					'style' => 'secondary',
					'size'  => 'sm',
					'id'    => 'masterstudy-account-google-meets-meetings__header-button-settings',
					'class' => 'masterstudy-account-google-meets-meetings__settings-btn',
				)
			);
			?>
		</div>
	</div>
	<div class="masterstudy-account-google-meets-meetings__content">
		<?php
		STM_LMS_Templates::show_lms_template( 'components/skeleton-loader', array( 'loader_type' => 'table-loader' ) );
		STM_LMS_Templates::show_lms_template(
			'components/analytics/datatable',
			array(
				'id'      => 'google-meets',
				'columns' => $meeting_table_columns,
			)
		);
		?>
	</div>
	<?php do_action( 'stm_lms_after_groups_end' ); ?>
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/no-records',
		array(
			'title_items'     => esc_html__( 'No meetings yet', 'masterstudy-lms-learning-management-system-pro' ),
			'container_class' => 'masterstudy-account-google-meets-meetings__no-records masterstudy-account-utility_hidden',
			'icon'            => 'stmlms-menu-google-meet',
			'default_slot'    => 'masterstudy-account-google-meets-meetings__no-records-template',
		)
	);
	?>
</div>
<template id="masterstudy-account-google-meets-meetings__no-records-template">
	<div class="masterstudy-account-google-meets-meetings__no-records-actions">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title' => esc_html__( 'Create Meeting', 'masterstudy-lms-learning-management-system-pro' ),
				'id'    => 'masterstudy-account-google-meets-meetings__header-button-create',
				'class' => 'masterstudy-account-google-meets-meetings__create-btn',
				'link'  => '#',
				'style' => 'primary',
				'size'  => 'sm',
			)
		);
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title' => esc_html__( 'Settings', 'masterstudy-lms-learning-management-system-pro' ),
				'link'  => '#',
				'style' => 'secondary',
				'size'  => 'sm',
				'id'    => 'masterstudy-account-google-meets-meetings__header-button-settings',
				'class' => 'masterstudy-account-google-meets-meetings__settings-btn',
			)
		);
		?>
	</div>
</template>
<template id="masterstudy-account-google-meets-meetings__actions-template">
	<div class="masterstudy-account-google-meets-meetings__actions">
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/button',
			array(
				'title' => esc_html__( 'Start meeting', 'masterstudy-lms-learning-management-system-pro' ),
				'link'  => '#',
				'style' => 'primary-light',
				'size'  => 'sm',
				'class' => 'masterstudy-account-google-meets-meetings__action-start',
			)
		);
		?>
		<div class="masterstudy-account-google-meets-meetings__actions-menu">
			<div class="masterstudy-account-google-meets-meetings__actions-menu-trigger">
				<span class="stmlms-menu-dots"></span>
			</div>
			<div class="masterstudy-account-google-meets-meetings__actions-menu-list">
				<div class="masterstudy-account-google-meets-meetings__actions-menu-item" data-action="edit">
					<span class="stmlms-edit-pencil masterstudy-account-google-meets-meetings__actions-menu-item-icon"></span>
					<span class="masterstudy-account-google-meets-meetings__actions-menu-item-text"><?php echo esc_html__( 'Edit' ); ?></span>
				</div>
				<div class="masterstudy-account-google-meets-meetings__actions-menu-item" data-action="delete">
					<span class="stmlms-delete masterstudy-account-google-meets-meetings__actions-menu-item-icon"></span>
					<span class="masterstudy-account-google-meets-meetings__actions-menu-item-text"><?php echo esc_html__( 'Delete' ); ?></span>
				</div>
			</div>
		</div>
	</div>
</template>
<?php STM_LMS_Templates::show_lms_template( 'google-meet/meeting-form', array( 'timezones' => stm_lms_get_timezone_options() ) ); ?>
<?php STM_LMS_Templates::show_lms_template( 'account/instructor/parts/google-meets/settings' ); ?>
