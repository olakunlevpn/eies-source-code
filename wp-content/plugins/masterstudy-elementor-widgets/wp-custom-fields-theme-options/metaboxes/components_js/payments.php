<script type="text/javascript">
	<?php
	ob_start();
	include STM_WPCFTO_PATH .'/metaboxes/components/payments.php';
	$template = preg_replace("/\r|\n/", "", addslashes(ob_get_clean()));
	?>
    Vue.component('stm-payments', {
        props: ['saved_payments'],
        data: function () {
            return {
                payment_values : {},
                payments: {
                    cash: {
                        enabled: '',
                        name: "<?php esc_html_e('Offline payment', 'wp-custom-fields-theme-options'); ?>",
                        fields: {
                            description: {
                                type: 'textarea',
                                placeholder: '<?php esc_html_e('Payment method description', 'wp-custom-fields-theme-options'); ?>'
                            },
                        },
                    },
                    wire_transfer: {
                        enabled: '',
                        name: "<?php esc_html_e('Wire Transfer', 'wp-custom-fields-theme-options'); ?>",
                        fields: {
                            account_number: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('Account number', 'wp-custom-fields-theme-options'); ?>'
                            },
                            holder_name: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('Holder name', 'wp-custom-fields-theme-options'); ?>'
                            },
                            bank_name: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('Bank name', 'wp-custom-fields-theme-options'); ?>'
                            },
                            swift: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('Swift', 'wp-custom-fields-theme-options'); ?>'
                            },
                            description: {
                                type: 'textarea',
                                placeholder: '<?php esc_html_e('Payment method description', 'wp-custom-fields-theme-options'); ?>'
                            },
                        },
                    },
                    paypal: {
                        enabled: '',
                        name: "<?php esc_html_e('Paypal', 'wp-custom-fields-theme-options'); ?>",
                        fields: {
                            paypal_email: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('PayPal Email', 'wp-custom-fields-theme-options'); ?>'
                            },
                            currency_code: {
                                type: 'select',
                                source: 'codes',
                                value : 'USD',
                                placeholder: '<?php esc_html_e('Currency code', 'wp-custom-fields-theme-options'); ?>'
                            },
                            paypal_mode: {
                                type: 'select',
                                source: 'modes',
                                value : 'sandbox',
                                placeholder: '<?php esc_html_e('PayPal mode', 'wp-custom-fields-theme-options'); ?>'
                            },
                            description: {
                                type: 'textarea',
                                placeholder: '<?php esc_html_e('Payment method description', 'wp-custom-fields-theme-options'); ?>'
                            },
                        },
                    },
                    stripe: {
                        enabled: '',
                        name: "<?php esc_html_e('Stripe', 'wp-custom-fields-theme-options'); ?>",
                        fields: {
                            stripe_public_api_key: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('Publishable key', 'wp-custom-fields-theme-options'); ?>'
                            },
                            secret_key: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('Secret key', 'wp-custom-fields-theme-options'); ?>'
                            },
                            description: {
                                type: 'textarea',
                                placeholder: '<?php esc_html_e('Payment method description', 'wp-custom-fields-theme-options'); ?>'
                            },
                            currency: {
                                type: 'text',
                                placeholder: '<?php esc_html_e('Stripe currency code', 'wp-custom-fields-theme-options'); ?>'
                            },
                        },
                    },
                },
                sources: {
                    codes: {
                        '<?php esc_html_e('Select Currency code', 'wp-custom-fields-theme-options'); ?>' : '',
                        '<?php esc_html_e('Australian dollar', 'wp-custom-fields-theme-options'); ?>' : 'AUD',
                        '<?php esc_html_e('Brazilian real', 'wp-custom-fields-theme-options'); ?>' : 'BRL',
                        '<?php esc_html_e('Canadian dollar', 'wp-custom-fields-theme-options'); ?>' : 'CAD',
                        '<?php esc_html_e('Czech koruna', 'wp-custom-fields-theme-options'); ?>' : 'CZK',
                        '<?php esc_html_e('Danish krone', 'wp-custom-fields-theme-options'); ?>' : 'DKK',
                        '<?php esc_html_e('Euro', 'wp-custom-fields-theme-options'); ?>' : 'EUR',
                        '<?php esc_html_e('Hong Kong dollar', 'wp-custom-fields-theme-options'); ?>' : 'HKD',
                        '<?php esc_html_e('Hungarian forint 1', 'wp-custom-fields-theme-options'); ?>' : 'HUF',
                        '<?php esc_html_e('Indian rupee', 'wp-custom-fields-theme-options'); ?>' : 'INR',
                        '<?php esc_html_e('Israeli new shekel', 'wp-custom-fields-theme-options'); ?>' : 'ILS',
                        '<?php esc_html_e('Japanese yen 1', 'wp-custom-fields-theme-options'); ?>' : 'JPY',
                        '<?php esc_html_e('Malaysian ringgit 2	', 'wp-custom-fields-theme-options'); ?>' : 'MYR',
                        '<?php esc_html_e('Mexican peso', 'wp-custom-fields-theme-options'); ?>' : 'MXN',
                        '<?php esc_html_e('New Taiwan dollar 1', 'wp-custom-fields-theme-options'); ?>' : 'TWD',
                        '<?php esc_html_e('New Zealand dollar', 'wp-custom-fields-theme-options'); ?>' : 'NZD',
                        '<?php esc_html_e('Norwegian krone', 'wp-custom-fields-theme-options'); ?>' : 'NOK',
                        '<?php esc_html_e('Philippine peso', 'wp-custom-fields-theme-options'); ?>' : 'PHP',
                        '<?php esc_html_e('Polish złoty', 'wp-custom-fields-theme-options'); ?>' : 'PLN',
                        '<?php esc_html_e('Pound sterling', 'wp-custom-fields-theme-options'); ?>' : 'GBP',
                        '<?php esc_html_e('Russian ruble', 'wp-custom-fields-theme-options'); ?>' : 'RUB',
                        '<?php esc_html_e('Singapore dollar', 'wp-custom-fields-theme-options'); ?>' : 'SGD',
                        '<?php esc_html_e('Swedish krona', 'wp-custom-fields-theme-options'); ?>' : 'SEK',
                        '<?php esc_html_e('Swiss franc', 'wp-custom-fields-theme-options'); ?>' : 'CHF',
                        '<?php esc_html_e('Thai baht', 'wp-custom-fields-theme-options'); ?>' : 'THB',
                        '<?php esc_html_e('United States dollar', 'wp-custom-fields-theme-options'); ?>' : 'USD',
                    },
                    modes : {
                        '<?php esc_html_e('Sandbox', 'wp-custom-fields-theme-options'); ?>' : 'sandbox',
                        '<?php esc_html_e('Live', 'wp-custom-fields-theme-options'); ?>' : 'live',
                    }
                }
            }
        },
        template: '<?php echo stm_wpcfto_filtered_output($template); ?>',
        mounted: function () {
            if (this.saved_payments) this.setPaymentValues();
        },
        methods: {
            setPaymentValues() {
                var vm = this;
                for(var payment_method in vm.payments) {
                    if (!vm.payments.hasOwnProperty(payment_method) && !vm.saved_payments.hasOwnProperty(payment_method)) continue;
                    vm.payments[payment_method]['enabled'] = vm.saved_payments[payment_method]['enabled'];

                    for(var field_name in vm.payments[payment_method]['fields']) {
                        vm.$set(vm.payments[payment_method]['fields'][field_name], 'value', vm.saved_payments[payment_method]['fields'][field_name]);
                    }
                }
            },
            getPaymentValues() {
                var vm = this;
                for(var payment_method in vm.payments) {

                    if (!vm.payments.hasOwnProperty(payment_method)) continue;
                    vm.payment_values[payment_method] = {
                        'enabled' : vm.payments[payment_method]['enabled'],
                    };

                    if(typeof vm.payment_values[payment_method]['fields'] === 'undefined') vm.payment_values[payment_method]['fields'] = {};

                    for(var field_name in vm.payments[payment_method]['fields']) {
                        if (! vm.payments[payment_method]['fields'].hasOwnProperty(field_name)) continue;
                        var value = (typeof vm.payments[payment_method]['fields'][field_name]['value'] === 'undefined') ? '' : vm.payments[payment_method]['fields'][field_name]['value'];

                        vm.payment_values[payment_method]['fields'][field_name] = value;

                    }
                }

                this.$emit('update-payments', vm.payment_values);
            }
        },
        watch: {
            payments: {
                handler: function () {
                    this.getPaymentValues();
                },
                deep: true
            },
        }
    })
</script>