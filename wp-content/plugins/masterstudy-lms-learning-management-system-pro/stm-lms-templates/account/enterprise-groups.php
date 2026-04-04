<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lms_current_user = STM_LMS_User::get_current_user( '', true, true );
do_action( 'stm_lms_template_main' );
do_action( 'masterstudy_before_account', $lms_current_user );

wp_enqueue_script( 'masterstudy-account-enterprise-groups' );
wp_enqueue_style( 'masterstudy-loader' );
wp_enqueue_style( 'masterstudy-account-enterprise-groups' );
wp_enqueue_style( 'masterstudy-account-main' );

wp_localize_script(
	'masterstudy-account-enterprise-groups',
	'stm_lms_groups',
	array(
		'translations' => array(
			'group_limit'          => esc_html__( 'Group Limit:', 'masterstudy-lms-learning-management-system-pro' ),
			'delete_group_confirm' => esc_html__( 'Do you really want to delete group', 'masterstudy-lms-learning-management-system-pro' ),
			'members'              => esc_html__( 'member(s)', 'masterstudy-lms-learning-management-system-pro' ),
			'courses'              => esc_html__( 'course(s)', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);
?>

<div class="masterstudy-account">
	<?php do_action( 'stm_lms_admin_after_wrapper_start', $lms_current_user ); ?>
	<div class="masterstudy-account-sidebar">
		<div class="masterstudy-account-sidebar__wrapper">
			<?php do_action( 'masterstudy_account_sidebar', $lms_current_user ); ?>
		</div>
	</div>
	<div class="masterstudy-account-container">
		<div class="masterstudy-account-enterprise-groups">
			<div class="masterstudy-account-enterprise-groups__header">
				<div class="masterstudy-account-enterprise-groups__header-title">
					<?php echo esc_html__( 'Your groups', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</div>
				<div class="masterstudy-account-enterprise-groups__header-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-account-enterprise-groups__import-csv',
							'class' => 'masterstudy-account-enterprise-groups__import-csv',
							'title' => esc_html__( 'Import CSV', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'secondary',
							'size'  => 'sm',
						)
					);

					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-account-enterprise-groups__add-group',
							'class' => 'masterstudy-account-enterprise-groups__add-group',
							'title' => esc_html__( 'Add Group', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'secondary',
							'size'  => 'sm',
						)
					);
					?>
				</div>
			</div>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/loader',
						array(
							'dark_mode' => false,
							'is_local'  => true,
						)
					);
					?>
			<div class="masterstudy-account-enterprise-groups__groups">
			</div>
			<div class="masterstudy-account-enterprise-groups__member-groups masterstudy-account-utility_hidden">
				<div class="masterstudy-account-enterprise-groups__member-groups-title">
					<?php echo esc_html__( 'Groups you are a member of', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</div>
				<div class="masterstudy-account-enterprise-groups__member-groups-list"></div>
			</div>
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/no-records',
				array(
					'title_items'     => esc_html__( 'You do not have groups yet.', 'masterstudy-lms-learning-management-system-pro' ),
					'container_class' => 'masterstudy-account-enterprise-groups__no-records masterstudy-account-utility_hidden',
					'icon'            => 'stmlms-group-users',
					'default_slot'    => 'masterstudy-account-enterprise-groups__no-records-template',
				)
			);
			?>

			<?php
			STM_LMS_Templates::show_lms_template(
				'account/parts/enterprise-groups/create-edit-drawer',
			);

			STM_LMS_Templates::show_lms_template(
				'account/parts/enterprise-groups/view-drawer'
			);

			STM_LMS_Templates::show_lms_template(
				'account/parts/enterprise-groups/import-csv-modal'
			);
			?>

			<template id="masterstudy-account-enterprise-groups__group">
				<div class="masterstudy-account-enterprise-groups__group">
					<div class="masterstudy-account-enterprise-groups__group-header">
						<span class="masterstudy-account-enterprise-groups__group-header-title"></span>
						<div class="masterstudy-account-enterprise-groups__group-header-action">
							<span class="stmlms-menu-dots"></span>
						</div>
						<div class="masterstudy-account-enterprise-groups__group-header-floating-menu">
							<div class="masterstudy-account-enterprise-groups__group-header-floating-menu-item" data-action="view">
								<span class="stmlms-open-eye masterstudy-account-enterprise-groups__group-header-floating-menu-item-icon"></span>
								<span class="masterstudy-account-enterprise-groups__group-header-floating-menu-item-text"><?php echo esc_html__( 'View' ); ?></span>
							</div>
							<div class="masterstudy-account-enterprise-groups__group-header-floating-menu-item" data-action="edit">
								<span class="stmlms-edit-pencil masterstudy-account-enterprise-groups__group-header-floating-menu-item-icon"></span>
								<span class="masterstudy-account-enterprise-groups__group-header-floating-menu-item-text"><?php echo esc_html__( 'Edit' ); ?></span>
							</div>
							<div class="masterstudy-account-enterprise-groups__group-header-floating-menu-item" data-action="delete">
								<span class="stmlms-delete masterstudy-account-enterprise-groups__group-header-floating-menu-item-icon"></span>
								<span class="masterstudy-account-enterprise-groups__group-header-floating-menu-item-text"><?php echo esc_html__( 'Delete' ); ?></span>
							</div>
						</div>
					</div>
					<div class="masterstudy-account-enterprise-groups__group-members">
						<div class="masterstudy-account-enterprise-groups__group-member-counter">
							<span></span>
						</div>
					</div>

					<div class="masterstudy-account-enterprise-groups__group-stats">
						<span class="masterstudy-account-enterprise-groups__group-stats-members"></span>
						<span>•</span>
						<span class="masterstudy-account-enterprise-groups__group-stats-courses"></span>
					</div>
				</div>
			</template>

			<template id="masterstudy-account-enterprise-groups__no-records-template">
				<div class="masterstudy-account-enterprise-groups__no-records-actions">
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-account-enterprise-groups__add-group',
							'class' => 'masterstudy-account-enterprise-groups__add-group',
							'title' => esc_html__( 'Add Group', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'primary',
							'size'  => 'sm',
						)
					);

					STM_LMS_Templates::show_lms_template(
						'components/button',
						array(
							'id'    => 'masterstudy-account-enterprise-groups__import-csv',
							'class' => 'masterstudy-account-enterprise-groups__import-csv',
							'title' => esc_html__( 'Import CSV', 'masterstudy-lms-learning-management-system-pro' ),
							'link'  => '#',
							'style' => 'secondary',
							'size'  => 'sm',
						)
					);
					?>
				</div>
			</template>

			<template id="masterstudy-account-enterprise-groups__group-member">
				<div class="masterstudy-account-enterprise-groups__group-member">
					<img src="#" alt="name"/>
				</div>
			</template>
			<template id="masterstudy-account-enterprise-groups__member-group">
				<div class="masterstudy-account-enterprise-groups__group">
					<div class="masterstudy-account-enterprise-groups__group-header">
						<span class="masterstudy-account-enterprise-groups__group-header-title"></span>
					</div>
					<div class="masterstudy-account-enterprise-groups__group-members">
						<div class="masterstudy-account-enterprise-groups__group-member-counter">
							<span></span>
						</div>
					</div>

					<div class="masterstudy-account-enterprise-groups__group-stats">
						<span class="masterstudy-account-enterprise-groups__group-stats-members"></span>
						<span>•</span>
						<span class="masterstudy-account-enterprise-groups__group-stats-courses"></span>
					</div>
				</div>
			</template>
		</div>
		<?php do_action( 'stm_lms_after_groups_end' ); ?>
	</div>
</div>
<?php do_action( 'masterstudy_after_account', $lms_current_user ); ?>
