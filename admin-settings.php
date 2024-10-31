<div class="wrap">
  <h1><?php _e('PushBots settings', $this->plugin->name); ?></h1>
      <form method="post" action="<?php echo esc_html( admin_url( 'admin.php?page=' . $this->plugin->name ) ); ?>">
          <?php wp_nonce_field('update-options') ?>
      <table class="form-table">
        <tr>
          <th><label for="pb_application_id"><?php _e('App ID', $this->plugin->name); ?></label></th>
          <td>
          <input type="text" name="pb_application_id" id="pb_application_id" value="<?php echo esc_html(get_option( 'pb_application_id')); ?>" class="regular-text" /><br />
          </td>
        </tr>
        <tr>
          <th><label for="pb_application_secret"><?php _e('App Secret', $this->plugin->name); ?></label></th>
          <td>
          <input type="text" name="pb_application_secret" id="pb_application_secret" value="<?php echo esc_html(get_option( 'pb_application_secret')); ?>" class="regular-text" /><br />
          </td>
        </tr>
        <tr>
          <th><label for="pb_website_url"><?php _e('Website URL', $this->plugin->name); ?></label></th>
          <td>
          <input type="text" name="pb_website_url" id="pb_website_url" value="<?php echo esc_url(get_option( 'pb_website_url')); ?>" class="regular-text" /><br />
        </td>
      </tr>
      <tr>
        <th><label for="pb_safari_push_id"><?php _e('Safari Push Id', $this->plugin->name); ?> <small>(optional)</small></label></th>
        <td>
          <input type="text" name="pb_safari_push_id" id="pb_safari_push_id" placeholder="web.com.pushbots.main" value="<?php echo esc_html(get_option( 'pb_safari_push_id')); ?>" class="regular-text" /><br />
        </td>
      </tr>
      <tr><td><hr/></td><td><hr/></td></tr>
      <tr>
        <th><label for="pb_enable_welcome_message"><?php _e('Welcome Notification', $this->plugin->name); ?></label></th>
        <td>
          <label><input type="checkbox" name="pb_enable_welcome_message" id="pb_enable_welcome_message"  <?php echo (get_option('pb_enable_welcome_message'))? 'checked="checked"' : ''; ?> /> Enable</label> <br />
        </td>
      </tr>
      <tr>
          <th><label for="pb_welcome_title"><?php _e('Welcome Title', $this->plugin->name); ?></label></th>
          <td>
            <input
                type="text"
                placeholder="<?php _e('Welcome 🙌🎉', $this->plugin->name) ?>"
                name="pb_welcome_title"
                id="pb_welcome_title"
                value="<?php echo esc_html(get_option('pb_welcome_title')); ?>"
                class="regular-text"
                <?php echo (get_option('pb_enable_welcome_message'))? '' : 'disabled'; ?>
            /><br />
          </td>
      </tr>
      <tr>
          <th><label for="pb_welcome_message"><?php _e('Welcome Message', $this->plugin->name); ?></label></th>
          <td>
            <input
                type="text"
                name="pb_welcome_message"
                id="pb_welcome_message"
                placeholder="<?php _e('Thanks for subscribing!', $this->plugin->name) ?>"
                value="<?php echo esc_html(get_option('pb_welcome_message')); ?>"
                <?php echo (get_option('pb_enable_welcome_message'))? '' : 'disabled'; ?>
                class="regular-text"
            /><br />
          </td>
      </tr>
  </table>
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Settings', $this->plugin->name); ?>"></p>
  </form>
</div>
