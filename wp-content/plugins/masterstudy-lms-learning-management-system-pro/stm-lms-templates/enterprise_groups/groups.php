<div class="stm_lms_ent_groups">
	<div class="stm_lms_ent_groups_list hidden">
		<h2><?php esc_html_e( 'My Groups', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>
	</div>

	<h4 class="stm_lms_ent_groups__no-groups">
		<i class="stmlms-ghost"></i>
		<?php esc_html_e( 'No groups found.', 'masterstudy-lms-learning-management-system-pro' ); ?>
	</h4>

	<div class="import_groups">
		<div class="file_message_error hidden"></div>

		<label class="import_groups__file hidden"></label>
		<a href="#" class="btn btn-default btn-import hidden">
			<?php esc_html_e( 'Create Groups', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</a>

		<div class="import_groups__inner">
			<input type="file"/>
			<a href="#" class="btn btn-default">
				<?php esc_html_e( 'Import Groups (csv)', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</a>
		</div>
	</div>

</div>
