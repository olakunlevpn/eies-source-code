<script>
	<?php
	ob_start();
	require STM_WPCFTO_PATH . '/metaboxes/components/addons.php';
	$template = preg_replace( '/\r|\n/', '', addslashes( ob_get_clean() ) );
	?>

	Vue.component('stm-addons', {
		props: ['enabled_addons'],
		data: function () {
			return {
				loading: false,
				addons_list: <?php echo wp_json_encode( \MasterStudy\Lms\Plugin\Addons::list(), JSON_FORCE_OBJECT ); ?>,
				addons: {}
			}
		},
		mounted: function () {
			if (this.enabled_addons) {
				this.addons = this.enabled_addons;
			}
		},
		template: '<?php echo stm_wpcfto_filtered_output( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>',
		methods: {
			enableAddon: function (addon) {
				var _this = this;
				if (typeof _this['addons'][addon] === 'undefined' || _this['addons'][addon] === '') {
					_this.$set(_this.addons, addon, 'on');
				} else {
					_this.$set(_this.addons, addon, '');
				}
				_this.saveAddons(_this.addons);
			},
			saveAddons: function (adds) {
				var _this = this;
				this.loading = true;
				var data = new FormData();

				var addons = {};

				for (var prop in adds) {
					if (!adds.hasOwnProperty(prop)) continue;
					addons[prop] = adds[prop];
				}

				data.append('action', 'stm_lms_pro_save_addons');
				data.append('nonce', stm_lms_pro_nonces['stm_lms_pro_save_addons']);
				data.append('addons', JSON.stringify(addons));
				_this.$http.post(stm_wpcfto_ajaxurl, data).then(function (response) {
					_this.loading = false;
				});
			}
		},
	})
</script>
