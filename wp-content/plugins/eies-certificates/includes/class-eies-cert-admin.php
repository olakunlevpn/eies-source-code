<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Cert_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'wp_ajax_eies_cert_import', array( $this, 'ajax_import' ) );
	}

	public function register_menu() {
		add_menu_page(
			__( 'Certificados', 'eies-certificates' ),
			__( 'Certificados', 'eies-certificates' ),
			'manage_options',
			'eies-certificates',
			array( $this, 'render_page' ),
			'dashicons-awards',
			58
		);
	}

	public function render_page() {
		global $wpdb;
		$table = $wpdb->prefix . EIES_CERT_TABLE;

		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$paged  = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
		$per    = 25;
		$offset = ( $paged - 1 ) * $per;

		$where     = '1=1';
		$where_arg = array();
		if ( $search !== '' ) {
			$where       = '(code LIKE %s OR student_name LIKE %s OR student_email LIKE %s OR course_name LIKE %s)';
			$like        = '%' . $wpdb->esc_like( $search ) . '%';
			$where_arg   = array( $like, $like, $like, $like );
		}

		$total_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
		$list_sql  = "SELECT * FROM {$table} WHERE {$where} ORDER BY issued_date DESC LIMIT %d OFFSET %d";

		if ( ! empty( $where_arg ) ) {
			$total = (int) $wpdb->get_var( $wpdb->prepare( $total_sql, $where_arg ) );
			$rows  = $wpdb->get_results( $wpdb->prepare( $list_sql, array_merge( $where_arg, array( $per, $offset ) ) ) );
		} else {
			$total = (int) $wpdb->get_var( $total_sql );
			$rows  = $wpdb->get_results( $wpdb->prepare( $list_sql, $per, $offset ) );
		}

		$stats          = EIES_Cert_Importer::count_remaining();
		$moodle_total   = is_array( $stats ) ? $stats['total'] : 0;
		$already_done   = is_array( $stats ) ? $stats['done'] : 0;
		$new_available  = is_array( $stats ) ? $stats['new_available'] : 0;
		$moodle_online  = is_array( $stats ) ? ! empty( $stats['moodle_online'] ) : false;
		$has_new        = $new_available > 0;
		$btn_label      = $already_done > 0
			? ( $has_new
				? sprintf( __( 'Buscar nuevos certificados (%d disponibles)', 'eies-certificates' ), $new_available )
				: __( 'Buscar nuevos certificados', 'eies-certificates' ) )
			: __( 'Importar certificados de Moodle', 'eies-certificates' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Certificados EIES', 'eies-certificates' ); ?></h1>

			<?php if ( $moodle_online ) : ?>
			<div style="background:#fff;padding:16px;border:1px solid #ccd0d4;margin:16px 0;border-radius:4px;">
				<strong><?php esc_html_e( 'Sincronización con Moodle', 'eies-certificates' ); ?></strong>
				<p style="margin:8px 0;">
					<?php printf(
						esc_html__( 'En Moodle: %1$d | Importados: %2$d | Nuevos disponibles: %3$d', 'eies-certificates' ),
						$moodle_total, $already_done, $new_available
					); ?>
				</p>
				<button id="eies-cert-import-btn" class="button button-primary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eies_cert_import' ) ); ?>">
					<?php echo esc_html( $btn_label ); ?>
				</button>
				<span id="eies-cert-import-status" style="margin-left:10px;"></span>
				<p style="margin:10px 0 0;color:#666;font-size:12px;">
					<?php esc_html_e( 'Seguro para ejecutar varias veces — solo añade certificados nuevos, nunca duplica.', 'eies-certificates' ); ?>
				</p>
			</div>
			<?php endif; ?>

			<form method="get" style="margin:16px 0;">
				<input type="hidden" name="page" value="eies-certificates">
				<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Buscar por código, nombre, email, curso', 'eies-certificates' ); ?>" style="width:400px;">
				<button class="button"><?php esc_html_e( 'Buscar', 'eies-certificates' ); ?></button>
			</form>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Código', 'eies-certificates' ); ?></th>
						<th><?php esc_html_e( 'Estudiante', 'eies-certificates' ); ?></th>
						<th><?php esc_html_e( 'Email', 'eies-certificates' ); ?></th>
						<th><?php esc_html_e( 'Curso', 'eies-certificates' ); ?></th>
						<th><?php esc_html_e( 'Fecha', 'eies-certificates' ); ?></th>
						<th><?php esc_html_e( 'Origen', 'eies-certificates' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $rows ) ) : ?>
						<tr><td colspan="6"><?php esc_html_e( 'No hay certificados.', 'eies-certificates' ); ?></td></tr>
					<?php else : foreach ( $rows as $r ) : ?>
						<tr>
							<td><code><?php echo esc_html( $r->code ); ?></code></td>
							<td><?php echo esc_html( $r->student_name ); ?></td>
							<td><?php echo esc_html( $r->student_email ); ?></td>
							<td><?php echo esc_html( $r->course_name ); ?></td>
							<td><?php echo esc_html( mysql2date( 'Y-m-d', $r->issued_date ) ); ?></td>
							<td><?php echo esc_html( $r->source ); ?></td>
						</tr>
					<?php endforeach; endif; ?>
				</tbody>
			</table>

			<?php
			$pages = max( 1, (int) ceil( $total / $per ) );
			if ( $pages > 1 ) {
				echo '<div class="tablenav"><div class="tablenav-pages">';
				echo paginate_links( array(
					'base'    => add_query_arg( 'paged', '%#%' ),
					'format'  => '',
					'current' => $paged,
					'total'   => $pages,
				) );
				echo '</div></div>';
			}
			?>
		</div>

		<script>
		(function($){
			$('#eies-cert-import-btn').on('click', function(){
				var btn = $(this), status = $('#eies-cert-import-status');
				btn.prop('disabled', true);
				function runBatch(){
					status.html('<em><?php echo esc_js( __( 'Importando...', 'eies-certificates' ) ); ?></em>');
					$.post(ajaxurl, {
						action: 'eies_cert_import',
						_wpnonce: btn.data('nonce')
					}, function(res){
						if (!res.success) {
							status.html('<span style="color:#dc3232;">' + (res.data || 'Error') + '</span>');
							btn.prop('disabled', false);
							return;
						}
						var totalImported = (typeof window._eiesImportedSoFar === 'number' ? window._eiesImportedSoFar : 0) + parseInt(res.data.imported || 0, 10);
						window._eiesImportedSoFar = totalImported;
						status.html('<?php echo esc_js( __( 'Nuevos añadidos', 'eies-certificates' ) ); ?>: ' + totalImported + ' | <?php echo esc_js( __( 'Pendientes', 'eies-certificates' ) ); ?>: ' + res.data.new_available);
						if (res.data.imported > 0 && res.data.new_available > 0) {
							setTimeout(runBatch, 200);
						} else if (totalImported === 0) {
							status.html('<strong style="color:#0073aa;">✓ <?php echo esc_js( __( 'Sin nuevos certificados. Todo está al día.', 'eies-certificates' ) ); ?></strong>');
							btn.prop('disabled', false);
						} else {
							status.append(' <strong style="color:#46b450;">✓ <?php echo esc_js( __( 'Completado', 'eies-certificates' ) ); ?></strong>');
							btn.prop('disabled', false);
							setTimeout(function(){ location.reload(); }, 1500);
						}
					}).fail(function(){
						status.html('<span style="color:#dc3232;">Error de red</span>');
						btn.prop('disabled', false);
					});
				}
				runBatch();
			});
		})(jQuery);
		</script>
		<?php
	}

	public function ajax_import() {
		check_ajax_referer( 'eies_cert_import' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permiso denegado', 'eies-certificates' ) );
		}

		$result = EIES_Cert_Importer::import_batch();
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}
		wp_send_json_success( $result );
	}
}
