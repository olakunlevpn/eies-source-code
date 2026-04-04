<div class="stm_lms_ent_groups_add_edit">
	<div class="stm_lms_ent_groups_add_edit__title">
		<a href="#" class="cancel-editing hidden">
			<span><?php esc_html_e( 'Add new group', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
		</a>

		<h2 class="stm_lms_ent_groups__edit-title"><?php esc_attr_e( 'Add Group', 'masterstudy-lms-learning-management-system-pro' ); ?></h2>
		<input type="text" class="form-control stm_lms_ent_groups__group-name-input" placeholder="<?php esc_attr_e( 'Enter group name', 'masterstudy-lms-learning-management-system-pro' ); ?>"/>
	</div>

	<!--EMAILS-->
	<div class="stm_lms_ent_groups_add_edit__emails">
		<h4>
			<?php esc_html_e( 'Add users', 'masterstudy-lms-learning-management-system-pro' ); ?>
			<span class="stm_lms_ent_groups_add_edit__email-limit">></span>
		</h4>

		<div class="stm_lms_ent_groups_add_edit__emails_new">
			<input type="text" class="form-control" placeholder="<?php esc_attr_e( 'Enter new user e-mail', 'masterstudy-lms-learning-management-system-pro' ); ?>"/>

			<i class="stmlms-arrow-return hidden"></i>
		</div>

		<div class="stm_lms_ent_groups_add_edit__emails_list">
		</div>
	</div>

	<a href="#" class="btn btn-default stm_lms_ent_groups_add_edit__submit_btn disabled">
		<span><?php esc_html_e( 'Add group', 'masterstudy-lms-learning-management-system-pro' ); ?></span>
	</a>

	<div class="stm-lms-message stm_lms_ent_groups_add_edit__response-msg hidden"></div>
</div>

