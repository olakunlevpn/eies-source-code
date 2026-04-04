<?php
use MasterStudy\Lms\Plugin\Taxonomy;

class STM_LMS_Woocommerce_Coupons {

	public $taxonomy;
	public $field_include;
	public $field_exclude;

	const E_WC_COUPON_EXCLUDED_CATEGORIES = 305;

	public function __construct() {
		$this->taxonomy      = Taxonomy::COURSE_CATEGORY;
		$this->field_include = "_{$this->taxonomy}";
		$this->field_exclude = "_exclude_{$this->taxonomy}";

		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_coupon_valid' ), 10, 3 );
		add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'add_course_category_fields' ) );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'save_course_category_fields' ) );
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'validate_coupon_for_product' ), 10, 3 );
		add_filter( 'woocommerce_coupon_error', array( $this, 'category_exclusion_error' ), 10, 2 );
	}

	/**
	 * Get a list of categories that are assigned to a specific course
	 *
	 * @param  int $product_id Product id.
	 * @return array categories
	 */
	private function get_course_categories( $product_id ) {
		return wp_get_post_terms( $product_id, $this->taxonomy, array( 'fields' => 'ids' ) );
	}

	/**
	 * Get category settings for a coupon.
	 *
	 * @param $coupon_id
	 * @return array Categories settings (included and excluded course categories).
	 */
	public function get_category_settings_on_coupon( $coupon_id ) {
		return array(
			'included' => get_post_meta( $coupon_id, $this->field_include, true ),
			'excluded' => get_post_meta( $coupon_id, $this->field_exclude, true ),
		);
	}

	/**
	 * Validate the coupon based on included and/or excluded course categories.
	 *
	 * @throws Exception Throws Exception for invalid coupons.
	 * @param  bool         $valid  Whether the coupon is valid.
	 * @param  WC_Coupon    $coupon Coupon object.
	 * @param  WC_Discounts $discounts Discounts object.
	 * @return bool         $valid  True if coupon is valid, otherwise Exception will be thrown.
	 */
	public function is_coupon_valid( $valid, $coupon, $discounts = null ) {
		$coupon_settings = $this->get_category_settings_on_coupon( $coupon->get_id() );

		if ( empty( $coupon_settings['included'] ) || empty( $coupon_settings['excluded'] ) ) {
			return $valid;
		}

		$included_categories_match   = false;
		$excluded_categories_matches = 0;

		$items = $discounts->get_items();

		foreach ( $items as $item ) {
			$course_categories = $this->get_course_categories( $item->product->get_id() );

			if ( ! empty( array_intersect( $course_categories, $coupon_settings['included'] ) ) ) {
				$included_categories_match = true;
			}

			if ( ! empty( array_intersect( $course_categories, $coupon_settings['excluded'] ) ) ) {
				++$excluded_categories_matches;
			}
		}

		// Coupon has a category requirement but no products in the cart have the category.
		if ( ! $included_categories_match ) {
			throw new Exception( $coupon->get_coupon_error( WC_Coupon::E_WC_COUPON_NOT_APPLICABLE ), WC_Coupon::E_WC_COUPON_NOT_APPLICABLE ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		// All products in the cart match category exclusion rule.
		if ( count( $items ) === $excluded_categories_matches ) {
			throw new Exception( esc_html__( 'Sorry, this coupon is not applicable to the categories of selected products.', 'masterstudy-lms-learning-management-system-pro' ), self::E_WC_COUPON_EXCLUDED_CATEGORIES ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		// For a cart discount, there is at least one product in cart that matches exclusion rule.
		if ( $coupon->is_type( 'fixed_cart' ) && $excluded_categories_matches > 0 ) {
			throw new Exception( esc_html__( 'Sorry, this coupon is not applicable to the categories of selected products.', 'masterstudy-lms-learning-management-system-pro' ), self::E_WC_COUPON_EXCLUDED_CATEGORIES ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $valid;
	}

	public function add_course_category_fields( $coupon_id ) {
		$coupon_settings = $this->get_category_settings_on_coupon( $coupon_id );
		$categories      = get_terms(
			array(
				'taxonomy'   => $this->taxonomy,
				'orderby'    => 'name',
				'hide_empty' => false,
			)
		);
		?>

		<p class="form-field">
			<label for="<?php echo esc_attr( $this->field_include ); ?>">
				<?php esc_html_e( 'Course categories', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</label>
			<select
					id="<?php echo esc_attr( $this->field_include ); ?>"
					name="<?php echo esc_attr( $this->field_include ); ?>[]"
					style="width: 50%;"
					class="wc-enhanced-select"
					multiple="multiple"
					data-placeholder="<?php esc_attr_e( 'Any category', 'masterstudy-lms-learning-management-system-pro' ); ?>"
			>
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( is_array( $coupon_settings['included'] ) && in_array( $category->term_id, $coupon_settings['included'], true ) ); ?>>
						<?php echo esc_html( $category->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php
				echo wp_kses_post(
					wc_help_tip(
						esc_html__(
							'Choose which course categories the coupon applies to. Only courses in these categories will be eligible for the discount.',
							'masterstudy-lms-learning-management-system-pro'
						)
					)
				);
			?>
		</p>

		<p class="form-field">
			<label for="<?php echo esc_attr( $this->field_exclude ); ?>">
				<?php esc_html_e( 'Exclude course categories', 'masterstudy-lms-learning-management-system-pro' ); ?>
			</label>
			<select
					id="<?php echo esc_attr( $this->field_exclude ); ?>"
					name="<?php echo esc_attr( $this->field_exclude ); ?>[]"
					style="width: 50%;"
					class="wc-enhanced-select"
					multiple="multiple"
					data-placeholder="<?php esc_attr_e( 'No categories', 'masterstudy-lms-learning-management-system-pro' ); ?>"
			>
				<?php foreach ( $categories as $category ) : ?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( is_array( $coupon_settings['included'] ) && in_array( $category->term_id, $coupon_settings['excluded'], true ) ); ?>>
						<?php echo esc_html( $category->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<?php
				echo wp_kses_post(
					wc_help_tip(
						esc_html__(
							'Select course categories not eligible for this coupon. Courses in these categories will be excluded from the discount.',
							'masterstudy-lms-learning-management-system-pro'
						)
					)
				);
			?>
		</p>

		<?php
	}

	public function save_course_category_fields( $post_id ) {
		$categories         = array_map( 'intval', (array) ( $_POST[ $this->field_include ] ?? array() ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$exclude_categories = array_map( 'intval', (array) ( $_POST[ $this->field_exclude ] ?? array() ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		update_post_meta( $post_id, $this->field_include, $categories );
		update_post_meta( $post_id, $this->field_exclude, $exclude_categories );
	}

	public function validate_coupon_for_product( $is_valid, $product, $coupon ) {
		$coupon_settings = $this->get_category_settings_on_coupon( $coupon->get_id() );
		$product_terms   = $this->get_course_categories( $product->get_id() );

		if ( ! empty( $coupon_settings['included'] ) && empty( array_intersect( $product_terms, $coupon_settings['included'] ) ) ) {
			return false;
		}

		if ( ! empty( $coupon_settings['excluded'] ) && ! empty( array_intersect( $product_terms, $coupon_settings['excluded'] ) ) ) {
			return false;
		}

		return $is_valid;
	}

	/**
	 * Display a custom error message when a cart discount coupon does not validate
	 * because an excluded category was found in the cart.
	 *
	 * @param  string $err      The error message.
	 * @param  string $err_code The error code.
	 * @return string
	 */
	public function category_exclusion_error( $err, $err_code ) {
		if ( self::E_WC_COUPON_EXCLUDED_CATEGORIES !== $err_code ) {
			return $err;
		}

		return esc_html__( 'Sorry, this coupon is not applicable to the categories of selected products.', 'masterstudy-lms-learning-management-system-pro' );
	}
}

new STM_LMS_Woocommerce_Coupons();
