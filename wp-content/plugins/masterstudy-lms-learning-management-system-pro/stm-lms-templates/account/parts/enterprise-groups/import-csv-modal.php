<?php
wp_enqueue_style( 'masterstudy-account-enterprise-groups-import-csv-modal' );
wp_enqueue_script( 'masterstudy-account-enterprise-groups-import-csv-modal' );

wp_localize_script(
	'masterstudy-account-enterprise-groups-import-csv-modal',
	'import_csv_modal',
	array(
		'translations' => array(
			'csv_file_error' => esc_html__( 'File is not CSV', 'masterstudy-lms-learning-management-system-pro' ),
		),
	)
);

STM_LMS_Templates::show_lms_template(
	'components/modal',
	array(
		'default_slot' => 'masterstudy-account-enterprise-groups__import-csv-modal-template',
		'modal_class'  => 'masterstudy-account-enterprise-groups__import-csv-modal',
	)
);

?>

<template id="masterstudy-account-enterprise-groups__import-csv-modal-template">
	<div class="masterstudy-account-enterprise-groups__import-csv-modal-content">
		<div class="masterstudy-account-enterprise-groups__import-csv-modal-content__header">
			<span class="masterstudy-account-enterprise-groups__import-csv-modal-content__header-title"><?php echo esc_html__( 'Import Groups from CSV', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
			<span class="masterstudy-account-enterprise-groups__import-csv-modal-content__header-close"><span class="stmlms-close"></span></span>
		</div>
		<div class="masterstudy-account-enterprise-groups__import-csv-modal-content__body">
			<div class="masterstudy-account-enterprise-groups__csv-select-view">
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-account-enterprise-groups__csv-select-view__select-file',
						'class' => 'masterstudy-account-enterprise-groups__csv-select-view__select-file',
						'title' => esc_html__( 'Upload file', 'masterstudy-lms-learning-management-system-pro' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);
				?>
				<input class="masterstudy-account-enterprise-groups__csv-select-view__select-file-input masterstudy-account-utility_hidden" type="file" hidden accept=".csv,text/csv" />
				<span class="masterstudy-account-enterprise-groups__csv-select-view__select-text">
					<?php esc_html_e( 'Drag files here or click the button.', 'masterstudy-lms-learning-management-system-pro' ); ?>
				</span>
			</div>

			<div class="masterstudy-account-enterprise-groups__csv-upload-view masterstudy-account-utility_hidden">
				<div class="masterstudy-account-enterprise-groups__csv-upload-view__selected-file">
					<span class="stmlms-csv"></span>
					<span class="masterstudy-account-enterprise-groups__csv-upload-view__selected-title"></span>
					<span class="stmlms-delete masterstudy-account-enterprise-groups__csv-upload-view__selected-remove"></span>
				</div>
				<?php
				STM_LMS_Templates::show_lms_template(
					'components/button',
					array(
						'id'    => 'masterstudy-account-enterprise-groups__csv-upload-view__upload-csv',
						'class' => 'masterstudy-account-enterprise-groups__csv-upload-view__upload-csv',
						'title' => esc_html__( 'Add users', 'masterstudy-lms-learning-management-system-pro' ),
						'link'  => '#',
						'style' => 'primary',
						'size'  => 'sm',
					)
				);
				?>
			</div>

			<div class="masterstudy-account-enterprise-groups__csv-error-message masterstudy-account-utility_hidden"></div>
		</div>
	</div>
</template>

