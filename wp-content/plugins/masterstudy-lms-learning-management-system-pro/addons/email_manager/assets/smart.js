(function ($) {
	'use strict';

	const ARROW_CLASS = 'stm-lms-email-header-toggle-arrow';
	const COLLAPSED_CLASS = 'stm-lms-email-section-collapsed';

	const ARROW_HTML =
		'<span class="' + ARROW_CLASS + '" role="button" tabindex="0" aria-label="Collapse section">' +
		'<i class="stmlms-chevron_down"></i>' +
		'</span>';

	function getSectionFields($enableField) {
		// All fields are siblings inside the same ".row".
		const $row = $enableField.closest('.row');
		const $children = $row.children('.wpcfto-box-child');

		const startIndex = $children.index($enableField);
		if (startIndex === -1) {
			return $();
		}

		// Collect fields after current _enable until the next _enable (or end).
		const $result = $();

		for (let i = startIndex + 1; i < $children.length; i++) {
			const $item = $children.eq(i);
			const dataField = String($item.attr('data-field') || '');

			if (dataField.endsWith('_enable')) {
				break;
			}

			$result.push($item.get(0));
		}

		return $result;
	}function ensureSectionFieldsRendered($enableField) {
        const $row = $enableField.closest('.row');
    
        // If already present → done.
        if (getSectionFields($enableField).length) {
            return Promise.resolve();
        }
    
        const $checkbox = $enableField.find('input[type="checkbox"]').first();
        if (!$checkbox.length) {
            return Promise.resolve();
        }
    
        const initialChecked = $checkbox.prop('checked');
    
        return new Promise((resolve) => {
            const observer = new MutationObserver(() => {
                if (getSectionFields($enableField).length) {
                    observer.disconnect();
    
                    // Restore original state if we changed it.
                    if (!initialChecked) {
                        $checkbox.trigger('click');
                    }
    
                    // Give Vue a tick to finish DOM updates.
                    setTimeout(resolve, 0);
                }
            });
    
            observer.observe($row.get(0), { childList: true, subtree: true });
    
            // Force render by enabling once (only if it was disabled).
            if (!initialChecked) {
                $checkbox.trigger('click');
            } else {
                // It was enabled but still no fields (rare) → resolve anyway.
                setTimeout(resolve, 0);
            }
    
            // Safety timeout (avoid hanging forever).
            setTimeout(() => {
                observer.disconnect();
                resolve();
            }, 1500);
        });
    }
    

    function setCollapsed($enableField, shouldCollapse) {
        const $sectionFields = getSectionFields($enableField);
    
        // Store collapse state in data attribute to persist independently of checkbox
        $enableField.attr('data-email-collapsed', shouldCollapse ? '1' : '0');
        $enableField.toggleClass(COLLAPSED_CLASS, shouldCollapse);
    
        if (shouldCollapse) {
            $sectionFields
                .removeClass('stm-lms-email-force-visible')
                .hide();
            return;
        }
    
        // Expanded: force visible even if WPCFTO hides them when checkbox is OFF.
        $sectionFields
            .addClass('stm-lms-email-force-visible')
            .show();
    }
    

	function toggleCollapsed($enableField) {
		const isCollapsed = $enableField.hasClass(COLLAPSED_CLASS);
		setCollapsed($enableField, !isCollapsed);
	}

	function injectArrows() {
		// Only for "enable" fields.
		$('.wpcfto-box-checkbox').each(function () {
			const $enableField = $(this);
			const dataField = String($enableField.attr('data-field') || '');

			if (!dataField.endsWith('_enable')) {
				return;
			}

			const $label = $enableField.find('.wpcfto-field-aside__label').first();
			if (!$label.length) {
				return;
			}

			if ($label.find('.' + ARROW_CLASS).length) {
				// Arrow already exists, restore collapse state from data attribute
				const savedState = $enableField.attr('data-email-collapsed');
				if (savedState !== undefined) {
					setCollapsed($enableField, savedState === '1');
				}
				return;
			}

			$label.append(ARROW_HTML);

			// Default state: collapsed
			setCollapsed($enableField, true);
		});
	}

	$(document).ready(function () {
		injectArrows();

		// Vue/WPCFTO async rendering.
		new MutationObserver(injectArrows).observe(document.body, {
			childList: true,
			subtree: true,
		});

		$(document).on(
			'click',
			'#emails_to_admin .wpcfto-box-checkbox,' +
			'#emails_to_instructors .wpcfto-box-checkbox,' +
			'#emails_to_students .wpcfto-box-checkbox,' +
			'#system_notifications .wpcfto-box-checkbox',
			function (e) {

				// ❌ Ignore clicks on interactive elements
				if (
					$(e.target).closest(
						'input, textarea, select, button, label'
					).length
				) {
					return;
				}

				// ❌ Ignore direct arrow clicks (already handled)
				if ($(e.target).closest('.stm-lms-email-header-toggle-arrow').length) {
					return;
				}

				// ✅ Trigger arrow toggle (open OR close)
				$(this)
					.find('.stm-lms-email-header-toggle-arrow')
					.trigger('click');
			}
		);


		// Arrow click ONLY collapses/expands — does NOT click checkbox.
		$(document).on('click keydown', '.' + ARROW_CLASS, function (e) {
			if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
				return;
			}

			e.preventDefault();
			e.stopPropagation();

			const $enableField = $(this).closest('.wpcfto-box-checkbox');
			const $checkboxWrapper = $enableField.find('.wpcfto-admin-checkbox-wrapper');
			const $checkbox = $enableField.find('input[type="checkbox"]').first();
			const wasDisabled = $checkboxWrapper.length && !$checkboxWrapper.hasClass('active');
			
			// If toggle is not active (disabled), enable it by simulating click on checkbox
			// but then remove active class to keep it visually disabled
			if (wasDisabled && $checkbox.length && !$checkbox.is(':checked')) {
				// Simulate click on the checkbox to trigger WPCFTO rendering
				$checkbox.trigger('click');
				// Immediately remove active class to keep toggle visually disabled
				setTimeout(() => {
					$checkboxWrapper.removeClass('active');
				}, 0);
			}
			
			const isCollapsed = $enableField.hasClass(COLLAPSED_CLASS);
			
			if (isCollapsed) {
				// Expanding: make sure fields exist first.
				// If we just enabled the toggle, wait a bit for WPCFTO to render
				if (wasDisabled) {
					setTimeout(() => {
						ensureSectionFieldsRendered($enableField).then(() => {
							setCollapsed($enableField, false);
						});
					}, 100);
				} else {
					ensureSectionFieldsRendered($enableField).then(() => {
						setCollapsed($enableField, false);
					});
				}
			} else {
				// Collapsing: just hide.
				setCollapsed($enableField, true);
			}
		});

		// Prevent checkbox changes from affecting collapse state
		// Listen to checkbox changes and preserve collapse state
		$(document).on('change', '.wpcfto-box-checkbox input[type="checkbox"]', function() {
			const $enableField = $(this).closest('.wpcfto-box-checkbox');
			const dataField = String($enableField.attr('data-field') || '');
			
			// Only handle _enable checkboxes
			if (!dataField.endsWith('_enable')) {
				return;
			}

			const isChecked = $(this).is(':checked');
			const isCollapsed = $enableField.hasClass(COLLAPSED_CLASS);
			const $sectionFields = getSectionFields($enableField);

			// If expanded, force show fields even when checkbox is unchecked
			if (!isCollapsed) {
				$sectionFields
					.addClass('stm-lms-email-force-visible')
					.show();
			}
			// If collapsed, let WPCFTO handle visibility (fields will be hidden anyway)
		});
	});
})(window.jQuery);


