(function ($) {
  $(document).ready(function () {
    const vars = masterstudy_woocommerce_orders;
    $('.masterstudy-button_icon-print').on('click', function () {
      window.print();
    });

    let referrerUrl = document.referrer
    if (referrerUrl) {
      let button = $('.masterstudy-orders-details a.masterstudy-button_icon-arrow-left');

      if (button.length) {
        button.attr('href', referrerUrl);
      }
    }

    const note = $('.masterstudy-orders-table__notes')
    const text = $(note).find('.masterstudy-orders-note-text')
    const input = $(note).find('.masterstudy-orders-note-input')
    const actions = $(note).find('.masterstudy-orders-note__actions')
    const editBtn = $(actions).find('.masterstudy-orders-note-edit')
    const saveBtn = $(actions).find('.masterstudy-orders-note-save')
    const cancelBtn = $(actions).find('.masterstudy-orders-note-cancel')
    let oldVal = '';

    input.on('input', () => {
      saveBtn.prop('disabled', oldVal === input.val())
    })

    const toggleNoteState = () => {
      text.toggle()
      input.toggle()

      editBtn.toggle()
      saveBtn.toggle()
      cancelBtn.toggle()
    }

    actions.on('click', '.masterstudy-orders-note-edit', function () {
      oldVal = input.val();
      saveBtn.prop('disabled', true)

      toggleNoteState()
    })

    cancelBtn.on('click', function () {
      toggleNoteState()
      input.val(oldVal);
    })

    saveBtn.on('click', function () {
      const note = input.val()

      $.ajax({
        url: vars.ajaxurl,
        method: 'POST',
        dataType: 'json',
        context: this,
        data: {
          'order_id': vars.order.order_id,
          'order_status': vars.order.status,
          'order_note': note,
          'action': 'stm_lms_save_order',
          'nonce': vars.nonce_save_order,
        },
        success: function () {
          text.text(note)
          toggleNoteState()
        }
      });
    })
  });
})(jQuery);
