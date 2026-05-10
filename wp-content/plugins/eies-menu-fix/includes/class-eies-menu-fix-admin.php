<?php
/**
 * Admin UI under Tools → EIES Menu Fix. Simple form to choose a target menu
 * and re-run the patcher. POST/redirect pattern, CSRF protected.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EIES_Menu_Fix_Admin {

	const SLUG = 'eies-menu-fix';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_eies_menu_fix_run', array( $this, 'handle_run' ) );
	}

	public function register_menu() {
		add_management_page(
			__( 'EIES Menu Fix', 'eies-menu-fix' ),
			__( 'EIES Menu Fix', 'eies-menu-fix' ),
			'manage_options',
			self::SLUG,
			array( $this, 'render_page' )
		);
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Forbidden.', 'eies-menu-fix' ) );
		}

		$menus  = wp_get_nav_menus();
		$target = EIES_Menu_Fix::get_target_menu_id();

		$notice = '';
		$class  = 'notice-info';
		if ( isset( $_GET['eies_done'] ) ) {
			$fixed   = isset( $_GET['fixed'] ) ? (int) $_GET['fixed'] : 0;
			$scanned = isset( $_GET['scanned'] ) ? (int) $_GET['scanned'] : 0;
			$bound   = isset( $_GET['bound'] ) ? (int) $_GET['bound'] : 0;
			$notice  = sprintf(
				/* translators: 1: number patched, 2: number scanned, 3: target menu id */
				esc_html__( 'Patched %1$d of %2$d template(s); broken refs bound to menu #%3$d.', 'eies-menu-fix' ),
				$fixed,
				$scanned,
				$bound
			);
			$class   = 'notice-success';
		} elseif ( isset( $_GET['eies_error'] ) ) {
			$notice = esc_html__( 'Could not patch. Verify the selected menu exists.', 'eies-menu-fix' );
			$class  = 'notice-error';
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'EIES Menu Fix', 'eies-menu-fix' ); ?></h1>
			<p><?php esc_html_e( 'Scans every Elementor template for the MasterStudy LMS Mega Menu widget. If the widget references a menu that no longer exists (common after a fresh install or DB import), the reference is rewritten to a valid menu.', 'eies-menu-fix' ); ?></p>

			<?php if ( $notice ) : ?>
				<div class="notice <?php echo esc_attr( $class ); ?>"><p><?php echo $notice; ?></p></div>
			<?php endif; ?>

			<?php if ( empty( $menus ) ) : ?>
				<div class="notice notice-warning"><p><?php
					esc_html_e( 'No nav menus exist yet. Create at least one menu under Appearance → Menus first.', 'eies-menu-fix' );
				?></p></div>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'eies_menu_fix_run', 'eies_menu_fix_nonce' ); ?>
					<input type="hidden" name="action" value="eies_menu_fix_run">

					<table class="form-table">
						<tr>
							<th scope="row"><label for="eies-menu-fix-target"><?php esc_html_e( 'Target menu', 'eies-menu-fix' ); ?></label></th>
							<td>
								<select id="eies-menu-fix-target" name="target_menu_id">
									<?php foreach ( $menus as $menu ) : ?>
										<option value="<?php echo (int) $menu->term_id; ?>" <?php selected( $menu->term_id, $target ); ?>>
											<?php
											echo esc_html( sprintf(
												/* translators: 1: menu name, 2: term id, 3: item count */
												__( '%1$s (#%2$d, %3$d items)', 'eies-menu-fix' ),
												$menu->name,
												$menu->term_id,
												$menu->count
											) );
											?>
										</option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Any Mega Menu widget pointing at a non-existent menu will be rebound to this menu.', 'eies-menu-fix' ); ?></p>
							</td>
						</tr>
					</table>

					<?php submit_button( __( 'Patch Elementor mega menu widgets', 'eies-menu-fix' ) ); ?>
				</form>
			<?php endif; ?>
		</div>
		<?php
	}

	public function handle_run() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Forbidden.', 'eies-menu-fix' ) );
		}
		check_admin_referer( 'eies_menu_fix_run', 'eies_menu_fix_nonce' );

		$target = isset( $_POST['target_menu_id'] ) ? (int) $_POST['target_menu_id'] : 0;
		if ( ! $target || ! wp_get_nav_menu_object( $target ) ) {
			wp_safe_redirect( add_query_arg( 'eies_error', '1', admin_url( 'tools.php?page=' . self::SLUG ) ) );
			exit;
		}

		$result = EIES_Menu_Fix::patch_all( $target );

		wp_safe_redirect( add_query_arg(
			array(
				'eies_done' => '1',
				'fixed'     => (int) $result['fixed'],
				'scanned'   => (int) $result['scanned'],
				'bound'     => (int) $result['target'],
			),
			admin_url( 'tools.php?page=' . self::SLUG )
		) );
		exit;
	}
}