(function ($) {
    let storedContext = null;
    let lastSmartTagButton = null;
    const selector = '[data-vue="stm_lms_email_manager_settings"]';

    $(document).ready(function () {

        const $vm = $(selector);
        if (!$vm.length) return;

        if (!$('#suggestionMenu').length) {
            $('body').append('<div id="suggestionMenu" class="suggestion-menu"></div>');
        }

        // Typing { in editor
        $(document).on('keyup', '.trumbowyg-editor', function (e) {
            if (e.key === '{') {
                onBraceTypedInEditor(this);
            }
        });

        // Typing { in input (like Subject field)
        $(document).on('keyup', '.wpcfto-box-text input[type="text"]', function (e) {
            if (e.key === '{') {
                onBraceTypedInInput(this);
            }
        });

        // Click outside hides suggestion menu
        $(document).on('mousedown', function (e) {
            // if click is outside BOTH the menu AND any smart-tag-button
            if (
                !$(e.target).closest('#suggestionMenu').length &&
                !$(e.target).closest('.smart-tag-button').length
            ) {
                hideSuggestionMenu();
            }
        });

        // Add Smart Tag button + tip below editor
        $('.wpcfto-box-trumbowyg').each(function () {
            const $wrapper = $(this);
            if ($wrapper.find('.smart-tag-footer').length) return;

            const raw = $wrapper.attr('data-vars');
            if (!raw) return;

            const $tip = $('<p class="smart-tag-tip">')
                .text('💡 To add a smart tag, click the button or type `{` inside the editor.');
            const $button = $(`
                <button type="button" class="smart-tag-button">
                    Add Smart Tag <span class="smart-tag-icon">&#9662;</span>
                </button>
            `);


            const $footer = $('<div class="smart-tag-footer">').append($tip);

            $wrapper.append($footer);
            $wrapper.css('position', 'relative'); // Ensure relative for absolute child
            $wrapper.append($button); // Append button separately

        });

        // Smart Tag Button click → show menu above button
        $(document).on('click', '.smart-tag-button', function (e) {
            e.preventDefault(); // stop default reflow

            const $button = $(this);
            const $editor = $button.closest('.wpcfto-box-trumbowyg').find('.trumbowyg-editor')[0];
            if (!$editor) return;

            const $menu = $('#suggestionMenu');
            const isOpen = $menu.is(':visible');

            // ✅ If menu is open and same button → just close and stop
            if (isOpen && lastSmartTagButton && lastSmartTagButton.is($button)) {
                hideSuggestionMenu(); // will reset icon
                lastSmartTagButton = null;
                return;
            }

            // ✅ Open menu for new click
            hideSuggestionMenu(); // close others
            $('.smart-tag-button').removeClass('open');

            $button.addClass('open');
            lastSmartTagButton = $button;

            $editor.focus();

            const sel = window.getSelection();
            if (!sel || sel.rangeCount === 0) return;

            const range = sel.getRangeAt(0).cloneRange();
            storedContext = { type: 'editor', range };

            buildAndShowMenu($editor, null, null, 'editor', 'button', $button);
        });

        // Keyboard nav for menu
        $(document).on('keydown', '#suggestionMenu, .trumbowyg-editor, .wpcfto-box-text input[type="text"]', function (e) {
            const $menu = $('#suggestionMenu');
            if (!$menu.is(':visible')) return;

            const $items = $menu.find('.suggestion-item');
            if (!$items.length) return;

            const currentIndex = $items.index($items.filter('.focused'));

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    setFocusOnItem($items, currentIndex + 1 >= $items.length ? 0 : currentIndex + 1);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    setFocusOnItem($items, currentIndex - 1 < 0 ? $items.length - 1 : currentIndex - 1);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (currentIndex >= 0) $items.eq(currentIndex).trigger('click');
                    break;
                case 'Escape':
                    e.preventDefault();
                    hideSuggestionMenu();
                    break;
            }
        });
    });

    function onBraceTypedInEditor(editorDiv) {
        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return;
        const range = sel.getRangeAt(0).cloneRange();
        storedContext = { type: 'editor', range };
        const coords = getCaretDocumentCoords();
        if (!coords) return;
        buildAndShowMenu(editorDiv, coords.left, coords.top, 'editor');
    }

    function onBraceTypedInInput(inputEl) {
        const input = inputEl;
        const caretIndex = input.selectionStart;
        if (caretIndex === null || caretIndex === undefined) return;
        storedContext = { type: 'input', element: input, pos: caretIndex };
        const rect = input.getBoundingClientRect();
        const docLeft = rect.left + window.scrollX;
        const docTop = rect.top + window.scrollY + rect.height;
        buildAndShowMenu(input, docLeft, docTop, 'input');
    }

    function getCaretDocumentCoords() {
        const sel = window.getSelection();
        if (!sel || sel.rangeCount === 0) return null;
        const range = sel.getRangeAt(0).cloneRange();
        range.collapse(true);
        const rect = range.getBoundingClientRect();
        if (!rect) return null;
        return {
            left: rect.left + window.scrollX,
            top: rect.top + window.scrollY + rect.height,
        };
    }

    function buildAndShowMenu(element, left, top, contextType, trigger = 'typing', $triggerEl = null) {
        let vars = null;

        if (contextType === 'editor') {
            const $wrapper = $(element).closest('.wpcfto-box-trumbowyg');
            const raw = $wrapper.attr('data-vars');
            if (raw) {
                try {
                    vars = JSON.parse(raw);
                } catch (e) {
                    console.warn('Invalid JSON in data-vars (editor)', e);
                }
            }
        } else {
            const $textWrapper = $(element).closest('.wpcfto-box-text');
            const $editorWrapper = $textWrapper.siblings('.wpcfto-box-trumbowyg');
            const raw = $editorWrapper.attr('data-vars');
            if (raw) {
                try {
                    vars = JSON.parse(raw);
                } catch (e) {
                    console.warn('Invalid JSON in data-vars (input)', e);
                }
            }
        }

        const dynamicSuggestions = [];
        if (vars) {
            const skipKeys = [
                'certificate_preview',
                'button',
                'site_url',
                'dashboard_url',
                'course_edit_url',
                'course_url',
                'lesson_url',
                'comment_content',
                'assignment_comment',
                'analytics_url',
                'attempt_url',
                'assignment_url',
                'admin_email',
                'text',
                'reset_url',
                'attempt_url',
                'password',
                'student_email',
                'quiz_url',
                'login_url',
                'mail'
            ];

            Object.entries(vars).forEach(([key, label]) => {
                // ✅ Skip specified keys in input fields
                if (contextType === 'input' && skipKeys.includes(key)) return;

                if (key && label) {
                    dynamicSuggestions.push({
                        leftText: label,
                        rightText: `{{${key}}}`,
                    });
                }
            });
        }
        if (!dynamicSuggestions.length) return;

        const $menu = $('#suggestionMenu');
        $menu.empty();

        dynamicSuggestions.forEach((item) => {
            const $entry = $('<div>')
                .addClass('suggestion-item')
                .append(
                    $('<span>').addClass('suggestion-left').text(item.leftText),
                    $('<span>').addClass('suggestion-right').text(item.rightText)
                )
                .on('click', function () {
                    insertSuggestion(item.rightText);
                });
            $menu.append($entry);
        });

        $menu.find('.suggestion-item').removeClass('focused');

        if (trigger === 'typing') {
            const menuWidth = $menu.outerWidth() || 270;
            const isEditor = $(element).hasClass('trumbowyg-editor');

            if (isEditor) {
                // Get editor bounding box
                const editorRect = element.getBoundingClientRect();
                const editorMidX = editorRect.left + (editorRect.width / 2);

                // Caret is right of center → open left
                if (left > editorMidX) {
                    const adjustedLeft = left - menuWidth + 5;
                    const safeLeft = Math.max(10, adjustedLeft);
                    $menu.css({
                        left: `${safeLeft}px`,
                        top: `${top}px`,
                        display: 'block'
                    });
                } else {
                    // Caret is left of center → open right
                    $menu.css({
                        left: `${left}px`,
                        top: `${top}px`,
                        display: 'block'
                    });
                }
            } else {
                // Input field → always open to right
                $menu.css({
                    left: `${left}px`,
                    top: `${top}px`,
                    display: 'block'
                });
            }
        } else if (trigger === 'button' && $triggerEl) {
            const rect = $triggerEl[0].getBoundingClientRect();
            const scrollTop = window.scrollY;
            const scrollLeft = window.scrollX;
            const menuHeight = $menu.outerHeight() || 160;
            const menuWidth = $menu.outerWidth() || 270;

            $menu.css({
                left: `${rect.right + scrollLeft - menuWidth}px`, // align menu's right edge to button's right
                top: `${rect.top + scrollTop - menuHeight - 4}px`,
                marginTop: `-10px`,
                boxShadow: `0px 327px 91px 0px rgba(0, 0, 0, 0.00), 0px 209px 84px 0px rgba(0, 0, 0, 0.01), 0px 118px 71px 0px rgba(0, 0, 0, 0.05), 0px 52px 52px 0px rgba(0, 0, 0, 0.09), 0px 13px 29px 0px rgba(0, 0, 0, 0.10)
`,
                display: 'block',
            });
        }

        setFocusOnItem($menu.find('.suggestion-item'), 0);
    }

    function hideSuggestionMenu() {
        $('#suggestionMenu').hide();
        $('.smart-tag-button').removeClass('open'); // 🔁 Reset icon on menu close
    }

    function insertSuggestion(value) {
        if (!storedContext) return;
        hideSuggestionMenu();

        if (storedContext.type === 'editor') {
            // Find the correct .trumbowyg-editor for this context
            let editor = storedContext.range.commonAncestorContainer;
            while (editor && (!editor.classList || !editor.classList.contains('trumbowyg-editor'))) {
                editor = editor.parentNode;
            }
            if (!editor) {
                editor = document.querySelector('.trumbowyg-editor');
            }

            // Get the related textarea (Trumbowyg is initialized on this!)
            var $box = $(editor).closest('.trumbowyg-box');
            var $textarea = $box.find('textarea.trumbowyg-textarea');

            // Use Trumbowyg's API on the textarea
            $textarea.trumbowyg('restoreRange');

            let selection = window.getSelection();
            if (selection.rangeCount > 0) {
                let range = selection.getRangeAt(0);
                let container = range.startContainer;
                let offset = range.startOffset;

                if (container.nodeType === 3) { // text node
                    let text = container.textContent;
                    // Remove up to 2 braces if found before the caret
                    let removeCount = 0;
                    if (offset >= 2 && text.substr(offset - 2, 2) === '{{') {
                        removeCount = 2;
                    } else if (offset >= 1 && text.substr(offset - 1, 1) === '{') {
                        removeCount = 1;
                    }

                    if (removeCount > 0) {
                        // Remove the brace(s)
                        let before = text.slice(0, offset - removeCount);
                        let after = text.slice(offset);
                        container.textContent = before + after;

                        // Move the caret back
                        range.setStart(container, offset - removeCount);
                        range.collapse(true);
                        selection.removeAllRanges();
                        selection.addRange(range);
                    }
                }
            }

            $textarea.trumbowyg('execCmd', {
                cmd: 'insertHTML',
                param: value + ' ',
                forceCss: false
            });
            $textarea.trigger('tbwchange'); // force sync to textarea

            storedContext = null;
        } else if (storedContext.type === 'input') {
            // your original input logic, unchanged
            const input = storedContext.element;
            const pos = storedContext.pos;
            const currentValue = input.value;

            let removeCount = 0;
            if (pos > 1 && currentValue.substr(pos - 2, 2) === '{{') {
                removeCount = 2;
            } else if (pos > 0 && currentValue.charAt(pos - 1) === '{') {
                removeCount = 1;
            }

            const beforeBrace = currentValue.slice(0, pos - removeCount);
            const afterBrace = currentValue.slice(pos);
            const newValue = beforeBrace + value + ' ' + afterBrace;

            input.value = newValue;
            const newCaretPos = beforeBrace.length + value.length + 1;
            input.setSelectionRange(newCaretPos, newCaretPos);

            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));

            storedContext = null;
        }
    }

    function insertTextAtCursorByElement(editorElement, text) {
        var $editor = $(editorElement);
        $editor.trumbowyg('restoreRange');
        $editor.trumbowyg('execCmd', {
            cmd: 'insertHTML',
            param: text,
            forceCss: false
        });
        $editor.trigger('tbwchange');
    }

    function setFocusOnItem($items, newIndex) {
        $items.removeClass('focused');
        const $target = $items.eq(newIndex).addClass('focused');

        const $menu = $('#suggestionMenu');
        const menuTop = $menu.scrollTop();
        const menuH = $menu.innerHeight();
        const itemTop = $target.position().top;
        const itemH = $target.outerHeight();

        if (itemTop < 0) {
            $menu.scrollTop(menuTop + itemTop);
        } else if (itemTop + itemH > menuH) {
            $menu.scrollTop(menuTop + (itemTop + itemH - menuH));
        }
    }
})(jQuery);
jQuery(function ($) {
	const selector = '#emails_to_students-stm_lms_time_limit_expiration_reminder_email_send_email_before';
	const placeholderText = '2w 1d 5h 4m';

	const DURATION_REGEX =
		/^(?:(\d+)\s*w\s*)?(?:(\d+)\s*d\s*)?(?:(\d+)\s*h\s*)?(?:(\d+)\s*m\s*)?$/i;

	function isValidDuration(value) {
		value = String(value || '').trim();

		// Allow empty (do not block save)
		if (!value) {
			return true;
		}

		const match = value.match(DURATION_REGEX);
		if (!match) {
			return false;
		}

		return Boolean(match[1] || match[2] || match[3] || match[4]);
	}

	function applyEnhancements() {
		const $input = $(selector);

		if (!$input.length) {
			return;
		}

		// Set placeholder (WPCFTO may overwrite, so we re-apply)
		if ($input.attr('placeholder') !== placeholderText) {
			$input.attr('placeholder', placeholderText);
		}

		// Bind once
		if ($input.data('msDurationBound')) {
			return;
		}

		$input.data('msDurationBound', true);

		function validate() {
			const value = $input.val();
			if (isValidDuration(value)) {
				$input.removeClass('stm-lms-invalid');
			} else {
				$input.addClass('stm-lms-invalid');
			}
		}

		$input.on('input blur', validate);

		// Initial validate (in case value is prefilled)
		validate();
	}

	// Run now
	applyEnhancements();

	// Run a few times after load (WPCFTO render timing)
	setTimeout(applyEnhancements, 200);
	setTimeout(applyEnhancements, 600);
	setTimeout(applyEnhancements, 1200);

	// Also re-apply on clicks/tabs inside Email Manager (cheap + safe)
	$(document).on('click', '.wpcfto_box', function () {
		setTimeout(applyEnhancements, 50);
	});
});
