jQuery(document).ready(function() {
  jQuery("#pb_enable_welcome_message").change(function() {
    if(this.checked) {
      jQuery("#pb_welcome_message, #pb_welcome_title").removeAttr('disabled');
    } else {
      jQuery("#pb_welcome_message, #pb_welcome_title").attr('disabled', 'disabled');
    }
  });
});
