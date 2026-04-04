(function ($) {
    'use strict';

    let subscriptionPaymentsTable = null;
    let subsIdToCancel = null;
    const prefix = subscription_details_page_data.is_instructor ? 'subscription/' : 'my-subscription/';
    const route = prefix + subscription_details_page_data.subscription_id + '/payment-history';

    $(document).ready(function() {
        initializeSubscriptionNote();
        updateTable(route);

        $(document).on('click', '.masterstudy-subscriptions-details-page__cancel', function () {
            const $btn = $(this);
            subsIdToCancel = $btn.data('id');
            if (!subsIdToCancel) return;

            $('.masterstudy-alert').addClass('masterstudy-alert_open');
        });

        $(document).on('click', '.masterstudy-subscriptions-details-page__resubscribe', function () {
            const $btn = $(this);
            const subsId = $btn.data('id');
            if (!subsId) return;

            let current_data = {
                action: 'stm_lms_add_to_cart_subscription',
                plan_id: subsId,
                nonce: stm_lms_nonces['stm_lms_add_to_cart_subscription']
            };

            $.ajax({
                url: stm_lms_ajaxurl,
                type: 'POST',
                data: current_data,
                beforeSend: function () {
                    $btn.addClass('masterstudy-subscriptions-details-page__resubscribe_loading');
                },
                success(res) {
                    if (res.success) {
                        if (res.data['cart_url'].length > 0) {
                            $btn.removeClass('masterstudy-subscriptions-details-page__resubscribe-button_loading');
                            window.location.href = res.data['cart_url'];
                        }
                    }
                }
            });
        });

        $('.masterstudy-alert').on('click', function(event) {
            if (event.target === this) {
                $(this).removeClass('masterstudy-alert_open');
            }
        });

        $(document).on('click', '.masterstudy-alert__actions [data-id="cancel"]', function(e) {
                e.preventDefault();
                $('.masterstudy-alert').removeClass('masterstudy-alert_open');
            });

        $(document).on('click', '.masterstudy-alert__header-close', function(e) {
                e.preventDefault();
                $('.masterstudy-alert').removeClass('masterstudy-alert_open');
            });

        $(document).on('click', '.masterstudy-alert__actions [data-id="submit"]', function(e) {
            e.preventDefault();
            const _this = $(this);
            const routeUrl = subscription_details_page_data.is_instructor ? `${subscription_details_page_data.rest_url}subscription/${subsIdToCancel}/cancel` : `${subscription_details_page_data.rest_url}my-subscription/${subsIdToCancel}/cancel`;
            $.ajax({
                url: routeUrl,
                method: 'PUT',
                headers: { 'X-WP-Nonce': subscription_details_page_data.nonce },
                data: { subscription_id: subsIdToCancel },
                beforeSend: function() {
                    _this.addClass('masterstudy-button_loading');
                },
                success: function(data) {
                    if (data && data.status === 'ok') {
                        _this.removeClass('masterstudy-button_loading');
                        $('.masterstudy-subscriptions-details-page__cancel').hide();
                        $('.masterstudy-alert').removeClass('masterstudy-alert_open');
                        $('.masterstudy-subscriptions-details-page__status').removeClass().addClass('masterstudy-subscriptions-details-page__status masterstudy-subscriptions-details-page__status_cancelled').text(subscription_details_page_data.statuses.cancelled);
                    }
                },
                error: function (xhr) {
                    _this.removeClass('masterstudy-button_loading');

                    let msg = 'An unknown error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                    try {
                        const json = JSON.parse(xhr.responseText);
                        if (json && json.message) msg = json.message;
                    } catch (e) { /* ignore */ }
                    }

                    _this.closest('.masterstudy-alert__container')
                    .find('.masterstudy-alert__error')
                    .text(msg)
                    .show();
                }
            })
        });
    });

    function updateTable(currentRoute, reloadTable = false) {
        const dataSrc = function (json) {
        const pageInfo = $('#masterstudy-datatable-subscription-payments').DataTable().page.info()
        const start = pageInfo.start;

        json.data = json.data.map((item, index) => {
            item.number = start + index + 1
            item.coupon = item.coupon?.formatted_value ?? ''
            return item
        });

        return json.data;
        }

        const columnDefs = subscription_details_page_data['subscription-payments-columns'].map((col, index) => {
            const def = {
                targets: index,
                orderable: false,
                data: col.data
            };

            if ( index === 0 ) {
                def.width = '30px';
            }

            if (index === subscription_details_page_data['subscription-payments-columns'].length - 1) {
                def.orderable = true;
                def.width = '30px';
                def.render = function (_data, _type, row) {
                    return renderStatus(row.status);
                };
            }

            return def;
        });

        subscriptionPaymentsTable = updateDataTable(
            subscriptionPaymentsTable,
            '#masterstudy-datatable-subscription-payments',
            [`[data-chart-id="subscription-payments-table"]`],
            currentRoute,
            subscription_details_page_data['subscription-payments-columns'],
            dataSrc,
            columnDefs,
            reloadTable,
        );
    }

    function renderStatus(status) {
        const statusMap = {
            'trialing': { text: subscription_details_page_data.statuses.trialing, class: 'masterstudy-subscriptions-details-page-table-status_trialing' },
            'active': { text: subscription_details_page_data.statuses.active, class: 'masterstudy-subscriptions-details-page-table-status_active' },
            'completed': { text: subscription_details_page_data.statuses.completed, class: 'masterstudy-subscriptions-details-page-table-status_completed' },
            'cancelled': { text: subscription_details_page_data.statuses.cancelled, class: 'masterstudy-subscriptions-details-page-table-status_cancelled' },
            'pending': { text: subscription_details_page_data.statuses.pending, class: 'masterstudy-subscriptions-details-page-table-status_pending' },
            'expired': { text: subscription_details_page_data.statuses.expired, class: 'masterstudy-subscriptions-details-page-table-status_expired' },
            'refunded': { text: subscription_details_page_data.statuses.refunded, class: 'masterstudy-subscriptions-details-page-table-status_refunded' },
            'paid': { text: subscription_details_page_data.statuses.paid, class: 'masterstudy-subscriptions-details-page-table-status_paid' },
        };
        const { text, class: statusClass } = statusMap[status] || { text: status, class: '' };
        return `<span class="masterstudy-subscriptions-details-page-table-status ${statusClass}">${text}</span>`;
    }

    function initializeSubscriptionNote() {
        $(document).on('click', '#subscription-note-edit-btn', function() {
            const $container = $(this).closest('.masterstudy-orders-table__details');
            const $display = $container.find('#masterstudy-subscription-note-text');
            const $update = $container.find('#subscription-note-update-btn');
            const $cancel = $container.find('#subscription-note-cancel-btn');
            const $textarea = $container.find('#subscription-note-textarea');

            $display.hide();
            $(this).hide();
            $update.show();
            $cancel.show();
            $textarea.show().focus();
        });

        $(document).on('click', '#subscription-note-cancel-btn', function() {
            const $container = $(this).closest('.masterstudy-orders-table__details');
            const $display = $container.find('#masterstudy-subscription-note-text');
            const $update = $container.find('#subscription-note-update-btn');
            const $edit = $container.find('#subscription-note-edit-btn');
            const $textarea = $container.find('#subscription-note-textarea');
            const originalText = $textarea.attr('data-original-text') || '';

            $(this).hide();
            $textarea.val(originalText).hide();
            $update.hide();
            $edit.show();
            $display.show();
        });

        $(document).on('click', '#subscription-note-update-btn', function() {
            const $btn = $(this);
            const $container = $btn.closest('.masterstudy-orders-table__details');
            const $display = $container.find('#masterstudy-subscription-note-text');
            const $cancel = $container.find('#subscription-note-cancel-btn');
            const $edit = $container.find('#subscription-note-edit-btn');
            const $textarea = $container.find('#subscription-note-textarea');
            const subscriptionId = $container.find('#subscription-note-update-btn').data('subscription-id');
            const noteText = $textarea.val().trim();

            $btn.prop('disabled', true);

            if (!$textarea.attr('data-original-text')) {
                $textarea.attr('data-original-text', $textarea.val());
            }

            updateSubscriptionNote(subscriptionId, noteText)
                .then(function(response) {
                    if (response.success) {
                        $textarea.hide();
                        $cancel.hide();
                        $edit.show();
                        $btn.hide();
                        $display.text(noteText).show();
                        showNotification('Note updated successfully!', 'success');
                    } else {
                        throw new Error(response.message || 'Failed to update note');
                    }
                })
                .catch(function(error) {
                    console.error('Error updating subscription note:', error);
                    showNotification('Failed to update note. Please try again.', 'error');
                })
                .finally(function() {
                    $btn.prop('disabled', false);
                });
        });
    }

    function updateSubscriptionNote(subscriptionId, noteText) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: window.location.origin + '/wp-json/masterstudy-lms/v2/subscription/' + subscriptionId,
                method: 'POST',
                data: {
                    note: noteText
                },
                headers: {
                    'X-WP-Nonce': subscription_details_page_data.nonce
                },
                success: function(response) {
                    resolve({
                        success: true,
                        data: response
                    });
                },
                error: function(xhr, status, error) {
                    let message = 'Failed to update note';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const errorData = JSON.parse(xhr.responseText);
                            message = errorData.message || message;
                        } catch (e) {
                        }
                    }
                    
                    reject({
                        success: false,
                        message: message
                    });
                }
            });
        });
    }

    function showNotification(message, type) {
        const $notification = $('<div class="subscription-note-notification ' + type + '">' + message + '</div>');
        
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
})(jQuery);
