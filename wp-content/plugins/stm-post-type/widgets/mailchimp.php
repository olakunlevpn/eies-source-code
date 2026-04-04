<?php

class Stm_Mailchimp_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'mailchimp', // Base ID
			__( 'MailChimp', 'stm-post-type' ), // Name
			array( 'description' => __( 'MailChimp widget', 'stm-post-type' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$style = (!empty($args['title_color'])) ? 'style="color: ' . $args['title_color'] . ';"' : '';
		$args['before_title'] = '<h5 class="stm_subscribe_title" ' . $style . '>';
		$args['after_title'] = '</h5>';
		echo stm_post_type_filtered_output($args['before_widget']);
		if ( ! empty( $title ) ) {
			echo stm_post_type_filtered_output($args['before_title']) . esc_html( $title ) . $args['after_title'];
		}

		$html = '';
		
		$mailchimp_key = stm_option('mailchimp_api_key');
		$mailchimp_list = stm_option('mailchimp_list_id');

		if( $mailchimp_key and $mailchimp_list ){
			$html .= '<form action="/" class="stm_subscribe_' . time() . '">';
			$html .= '<div class="stm_mailchimp_unit">';
			$html .= '<div class="form-group">';
			$html .= '<input type="email" name="email" class="form-control stm_subscribe_email" placeholder="' . __( 'Enter your E-mail', 'stm-post-type' ) . '" required/>';
			$html .= '</div>';
			$html .= '<button class="button"><span class="h5">' . __( 'Subscribe', 'stm-post-type' ) . '</span></button>';
			$html .= '<div class="stm_subscribe_preloader">' . __( 'Please wait...', 'stm-post-type' ) . '</div>';
			$html .= '</div>';
			$html .= '</form>';
			$html .= '
			<script type="text/javascript">
				jQuery(document).ready( function($){
					$(".stm_subscribe_' . time() . '").on(\'submit\', function (e) {
						e.preventDefault;
					    var $this = $(this);
						$(".stm_subscribe_preloader").addClass("loading");
				        $.ajax({
				            type: \'POST\',
				            data: \'action=stm_subscribe&email=\' + $($this).find(".stm_subscribe_email").val(),
				            dataType: \'json\',
				            url: ajaxurl,
				            success: function (json) {
				                if (json[\'success\']) {
				                    $($this).replaceWith(\'<div class="success_message">\' + json[\'success\'] + \'</div>\');
				                }
				                if (json[\'error\']) {
				                    alert(json[\'error\']);
				                }
				                $(".stm_subscribe_preloader").removeClass("loading");
				            }
				        });

				        return false;
				    });
				})
			</script>
		';
		}else{
			$html .= __( 'Error API', 'stm-post-type' );
		}

		echo balanceTags( $html, true );


		echo stm_post_type_filtered_output($args['after_widget']);
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Newsletter', 'stm-post-type' );
		}


		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'stm-post-type' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<?php printf(__('To setup this widget please follow this <a href="%s">link</a>', 'stm-post-type'), get_admin_url( '', 'customize.php' )); ?>
		</p>
	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                     = array();
		$instance['title']            = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}

function register_mailchimp_widget() {
	register_widget( 'Stm_Mailchimp_Widget' );
}

add_action( 'widgets_init', 'register_mailchimp_widget' );