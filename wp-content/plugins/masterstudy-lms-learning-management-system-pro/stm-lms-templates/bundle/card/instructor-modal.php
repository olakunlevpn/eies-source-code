<?php
/**
 * @var $bundle
 */
?>
<div class="masterstudy-bundle-instructor-actions__modal">
	<div class="masterstudy-bundle-instructor-actions__modal-list">
		<span
			class="masterstudy-bundle-instructor-actions__modal-link"
			data-bundle-action="toggle-status"
			data-bundle-id="<?php echo esc_attr( $bundle['id'] ); ?>"
		>
			<?php
			echo esc_html(
				( 'publish' === $bundle['status'] )
					? __( 'Move to drafts', 'masterstudy-lms-learning-management-system-pro' )
					: __( 'Publish', 'masterstudy-lms-learning-management-system-pro' )
			);
			?>
		</span>
		<a
			class="masterstudy-bundle-instructor-actions__modal-link"
			href="<?php echo esc_url( $bundle['edit_url'] ); ?>"
			target="_blank"
			rel="noopener noreferrer"
		>
			<?php echo esc_html__( 'Edit', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</a>
		<span
			class="masterstudy-bundle-instructor-actions__modal-link"
			data-bundle-action="delete"
			data-bundle-id="<?php echo esc_attr( $bundle['id'] ); ?>"
		>
			<?php echo esc_html__( 'Delete', 'masterstudy-lms-learning-management-system-pro' ); ?>
		</span>
	</div>
</div>
