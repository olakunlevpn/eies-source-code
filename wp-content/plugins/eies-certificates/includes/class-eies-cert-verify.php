<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Cert_Verify {

	public function __construct() {
		add_action( 'init', array( $this, 'register_rewrite' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'handle_request' ) );
		add_shortcode( 'eies_certificate_verify', array( $this, 'render_shortcode' ) );
	}

	public function register_rewrite() {
		add_rewrite_rule( '^verificar-certificado/?$', 'index.php?eies_verify=1', 'top' );
		add_rewrite_rule( '^verify-certificate/?$', 'index.php?eies_verify=1', 'top' );
		// Moodle legacy URL catcher.
		add_rewrite_rule( '^mod/customcert/verify_certificate\.php$', 'index.php?eies_verify=1', 'top' );
	}

	public function add_query_vars( $vars ) {
		$vars[] = 'eies_verify';
		return $vars;
	}

	public function handle_request() {
		if ( ! get_query_var( 'eies_verify' ) ) {
			return;
		}

		$code   = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
		$cert   = $code !== '' ? $this->find_by_code( $code ) : null;

		status_header( 200 );
		get_header();
		echo '<div class="eies-cert-wrap" style="max-width:720px;margin:60px auto;padding:30px;font-family:sans-serif;">';
		$this->render_form( $code );
		if ( $code !== '' ) {
			$this->render_result( $cert, $code );
		}
		echo '</div>';
		get_footer();
		exit;
	}

	public function render_shortcode( $atts ) {
		$code = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
		$cert = $code !== '' ? $this->find_by_code( $code ) : null;

		ob_start();
		echo '<div class="eies-cert-wrap">';
		$this->render_form( $code );
		if ( $code !== '' ) {
			$this->render_result( $cert, $code );
		}
		echo '</div>';
		return ob_get_clean();
	}

	private function find_by_code( $code ) {
		global $wpdb;
		$table = $wpdb->prefix . EIES_CERT_TABLE;
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE code = %s LIMIT 1", $code ) );
	}

	private function render_form( $code ) {
		$is_verify_endpoint = (bool) get_query_var( 'eies_verify' );
		if ( $is_verify_endpoint ) {
			$action = home_url( '/verificar-certificado/' );
		} else {
			$action = get_permalink() ? get_permalink() : home_url( '/verificar-certificado/' );
		}
		$action = esc_url( $action );
		?>
		<h2 style="margin-bottom:8px;"><?php esc_html_e( 'Verificar Certificado', 'eies-certificates' ); ?></h2>
		<p style="color:#555;margin-top:0;"><?php esc_html_e( 'Ingrese el código único del certificado para verificar su autenticidad.', 'eies-certificates' ); ?></p>
		<form method="get" action="<?php echo $action; ?>" style="margin:20px 0;">
			<input type="text" name="code" value="<?php echo esc_attr( $code ); ?>" placeholder="<?php esc_attr_e( 'Código de certificado', 'eies-certificates' ); ?>" required style="padding:10px;width:70%;border:1px solid #ccc;border-radius:4px;font-size:15px;">
			<button type="submit" style="padding:10px 20px;background:#0073aa;color:#fff;border:0;border-radius:4px;cursor:pointer;font-size:15px;"><?php esc_html_e( 'Verificar', 'eies-certificates' ); ?></button>
		</form>
		<?php
	}

	private function render_result( $cert, $code ) {
		if ( ! $cert ) {
			printf(
				'<div style="padding:20px;background:#fff0f0;border-left:4px solid #dc3232;border-radius:4px;"><strong>%s</strong><br>%s <code>%s</code></div>',
				esc_html__( 'Certificado no encontrado', 'eies-certificates' ),
				esc_html__( 'El código ingresado no corresponde a ningún certificado emitido:', 'eies-certificates' ),
				esc_html( $code )
			);
			return;
		}

		$issued = mysql2date( get_option( 'date_format' ), $cert->issued_date );
		?>
		<div style="padding:20px;background:#f0f9f0;border-left:4px solid #46b450;border-radius:4px;">
			<h3 style="margin-top:0;color:#46b450;"><?php esc_html_e( 'Certificado Verificado ✓', 'eies-certificates' ); ?></h3>
			<table style="width:100%;border-collapse:collapse;">
				<tr><td style="padding:8px 0;color:#666;width:180px;"><?php esc_html_e( 'Código', 'eies-certificates' ); ?></td><td style="padding:8px 0;"><strong><?php echo esc_html( $cert->code ); ?></strong></td></tr>
				<tr><td style="padding:8px 0;color:#666;"><?php esc_html_e( 'Estudiante', 'eies-certificates' ); ?></td><td style="padding:8px 0;"><?php echo esc_html( $cert->student_name ); ?></td></tr>
				<tr><td style="padding:8px 0;color:#666;"><?php esc_html_e( 'Curso', 'eies-certificates' ); ?></td><td style="padding:8px 0;"><?php echo esc_html( $cert->course_name ); ?></td></tr>
				<tr><td style="padding:8px 0;color:#666;"><?php esc_html_e( 'Tipo', 'eies-certificates' ); ?></td><td style="padding:8px 0;"><?php echo esc_html( $cert->cert_type ); ?></td></tr>
				<tr><td style="padding:8px 0;color:#666;"><?php esc_html_e( 'Fecha de emisión', 'eies-certificates' ); ?></td><td style="padding:8px 0;"><?php echo esc_html( $issued ); ?></td></tr>
			</table>
		</div>
		<?php
	}
}
