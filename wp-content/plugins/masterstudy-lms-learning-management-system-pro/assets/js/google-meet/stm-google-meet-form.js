(function ($) {
	$(document).ready(function () {
		var isEdit = false;
		var editMeetID = 0;
		var deleteMeetID = 0;
		var meetingsTable = null;
		var meetingColumns = stm_gm_front_ajax_variable && stm_gm_front_ajax_variable.meeting_table_columns
			? stm_gm_front_ajax_variable.meeting_table_columns
			: [];
		var meetingsHeader = $('.masterstudy-account-google-meets-meetings__header');
		var meetingsContent = $('.masterstudy-account-google-meets-meetings__content');
		var noRecordsEl = $('.masterstudy-account-google-meets-meetings__no-records');
		var settingsDrawer = $('.masterstudy-account-google-meets-meetings__settings-drawer');
		var settingsTimezone = null;
		var settingsSendUpdates = null;
		var actionsTemplate = document.getElementById('masterstudy-account-google-meets-meetings__actions-template');
		var actionsMenuVisibleClass = 'masterstudy-account-google-meets-meetings__actions-menu_visible';
		var actionsMenuListSelector = '.masterstudy-account-google-meets-meetings__actions-menu-list';
		var detachedActionsMenuClass = 'masterstudy-account-google-meets-meetings__actions-menu-list_detached';
		var detachedActionsMenuState = null;
		var settingsRootSelector = '.masterstudy-account-google-meets-meetings__settings';
		$(".meet-delete-btn-cl").click(function (e) {
			e.preventDefault();

			if (!deleteMeetID) {
				return;
			}

			var deleteBtn = $(this);
			var deleteBtnText = deleteBtn.html();
			var formData = new FormData();
			formData.append('action', 'gm_delete_meet_ajax');
			formData.append('post_id', deleteMeetID);
			formData.append('nonce', stm_gm_front_ajax_variable.nonce);

			deleteBtn.addClass('disabled').attr('disabled', 'disabled').html('Deleting...');

			$.ajax({
				url: stm_gm_front_ajax_variable.url,
				type: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success(response) {
					if (response.success) {
						$('#delete-meeting-mw-id').toggleClass('show');
						deleteMeetID = 0;
						updateMeetingsTable(true);
						return;
					}

					var errorMessage = response.data && response.data.error
						? response.data.error
						: 'Unable to delete meeting. Please try again.';
					alert(errorMessage);
				},
				error(xhr, ajaxOptions, thrownError) {
					alert('Unable to delete meeting. Please try again.');
					console.log(xhr)
				},
				complete() {
					deleteBtn.removeClass('disabled').attr('disabled', false).html(deleteBtnText);
				}
			});
		})

		function updateMeetingsTable(reloadTable = false) {
			if (!meetingColumns.length || typeof updateDataTable !== 'function') {
				return;
			}

			const dataSrc = function (json) {
				const items = Array.isArray(json.data) ? json.data : [];
				renderNoMeetings(items.length === 0);
				json.data = items.map((item) => item);

				return json.data;
			};

			const columnDefs = buildMeetingColumnDefs(meetingColumns);

			meetingsTable = updateDataTable(
				meetingsTable,
				'#masterstudy-datatable-google-meets',
				['.masterstudy-account-google-meets-meetings__content'],
				'google-meets/list',
				meetingColumns,
				dataSrc,
				columnDefs,
				reloadTable,
			);
		}

		function renderNoMeetings(val) {
			if (val) {
				meetingsHeader.toggleClass('masterstudy-account-utility_hidden', true);
				meetingsContent.toggleClass('masterstudy-account-utility_hidden', true);
				noRecordsEl.toggleClass('masterstudy-account-utility_hidden', false);
			} else {
				meetingsHeader.toggleClass('masterstudy-account-utility_hidden', false);
				meetingsContent.toggleClass('masterstudy-account-utility_hidden', false);
				noRecordsEl.toggleClass('masterstudy-account-utility_hidden', true);
			}
		}

		function buildMeetingColumnDefs(columns) {
			return columns.map((col, index) => {
				const def = {
					targets: index,
					data: col.data,
					orderable: false,
				};

				switch (col.data) {
					case 'title':
						def.className = 'masterstudy-account-google-meets-meetings__title-cell';
						def.render = function (_data, _type, row) {
							const title = row.title || '';
							const meetingUrl = row.meeting_url || '';
							const linkHtml = meetingUrl
								? `<a href="${meetingUrl}" target="_blank" class="masterstudy-account-google-meets-meetings__title-link">${meetingUrl}</a>`
								: '';
							const copyHtml = meetingUrl
								? `<a href="#" class="masterstudy-account-google-meets-meetings__copy-link" data-copy-link="${meetingUrl}" aria-label="Copy meeting link">
										<span class="stmlms-copy"></span>
									</a>`
								: '';

							return `<div class="masterstudy-account-google-meets-meetings__title-col">
								<span class="masterstudy-account-google-meets-meetings__title-text">${title}</span>
								<div class="masterstudy-account-google-meets-meetings__link-row">
									${linkHtml}
									${copyHtml}
								</div>
							</div>`;
						};
						break;
					case 'date_time':
						def.render = function (_data, _type, row) {
							const start = row.date_time ? `${row.date_time} —` : '';
							const end = row.date_time_end || '';
							if (!start && !end) {
								return '';
							}

							return `<div class="masterstudy-account-google-meets-meetings__date-time-col">${start}<br/>${end}</div>`;
						};
						break;
					case 'status':
						def.render = function (_data, _type, row) {
							const i18n = stm_gm_front_ajax_variable && stm_gm_front_ajax_variable.i18n
								? stm_gm_front_ajax_variable.i18n
								: {};
							const isStarted = !!row.is_meet_started;
							const statusLabel = isStarted
								? (i18n.expired || 'Expired')
								: (i18n.pending || 'Pending');
							const statusClass = isStarted
								? 'masterstudy-account-google-meets-meetings__status-expired'
								: 'masterstudy-account-google-meets-meetings__status-pending';

							return `<span class="masterstudy-account-google-meets-meetings__status ${statusClass}">${statusLabel}</span>`;
						};
						break;
					case 'actions':
						def.className = 'masterstudy-account-google-meets-meetings__actions-wrapper';
						def.render = function (_data, _type, row) {
							return buildActionsCell(row);
						};
						break;
				}

				return def;
			});
		}

		$(document).on('click', '.masterstudy-account-google-meets-meetings__actions-menu-item', function() {
			var action = $(this).attr('data-action');

			closeDetachedActionsMenu();

			if (action === 'delete') {
				handleDeleteMeeting($(this));
			}

			if (action === 'edit') {
				handleEditMeeting($(this));
			}
		});

		$(document).on('click', '.masterstudy-account-google-meets-meetings__copy-link', function (e) {
			e.preventDefault();
			var link = $(this).attr('data-copy-link') || '';
			if (!link) {
				return;
			}

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(link);
			}
		});

		$(".masterstudy-account-google-meets-meetings__create-btn").click(function (e) {
			e.preventDefault();
			var fields = [
				{ selector: '#front-meeting-name' },
				{ selector: '#front-meeting-summary' },
				{ selector: '#front-meeting-start-date' },
				{ selector: '#front-meeting-end-date' },
			];
			fields.forEach(function(field) {
				$(field.selector).val('');
			});
			$('#create-meeting-mw-id').toggleClass('show');
			
			$("p.gm-modal-title").html ('Add a new meeting');
		});
		$(".create-meeting-mw .gm-modal-close, .create-meeting-mw .button-cancel").click(function (e) {
			e.preventDefault();
			$('#create-meeting-mw-id').toggleClass('show');
		});
		$(".delete-meeting-mw .gm-modal-close, .delete-meeting-mw .button-cancel ").click(function (e) {
			e.preventDefault();
			$('#delete-meeting-mw-id').toggleClass('show');
		});

		$(document).on('click', '.masterstudy-account-google-meets-meetings__settings-btn', function (e) {
			e.preventDefault();
			settingsDrawer.toggleClass('masterstudy-drawer-component_open', true);
		});

		$(document).on('click', '.masterstudy-account-google-meets-meetings__settings-close', function () {
			settingsDrawer.toggleClass('masterstudy-drawer-component_open', false);
		});

		$(document).on( 'input', '.masterstudy-account-google-meets-meetings__settings input', function() {
			markSettingsDirty($(this).closest(settingsRootSelector));
		});

		document.addEventListener('msfieldEvent', function(e) {
			if (!e.detail?.name) {
				return;
			}

			if (
				'front-meeting-timezone-settings' === e.detail.name ||
				'front-send-updates-settings' === e.detail.name
			) {
				if ( 'front-meeting-timezone-settings' === e.detail.name ) {
					settingsTimezone = e.detail.value;
				}

				if ( 'front-send-updates-settings' === e.detail.name ) {
					settingsSendUpdates = e.detail.value;
				}

				markSettingsDirty($(settingsRootSelector));
			}
		});

		$(document).on('click', '.masterstudy-account-google-meets-meetings__settings-save', function (e) {
			e.preventDefault();
			$(this).addClass('disabled').attr('disabled', 'disabled');
			var settingsRoot = $(this).closest(settingsRootSelector);
			var settingsNotice = settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-notice');
			var settingsNoticeText = settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-notice-text');
			var settingsNoticeLoading = settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-notice-loading');
			var settingsNoticeSuccess = settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-notice-success');

			settingsNotice.removeClass('masterstudy-account-utility_hidden');
			settingsNoticeLoading.removeClass('masterstudy-account-utility_hidden');
			settingsNoticeSuccess.addClass('masterstudy-account-utility_hidden');
			settingsNoticeText.html('Saving...');
			var formData = new FormData();
			formData.append('action', 'gm_save_settings_ajax');
			formData.append('nonce', stm_gm_front_ajax_variable.nonce);
			formData.append('timezone', settingsTimezone ?? settingsRoot.data('defaultTimezone'));
			formData.append('reminder', settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-reminder').val());
			formData.append('send_updates', settingsSendUpdates ?? settingsRoot.data('defaultUpdates'));

			$.ajax({
				url: stm_gm_front_ajax_variable.url,
				type: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success(response) {
					console.log(response);
					settingsNoticeText.html('Settings saved successfully');
					settingsNoticeLoading.addClass('masterstudy-account-utility_hidden');
					settingsNoticeSuccess.removeClass('masterstudy-account-utility_hidden');
					setTimeout(function() {
						settingsNotice.addClass('masterstudy-account-utility_hidden');
					}, 3000);
				},
				error(xhr, ajaxOptions, thrownError) {
					console.log(xhr)
				}
			});
		});
		$(document).on('click', '.masterstudy-account-google-meets-meetings__settings-reset', function (e) {
			e.preventDefault();
			var formData = new FormData();
			formData.append('action', 'gm_front_reset_settings_ajax');
			formData.append('nonce', stm_gm_front_ajax_variable.nonce);
			
			if (confirm('Are you sure you want to delete this permanently from the site? Please confirm your choice?')) {
				
				$.ajax({
					url: stm_gm_front_ajax_variable.url,
					type: 'post',
					data: formData,
					dataType: 'json',
					processData: false,
					contentType: false,
					success(response) {
						location.reload();
					},
					error(xhr, ajaxOptions, thrownError) {
						console.log(xhr)
					}
				});
			}
		});
		$(document).on('click', '.masterstudy-account-google-meets-meetings__settings-change-account', function (e) {
			e.preventDefault();
			var formData = new FormData();
			formData.append('action', 'gm_front_reset_settings_ajax');
			formData.append('nonce', stm_gm_front_ajax_variable.nonce);
			formData.append('changeAccount', true);

			$.ajax({
				url: stm_gm_front_ajax_variable.url,
				type: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success(response) {
					window.location.href = response.url;
				},
				error(xhr, ajaxOptions, thrownError) {
					console.log(xhr)
				}
			});
		});
		$('.lms-gm-validation-input').on('input', function() {
			var input = $(this);
			var errorMsg = input.next('.gm-validation-error-message');
			if (input.val().trim() !== '') {
				input.css('border-color', '#DBE0E9');
				input.removeClass('gm-validation-error');
				errorMsg.remove();
			} else {
				input.css('border-color', 'red');
				if (errorMsg.length === 0) {
					errorMsg = $('<p class="gm-validation-error-message">This field is required.</p>');
					input.after(errorMsg);
					var elementToDisplay = input.next('.gm-validation-error-message');
					elementToDisplay.css('display', 'block');
				}
			}
		});

		function setValidationError(input, message) {
			var errorMsg = input.next('.gm-validation-error-message');
			input.addClass('gm-validation-error');
			input.css('border-color', 'red');
			if (errorMsg.length === 0) {
				errorMsg = $('<p class="gm-validation-error-message"></p>');
				input.after(errorMsg);
			}
			errorMsg.text(message).css('display', 'block');
		}

		function clearValidationError(input) {
			input.removeClass('gm-validation-error');
			input.css('border-color', '#DBE0E9');
			input.next('.gm-validation-error-message').remove();
		}

		function validateDateRange() {
			var startInput = $('#front-meeting-start-date');
			var endInput = $('#front-meeting-end-date');
			var startValue = startInput.val();
			var endValue = endInput.val();

			if (!startValue || !endValue) {
				return true;
			}

			var startDate = new Date(startValue);
			var endDate = new Date(endValue);

			if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) {
				return true;
			}

			if (endDate.getTime() <= startDate.getTime()) {
				setValidationError(endInput, 'End date/time must be later than start date/time.');
				return false;
			}

			if (
				endInput.next('.gm-validation-error-message').text() === 'End date/time must be later than start date/time.'
			) {
				clearValidationError(endInput);
			}

			return true;
		}

		$('#front-meeting-start-date, #front-meeting-end-date').on('input change', function() {
			validateDateRange();
		});

		function markSettingsDirty(settingsRoot) {
			if (!settingsRoot || !settingsRoot.length) {
				return;
			}

			settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-save')
				.removeClass('disabled')
				.attr('disabled', false);
			settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-notice')
				.addClass('masterstudy-account-utility_hidden');
			settingsRoot.find('.masterstudy-account-google-meets-meetings__settings-notice-success')
				.addClass('masterstudy-account-utility_hidden');
		}
		$(".create-meeting-mw-save").on('click', function (e) {
			e.preventDefault();
			var fields = [
				{ selector: '#front-meeting-name', message: 'Please enter a meeting name' },
				{ selector: '#front-meeting-summary', message: 'Please enter a meeting summary' },
				{ selector: '#front-meeting-start-date', message: 'Please enter a start date' },
				{ selector: '#front-meeting-end-date', message: 'Please enter an end date' },
				{ selector: '#front-meeting-timezone', message: 'Please select a timezone' },
			];

			var isError = false;
			var meetingBtnText = $('.create-meeting-mw-save').html();

			for (var i = 0; i < fields.length; i++) {
				var field = $(fields[i].selector);
				if (field.val() === '') {
					field.addClass('gm-validation-error');
					isError = true;
				}
			}
			if(isError) {
				$('.gm-validation-error-message').css('display', 'block');
				return false;
			}

			if (!validateDateRange()) {
				return false;
			}
			
			$(this).addClass('disabled').attr('disabled', 'disabled');
			$('.create-meeting-mw-save').html('Saving...');
			$(".create-meeting-mw-save").css('cursor', 'default')
			$(".create-meeting-mw-save").css('box-shadow', 'none')
			var formData = new FormData();
			if (isEdit) {
				formData.append('is_edit', isEdit);
				formData.append('google_meet_id', editMeetID);
				formData.append('original_post_status', 'publish');
			}

			formData.append('action', 'gm_create_new_event_front');
			formData.append('isInstructorMeet', true);
			formData.append('nonce', stm_gm_front_ajax_variable.nonce);
			formData.append('name', $('#front-meeting-name').val());
			formData.append('stm_gma_summary', $('#front-meeting-summary').val());
			formData.append('front_start_date_time', $('#front-meeting-start-date').val());
			formData.append('front_end_date_time', $('#front-meeting-end-date').val());
			formData.append('stm_gma_timezone', $('#front-meeting-timezone').val());
			$.ajax({
				url: stm_gm_front_ajax_variable.url,
				type: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success(response) {
					if ( response.success ) {
						if ( response.is_reload ) {
							location.reload();
						} else {
							$("#meetings table").removeClass('hidden');
							$("#meetings .not-found-meetings").addClass('hidden');
							$("#meetings tbody").prepend(response.table_data);
							updateMeetingsTable(true);
							$('.create-meeting-mw-save').css( 'background', 'green' );
							$('.create-meeting-mw-save').html(response.success);

							setTimeout(function() {
								$('#create-meeting-mw-id').toggleClass('show');
								$('.create-meeting-mw-save').html(meetingBtnText);
								$('.create-meeting-mw-save').attr('style', false);
								$('.create-meeting-mw-save').removeClass('disabled').attr('disabled', false);
							}, 3000);
						}						
					} else {
						var errorMessage = response.data && response.data.error ? response.data.error : 'Unable to save meeting. Please check dates and try again.';
						setValidationError($('#front-meeting-end-date'), errorMessage);
						$('.create-meeting-mw-save').removeClass('disabled').attr('disabled', false);
						$('.create-meeting-mw-save').html(meetingBtnText);
						$('.create-meeting-mw-save').attr('style', false);
					}
				},
				error(xhr, ajaxOptions, thrownError) {
					console.log(xhr)
				}
			});
			isEdit = false;
		});
		$("#meetingsList").click(function (e) {
			if (window.location.search.indexOf('?paged=') !== -1) {
				window.location.href = ($('.float_menu_item_active').attr('href') + $(this).attr('href'));
			}
			})
		$(document).on('click', '.masterstudy-account-google-meets-meetings__actions-menu-trigger', function(e) {
			e.preventDefault();
			var trigger = $(this);
			var menu = trigger.closest('.masterstudy-account-google-meets-meetings__actions-menu')
				.find('.masterstudy-account-google-meets-meetings__actions-menu-list');
			if (!menu.length) {
				return;
			}

			if (detachedActionsMenuState && detachedActionsMenuState.trigger.is(trigger)) {
				closeDetachedActionsMenu();
				return;
			}

			openDetachedActionsMenu(trigger, menu.first());
		});

		$(document).on('click', function(e) {
			if (
				$(e.target).closest('.masterstudy-account-google-meets-meetings__actions-menu').length ||
				$(e.target).closest('.masterstudy-account-google-meets-meetings__actions-menu-trigger').length ||
				$(e.target).closest(actionsMenuListSelector).length
			) {
				return;
			}

			closeDetachedActionsMenu();
		});

		$(window).on('resize scroll', function() {
			if (detachedActionsMenuState) {
				closeDetachedActionsMenu();
			}
		});

		function openDetachedActionsMenu(trigger, menu) {
			closeDetachedActionsMenu();

			const menuRoot = trigger.closest('.masterstudy-account-google-meets-meetings__actions-menu');
			if (!menuRoot.length) {
				return;
			}

			$('body').append(menu);
			menu.addClass(actionsMenuVisibleClass + ' ' + detachedActionsMenuClass);
			positionDetachedActionsMenu(trigger, menu);

			detachedActionsMenuState = {
				trigger: trigger,
				menuRoot: menuRoot,
				menu: menu,
			};
		}

		function positionDetachedActionsMenu(trigger, menu) {
			const triggerEl = trigger.get(0);
			if (!triggerEl) {
				return;
			}

			const rect = triggerEl.getBoundingClientRect();
			const scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
			const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || 0;
			const menuWidth = menu.outerWidth() || 146;
			const top = rect.bottom + scrollTop + 6;
			const left = rect.right + scrollLeft - menuWidth;

			menu.css({
				position: 'absolute',
				top: `${top}px`,
				left: `${left}px`,
			});
		}

		function closeDetachedActionsMenu() {
			if (!detachedActionsMenuState) {
				return;
			}

			const menu = detachedActionsMenuState.menu;
			const menuRoot = detachedActionsMenuState.menuRoot;

			menu.removeClass(actionsMenuVisibleClass + ' ' + detachedActionsMenuClass);
			menu.removeAttr('style');
			menuRoot.append(menu);

			detachedActionsMenuState = null;
		}

		function setDateAndTime(timestamp, time) {
			var date = new Date(timestamp * 1000);

			var dateString = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
			var timeString = String(time); // Convert to string to ensure it can be split
			var hours = timeString.split(':')[0];
			var minutes = timeString.split(':')[1];
			var timeValue = dateString + 'T' + hours + ':' + minutes;
			return timeValue;
		}
		function setDate(timestamp) {
			const date = new Date(timestamp * 1000);

			const year = date.getFullYear();
			const month = String(date.getMonth() + 1).padStart(2, '0');
			const day = String(date.getDate()).padStart(2, '0');

			return `${year}-${month}-${day}`;
		}

		function handleDeleteMeeting(target) {
			deleteMeetID = target.attr('data-meet-id');
			$('#delete-meeting-mw-id').toggleClass('show');
			$("p.gm-modal-title").html ('Confirm delete');
			var formData = new FormData();
			formData.append('action', 'gm_get_meet_by_id_ajax');
			formData.append('post_id', target.attr('data-meet-id'));
			formData.append('nonce', stm_gm_front_ajax_variable.nonce);

			$.ajax({
				url: stm_gm_front_ajax_variable.url,
				type: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false,
				success(response) {
					var data = [
						{label: 'Name:', value: response.meet_title},
						{label: 'Summary:', value: response.meetData.stm_gma_summary},
						{
							label: 'Starts:',
							value: (setDate(response.meetData.stm_gma_start_date / 1000)) + ' ' + (response.meetData.stm_gma_start_time)
						},
						{
							label: 'Ends:',
							value: (setDate(response.meetData.stm_gma_end_date / 1000)) + ' ' + (response.meetData.stm_gma_end_time)
						},
						{label: 'Timezone:', value: response.meetData.stm_gma_timezone},
						{label: 'Host email:', value: response.meet_host},
						// Add more objects as needed for other properties
					];
					
					var html = '';
					for (var i = 0; i < data.length; i++) {
						html += '<div class="meet-delete-row">';
						html += '<span class="names">' + data[i].label + '</span> <span class="values">' + data[i].value + '</span>';
						html += '</div>';
					}
					$('#delete-meeting-mw-id .meet-delete-data p').html(html);
				},
				error(xhr, ajaxOptions, thrownError) {
					console.log(xhr)
				}
			});
		}

		function handleEditMeeting(target) {
			$("p.gm-modal-title").html ('Edit Meeting');
			if (target.attr('data-meet-id') !== null) {
				isEdit = true;
				editMeetID = target.attr('data-meet-id');
				var formData = new FormData();
				formData.append('action', 'gm_get_meet_by_id_ajax');
				formData.append('post_id', editMeetID);
				formData.append('nonce', stm_gm_front_ajax_variable.nonce);

				$.ajax({
					url: stm_gm_front_ajax_variable.url,
					type: 'post',
					data: formData,
					dataType: 'json',
					processData: false,
					contentType: false,
					success(response) {
						$('#front-meeting-name').val(response.meet_title);
						$('#front-meeting-summary').val(response.meetData.stm_gma_summary);
						$('#front-meeting-start-date').val(setDateAndTime(response.meetData.stm_gma_start_date / 1000, response.meetData.stm_gma_start_time));
						$('#front-meeting-end-date').val(setDateAndTime(response.meetData.stm_gma_end_date / 1000, response.meetData.stm_gma_end_time));
						$('#front-meeting-timezone').val(response.meetData.stm_gma_timezone);
						$('#create-meeting-mw-id').toggleClass('show');
					},
					error(xhr, ajaxOptions, thrownError) {
						console.log(xhr)
					}
				});
			}
		}

		function buildActionsCell(row) {
			if (!actionsTemplate) {
				return '';
			}

			const fragment = actionsTemplate.content.cloneNode(true);
			const wrapper = fragment.querySelector('.masterstudy-account-google-meets-meetings__actions');
			const startBtn = fragment.querySelector('.masterstudy-account-google-meets-meetings__action-start');
			const menu = fragment.querySelector('.masterstudy-account-google-meets-meetings__actions-menu-list');
			const editBtn = fragment.querySelector('.masterstudy-account-google-meets-meetings__actions-menu-item[data-action="edit"]');
			const deleteBtn = fragment.querySelector('.masterstudy-account-google-meets-meetings__actions-menu-item[data-action="delete"]');

			if (row.is_meet_started) {
				if (startBtn) {
					startBtn.remove();
				}
				if (editBtn) {
					editBtn.remove();
				}
			} else {
				if (startBtn) {
					startBtn.setAttribute('href', row.meeting_url || '#');
					startBtn.setAttribute('target', '_blank');
				}
			}

			if (editBtn) {
				editBtn.setAttribute('data-meet-id', row.meeting_id);
			}
			if (deleteBtn) {
				deleteBtn.setAttribute('data-meet-id', row.meeting_id);
			}
			if (menu && !editBtn && !deleteBtn) {
				menu.remove();
			}

			const container = document.createElement('div');
			container.appendChild(wrapper);

			return container.innerHTML;
		}

		updateMeetingsTable();

	})
})(jQuery);