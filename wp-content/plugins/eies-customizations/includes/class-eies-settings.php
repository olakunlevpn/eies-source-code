<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Custom_Settings {

	const OPT_GROUP = 'eies_customizations';
	const OPT_NAME  = 'eies_customizations_options';

	public static function defaults() {
		return array(
			'founding_year'   => '',
			'student_roles'   => 'subscriber,customer',
			'instructor_role' => 'stm_lms_instructor',
			'cache_ttl'       => HOUR_IN_SECONDS,
			'number_format'   => 'bo',
		);
	}

	public static function get( $key, $fallback = null ) {
		$opts = wp_parse_args( get_option( self::OPT_NAME, array() ), self::defaults() );
		return isset( $opts[ $key ] ) ? $opts[ $key ] : $fallback;
	}

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_eies_custom_flush_cache', array( $this, 'handle_flush_cache' ) );
	}

	public function register_menu() {
		add_options_page(
			__( 'EIES Customizations', 'eies-customizations' ),
			__( 'EIES Customizations', 'eies-customizations' ),
			'manage_options',
			'eies-customizations',
			array( $this, 'render_page' )
		);
	}

	public function register_settings() {
		register_setting( self::OPT_GROUP, self::OPT_NAME, array(
			'type'              => 'array',
			'sanitize_callback' => array( $this, 'sanitize' ),
			'default'           => self::defaults(),
		) );
	}

	public function sanitize( $input ) {
		$clean = self::defaults();

		if ( isset( $input['founding_year'] ) ) {
			$year = (int) $input['founding_year'];
			$clean['founding_year'] = ( $year > 1900 && $year <= (int) date( 'Y' ) ) ? (string) $year : '';
		}
		if ( isset( $input['student_roles'] ) ) {
			$clean['student_roles'] = sanitize_text_field( $input['student_roles'] );
		}
		if ( isset( $input['instructor_role'] ) ) {
			$clean['instructor_role'] = sanitize_text_field( $input['instructor_role'] );
		}
		if ( isset( $input['cache_ttl'] ) ) {
			$ttl = (int) $input['cache_ttl'];
			$clean['cache_ttl'] = max( 60, $ttl );
		}
		if ( isset( $input['number_format'] ) ) {
			$clean['number_format'] = in_array( $input['number_format'], array( 'bo', 'us', 'raw' ), true ) ? $input['number_format'] : 'bo';
		}

		// Flush stat transients on save.
		EIES_Custom_Stats::flush_cache();

		return $clean;
	}

	public function handle_flush_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permiso denegado', 'eies-customizations' ) );
		}
		check_admin_referer( 'eies_custom_flush_cache' );

		EIES_Custom_Stats::flush_cache();

		wp_safe_redirect( add_query_arg( 'flushed', '1', admin_url( 'options-general.php?page=eies-customizations' ) ) );
		exit;
	}

	public function render_page() {
		$opts = wp_parse_args( get_option( self::OPT_NAME, array() ), self::defaults() );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'EIES Customizations', 'eies-customizations' ); ?></h1>

			<?php if ( isset( $_GET['flushed'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Caché actualizado.', 'eies-customizations' ); ?></p>
				</div>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( self::OPT_GROUP ); ?>

				<h2 class="title"><?php esc_html_e( 'Estadísticas de la página de inicio', 'eies-customizations' ); ?></h2>
				<p><?php esc_html_e( 'Configuración para los shortcodes [eies_stat] usados en la sección "Logros de la Institución".', 'eies-customizations' ); ?></p>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="eies_founding_year"><?php esc_html_e( 'Año de fundación', 'eies-customizations' ); ?></label>
						</th>
						<td>
							<input type="number" min="1900" max="<?php echo (int) date( 'Y' ); ?>" id="eies_founding_year" name="<?php echo esc_attr( self::OPT_NAME ); ?>[founding_year]" value="<?php echo esc_attr( $opts['founding_year'] ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Se usa para calcular "Años formando profesionales". Ejemplo: 2008', 'eies-customizations' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="eies_student_roles"><?php esc_html_e( 'Roles de estudiantes', 'eies-customizations' ); ?></label>
						</th>
						<td>
							<input type="text" id="eies_student_roles" name="<?php echo esc_attr( self::OPT_NAME ); ?>[student_roles]" value="<?php echo esc_attr( $opts['student_roles'] ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( 'Separar con coma. Roles de WP que cuentan como estudiantes (ej: subscriber,customer).', 'eies-customizations' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="eies_instructor_role"><?php esc_html_e( 'Rol de docente', 'eies-customizations' ); ?></label>
						</th>
						<td>
							<input type="text" id="eies_instructor_role" name="<?php echo esc_attr( self::OPT_NAME ); ?>[instructor_role]" value="<?php echo esc_attr( $opts['instructor_role'] ); ?>" class="regular-text">
							<p class="description"><?php esc_html_e( 'Rol de WP que cuenta como docente (ej: stm_lms_instructor).', 'eies-customizations' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="eies_cache_ttl"><?php esc_html_e( 'TTL de caché (segundos)', 'eies-customizations' ); ?></label>
						</th>
						<td>
							<input type="number" min="60" id="eies_cache_ttl" name="<?php echo esc_attr( self::OPT_NAME ); ?>[cache_ttl]" value="<?php echo esc_attr( (int) $opts['cache_ttl'] ); ?>" class="small-text">
							<p class="description"><?php esc_html_e( 'Cuánto tiempo cachear los conteos antes de volver a consultar la base de datos. Por defecto: 3600 (1 hora).', 'eies-customizations' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Formato de números', 'eies-customizations' ); ?></th>
						<td>
							<label><input type="radio" name="<?php echo esc_attr( self::OPT_NAME ); ?>[number_format]" value="bo" <?php checked( $opts['number_format'], 'bo' ); ?>> <?php esc_html_e( 'Boliviano (10.453)', 'eies-customizations' ); ?></label><br>
							<label><input type="radio" name="<?php echo esc_attr( self::OPT_NAME ); ?>[number_format]" value="us" <?php checked( $opts['number_format'], 'us' ); ?>> <?php esc_html_e( 'Internacional (10,453)', 'eies-customizations' ); ?></label><br>
							<label><input type="radio" name="<?php echo esc_attr( self::OPT_NAME ); ?>[number_format]" value="raw" <?php checked( $opts['number_format'], 'raw' ); ?>> <?php esc_html_e( 'Sin separador (10453)', 'eies-customizations' ); ?></label>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Mantenimiento', 'eies-customizations' ); ?></h2>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="eies_custom_flush_cache">
				<?php wp_nonce_field( 'eies_custom_flush_cache' ); ?>
				<p><?php esc_html_e( 'Forzar recálculo inmediato de todos los contadores (útil después de importar cursos o usuarios).', 'eies-customizations' ); ?></p>
				<?php submit_button( __( 'Refrescar caché ahora', 'eies-customizations' ), 'secondary', 'submit', false ); ?>
			</form>

			<hr>

			<h2><?php esc_html_e( 'Shortcodes disponibles', 'eies-customizations' ); ?></h2>
			<?php $preview = EIES_Custom_Stats::preview_all(); ?>
			<table class="widefat striped" style="max-width:700px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Shortcode', 'eies-customizations' ); ?></th>
						<th><?php esc_html_e( 'Valor actual', 'eies-customizations' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr><td><code>[eies_stat type="years"]</code></td><td><?php echo esc_html( $preview['years'] ); ?></td></tr>
					<tr><td><code>[eies_stat type="courses"]</code></td><td><?php echo esc_html( $preview['courses'] ); ?></td></tr>
					<tr><td><code>[eies_stat type="students"]</code></td><td><?php echo esc_html( $preview['students'] ); ?></td></tr>
					<tr><td><code>[eies_stat type="instructors"]</code></td><td><?php echo esc_html( $preview['instructors'] ); ?></td></tr>
				</tbody>
			</table>
			<p class="description">
				<?php esc_html_e( 'Atributos opcionales: format="number|raw", prefix="+", suffix=" +".', 'eies-customizations' ); ?>
			</p>
		</div>
		<?php
	}
}
