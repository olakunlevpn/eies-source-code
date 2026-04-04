<?php
$users_limit = class_exists( 'STM_LMS_Enterprise_Courses' ) ? STM_LMS_Enterprise_Courses::get_group_common_limit() : 5;

wp_enqueue_style( 'masterstudy-account-enterprise-groups-create-edit-drawer' );
wp_enqueue_script( 'masterstudy-account-enterprise-groups-create-edit-drawer' );

wp_localize_script(
	'masterstudy-account-enterprise-groups-create-edit-drawer',
	'create_edit_drawer',
	array(
		'limit'        => $users_limit,
		'translations' => array(
			'add_group'  => esc_html__( 'Add new group', 'masterstudy-lms-learning-management-system-pro' ),
			'edit_group' => esc_html__( 'Edit Group', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);

STM_LMS_Templates::show_lms_template(
	'components/drawer',
	array(
		'default_slot' => 'masterstudy-account-enterprise-groups__create-drawer-template',
		'drawer_class' => 'masterstudy-account-enterprise-groups__create-drawer',
	)
);
?>

<template id="masterstudy-account-enterprise-groups__create-drawer-template">
	<div class="masterstudy-account-enterprise-groups__create-drawer-content">
		<div class="masterstudy-account-enterprise-groups__create-drawer-content__header">
			<span class="masterstudy-account-enterprise-groups__create-drawer-content__header-title"><?php echo esc_html__( 'Add new group', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
			<span class="masterstudy-account-enterprise-groups__create-drawer-content__header-close">
				<span class="stmlms-close"></span>
			</span>
		</div>
		<div class="masterstudy-account-enterprise-groups__create-drawer-content__body">
			<div class="masterstudy-account-enterprise-groups__create-drawer-content__fields">
				<div class="masterstudy-account-enterprise-groups__create-drawer-content__field">
					<span class="masterstudy-account-enterprise-groups__create-drawer-content__field-label"><?php esc_html_e( 'Group name', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
					<?php
					STM_LMS_Templates::show_lms_template(
						'components/input',
						array(
							'input_class' => 'masterstudy-account-enterprise-groups__create-drawer__group-name',
							'placeholder' => esc_html__( 'Enter group name', 'masterstudy-lms-learning-management-system-pro' ),
						)
					);
					?>
				</div>
				<div class="masterstudy-account-enterprise-groups__create-drawer-content__field">
					<span class="masterstudy-account-enterprise-groups__create-drawer-content__field-label"><?php esc_html_e( 'Add users', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
					<div class="masterstudy-account-enterprise-groups__create-drawer-content__field-input-container">
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/input',
							array(
								'input_class' => 'masterstudy-account-enterprise-groups__create-drawer-content__user-email',
								'placeholder' => esc_html__( 'Enter email', 'masterstudy-lms-learning-management-system-pro' ),
							)
						);
						?>
						<?php
						STM_LMS_Templates::show_lms_template(
							'components/button',
							array(
								'id'    => 'masterstudy-account-enterprise-groups__create-drawer-content__email-add',
								'class' => 'masterstudy-account-enterprise-groups__create-drawer-content__email-add masterstudy-account-utility_hidden',
								'title' => '',
								'link'  => '#',
								'style' => 'primary',
								'size'  => 'sm',
							)
						);
						?>
					</div>
					<span class="masterstudy-account-enterprise-groups__create-drawer-content__field-subtitle"><?php esc_html_e( 'Group Limit:', 'masterstudy-lms-learning-management-system-pro' ); ?>&nbsp;<?php echo esc_html( $users_limit ); ?></span>
				</div>
			</div>
			<div class="masterstudy-account-enterprise-groups__create-drawer-content__users"></div>
			<div class="masterstudy-account-enterprise-groups__create-drawer-content__submit">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-account-enterprise-groups__create-drawer-content__submit',
						'title' => esc_html__( 'Save', 'masterstudy-lms-learning-management-system-pro' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);
				?>
			</div>
		</div>
	</div>
</template>

<template id="masterstudy-account-enterprise-groups__group-email-item">
	<div class="masterstudy-account-enterprise-groups__group-email-item">
		<span class="masterstudy-account-enterprise-groups__group-email-item__email"></span>
		<div class="masterstudy-account-enterprise-groups__group-email-item__remove-btn">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'id'    => 'masterstudy-account-enterprise-groups__group-email-item__remove-btn',
					'title' => '',
					'link'  => '#',
					'style' => 'danger',
					'size'  => 'sm',
				)
			);
			?>
		</div>
	</div>
</template>
