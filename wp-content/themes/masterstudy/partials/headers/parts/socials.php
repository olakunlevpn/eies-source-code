<?php global $stm_option; ?>
<div class="pull-right">
	<div class="header_top_bar_socs">
		<ul class="clearfix">
			<?php
			if ( ! empty( $stm_option['top_bar_use_social'] ) ) {
				foreach ( $stm_option['top_bar_use_social'] as $key => $val ) {
					if ( ! empty( $stm_option[ $key ] ) && ( '1' === $val || 1 === $val ) ) {
						$icon = 'fab fa-' . ( ( 'youtube-play' === $key ) ? 'youtube-square' : $key );
						if ( 'twitter' === $key ) {
							$icon = 'fa-brands fa-x-twitter';
						}
						echo wp_kses_post( "<li><a href='{$stm_option[$key]}'><i class='$icon'></i></a></li>" );
					}
				}
			}
			?>
		</ul>
	</div>
</div>
