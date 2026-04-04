<?php

use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\OpenAi\Model;
use MasterStudy\Lms\Pro\AddonsPlus\AiLab\Services\UsageLogger;

$usage_setting = get_option( UsageLogger::$option_name, array() );
$usage_date    = array_key_last( $usage_setting );
?>

<div class="wpcfto_generic_field wpcfto_generic_field__select">
	<div class="ai-usage-section">
		<div class="section-title"><span><?php echo esc_html__( 'Usage', 'masterstudy-lms-learning-management-system-pro' ); ?></span></div>
		<div class="usage-section">
			<?php if ( ! empty( $usage_setting ) && is_array( $usage_setting ) ) : ?>
				<div class="usage-section-data"><?php echo esc_html( $usage_date ); ?> <span class="total-requests-price"></span></div>
				<div class="usage-section-content">
					<?php
					$usage_tokens = $usage_setting[ $usage_date ];
					foreach ( $usage_tokens as $token => $token_value ) :
						?>
						<div class="tokens-data">
							<div class="tokens-data-key">
								<?php echo esc_html( $token ); ?>:
							</div>
							<div class="tokens-data-tokens">
								<?php if ( isset( $token_value['total'] ) ) : ?>
									<input type="hidden" id="total-images-data" token="<?php echo esc_attr( $token ); ?>" value="<?php echo esc_attr( $token_value['total'] ); ?>" />
									<?php
								endif;
								if ( isset( $token_value['total_tokens'] ) ) :
									?>
									<input type="hidden" class="total-tokens-data" token="<?php echo esc_attr( $token ); ?>" value="<?php echo esc_attr( $token_value['total_tokens'] ); ?>" />
									<?php
								endif;
								if ( isset( $token_value['total'] ) ) {
									$image_price = Model::DALL_E_3 === $token ? 0.04 : 0.02;
									echo esc_html( $token_value['total'] ) . ' images (' . esc_html( number_format( $image_price * $token_value['total'], 2 ) ) . '$)';
								}

								if ( isset( $token_value['total_tokens'] ) ) {
									echo esc_html( $token_value['total_tokens'] ) . ' tokens (' . esc_html( number_format( $token_value['total_tokens'] / 1000 * 0.02, 2 ) ) . '$)';
								}
								?>
							</div>
						</div>
						<?php
					endforeach;
					?>
				</div>
				<?php
			else :
				echo esc_html__( 'You have not generated any content yet.', 'masterstudy-lms-learning-management-system-pro' );
			endif;
			?>
		</div>

		<div class="wpcfto-field-description wpcfto-field-description__before description">
			<?php
			printf(
				// Translators: %1$s: Open Link for account api key, %2$s: Close Link for account api key
				esc_html__( 'For detailed usage, please visit your %1$sOpenAI account%2$s. You can manage costs, set limits or conditions for AI usage.', 'masterstudy-lms-learning-management-system-pro' ),
				'<a href="https://platform.openai.com/account/usage/" target="_blank" rel="nofollow">',
				'</a>'
			);
			?>
		</div>
	</div>
</div>
