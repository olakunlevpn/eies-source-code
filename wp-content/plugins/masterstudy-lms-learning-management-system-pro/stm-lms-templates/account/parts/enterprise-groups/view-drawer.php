<?php
wp_enqueue_style( 'masterstudy-account-enterprise-groups-view-drawer' );
wp_enqueue_script( 'masterstudy-account-enterprise-groups-view-drawer' );
wp_enqueue_style( 'masterstudy-loader' );

wp_localize_script(
	'masterstudy-account-enterprise-groups-view-drawer',
	'view_drawer',
	array(
		'translations' => array(
			'admin_notice'      => esc_html__( 'You wont be able to manage group anymore. Proceed with caution!', 'masterstudy-lms-learning-management-system-pro' ),
			'remove_notice'     => esc_html__( 'Do you really want to delete this user from group?', 'masterstudy-lms-learning-management-system-pro' ),
			'add_course'        => esc_html__( 'Add Course', 'masterstudy-lms-learning-management-system-pro' ),
			'remove_course'     => esc_html__( 'Remove Course', 'masterstudy-lms-learning-management-system-pro' ),
			'set_as_admin'      => esc_html__( 'Set as admin', 'masterstudy-lms-learning-management-system-pro' ),
			'remove_from_group' => esc_html__( 'Remove from group', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);

STM_LMS_Templates::show_lms_template(
	'components/drawer',
	array(
		'default_slot' => 'masterstudy-account-enterprise-groups__view-drawer-template',
		'drawer_class' => 'masterstudy-account-enterprise-groups__view-drawer',
	)
);
?>

<template id="masterstudy-account-enterprise-groups__view-drawer-template">
	<div class="masterstudy-account-enterprise-groups__view-drawer-content">
		<div class="masterstudy-account-enterprise-groups__view-drawer-content__header">
			<span class="masterstudy-account-enterprise-groups__view-drawer-content__header-title"></span>
			<span class="masterstudy-account-enterprise-groups__view-drawer-content__header-close">
				<span class="stmlms-close"></span>
			</span>
		</div>
		<div class="masterstudy-account-enterprise-groups__view-drawer-content__body">
		</div>
		<div class="masterstudy-account-enterprise-groups__view-drawer-content-intersect"></div>
		<?php
		STM_LMS_Templates::show_lms_template(
			'components/loader',
			array(
				'dark_mode' => false,
				'is_local'  => true,
			)
		);
		?>
	</div>
</template>

<template id="masterstudy-account-enterprise-groups__view-drawer-user">
	<div class="masterstudy-account-enterprise-groups__view-drawer-user">
		<div class="masterstudy-account-enterprise-groups__view-drawer-user-header">
			<div class="masterstudy-account-enterprise-groups__view-drawer-user-info">
				<img alt="user" src="#" />
				<span></span>
			</div>
			<div class="masterstudy-account-enterprise-groups__view-drawer-user-actions">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-account-enterprise-groups__view-drawer-user-set-admin',
						'class' => 'masterstudy-account-enterprise-groups__view-drawer-user-set-admin',
						'title' => esc_html__( 'Set as admin', 'masterstudy-lms-learning-management-system-pro' ),
						'link'  => '#',
						'style' => 'secondary',
						'size'  => 'sm',
					)
				);
				?>

				<div class="masterstudy-account-enterprise-groups__view-drawer-user-remove-btn">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-account-enterprise-groups__view-drawer-user-remove',
						'class' => 'masterstudy-account-enterprise-groups__view-drawer-user-remove',
						'title' => '',
						'link'  => '#',
						'style' => 'danger',
						'size'  => 'sm',
					)
				);
				?>
				</div>
			</div>
		</div>
		<div class="masterstudy-account-enterprise-groups__view-drawer-user-courses">
		</div>
	</div>
</template>

<template id="masterstudy-account-enterprise-groups__view-drawer-course">
	<div class="masterstudy-account-enterprise-groups__view-drawer-course">
		<div class="masterstudy-account-enterprise-groups__view-drawer-course-info">
			<img alt="course" src="#" />
			<span></span>
		</div>
		<div class="masterstudy-account-enterprise-groups__view-drawer-course-actions">
			<div class="masterstudy-account-enterprise-groups__view-drawer-course-remove-btn">
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'id'    => 'masterstudy-account-enterprise-groups__view-drawer-course-remove',
					'class' => 'masterstudy-account-enterprise-groups__view-drawer-course-remove',
					'title' => '',
					'link'  => '#',
					'style' => 'danger',
					'size'  => 'sm',
				)
			);
			?>
			</div>
			<?php
			STM_LMS_Templates::show_lms_template(
				'components/button',
				array(
					'id'    => 'masterstudy-account-enterprise-groups__view-drawer-course-add',
					'class' => 'masterstudy-account-enterprise-groups__view-drawer-course-add',
					'title' => '',
					'link'  => '#',
					'style' => 'secondary',
					'size'  => 'sm',
				)
			);
			?>
		</div>
	</div>
</template>

<template id="masterstudy-account-enterprise-groups__view-drawer-no-users">
	<?php
	STM_LMS_Templates::show_lms_template(
		'components/no-records',
		array(
			'title_items'     => esc_html__( 'This group doesn\'t contain any users' ),
			'container_class' => 'masterstudy-account-enterprise-groups__view-drawer__no-records',
			'icon'            => 'stmlms-group-users',
		)
	);
	?>
</template>
