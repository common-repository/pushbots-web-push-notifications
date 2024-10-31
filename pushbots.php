<?php
class WPPushBots {
  public function __construct()
  {
		// Plugin Details
    $this->plugin               = new stdClass;
    $this->plugin->name         = 'pushbots'; // Plugin Folder
    $this->plugin->sdk         = 'sdk'; // Plugin Folder
    $this->plugin->displayName  = 'PushBots'; // Plugin Name
    $this->plugin->version      = '1.1.0';
    $this->plugin->folder       = plugin_dir_path( __FILE__ );
    $this->plugin->url          = plugin_dir_url( __FILE__ );
    $this->plugin->db_welcome_dismissed_key = $this->plugin->name . '_welcome_dismissed_key';
    // Get latest settings
    $this->settings = array(
      'pb_application_id' => esc_html( wp_unslash( get_option('pb_application_id') ) ),
      'pb_application_secret' => esc_html( wp_unslash( get_option('pb_application_secret') ) ),
      'pb_website_url' => (get_option('pb_website_url')) ? esc_html( wp_unslash( get_option('pb_website_url') ) ) : get_site_url(),
      'pb_safari_push_id' => esc_html( wp_unslash( get_option('pb_safari_push_id') ) ),
      'pb_enable_welcome_message' => esc_html( wp_unslash( get_option('pb_enable_welcome_message') ) ),
      'pb_welcome_title' => esc_html( wp_unslash( get_option('pb_welcome_title') ) ),
      'pb_welcome_message' => esc_html( wp_unslash( get_option('pb_welcome_message') ) ),
    );


	// Hooks
    add_action( 'admin_menu', array($this, 'pushbots_menu' ));
    add_action( 'admin_enqueue_scripts', array($this, 'register_admin_scripts') );
    add_image_size( 'pb_notification', 256, 256, true );

    if($this->settings['pb_application_id'])
        require_once __DIR__ . "/script.php";

  }

  function pushbots_menu() {
    add_menu_page( $this->plugin->displayName, $this->plugin->displayName, 4, $this->plugin->name, array( $this, 'pb_admin_settings'));
    add_action( 'save_post', array($this,'send_article_notification'));
    // add_action( 'post_submitbox_misc_actions', array($this,'article_push_checkbox') );
    add_action( 'add_meta_boxes', array($this,'cd_meta_box_add') );
  }

  function register_admin_scripts() {
      wp_enqueue_script( 'pushbots-toggle', plugins_url( 'admin-settings.js', __FILE__ ) );
  }

  function pb_admin_settings() {
	// only admin user can access this page
	if ( !current_user_can( 'administrator' ) ) {
		echo '<p>' . __( 'Sorry, you are not allowed to access this page.', $this->plugin->name ) . '</p>';
		return;
	}

    // Save Settings
    if ( isset( $_REQUEST['submit'] ) ) {
        $app_id = sanitize_text_field($_REQUEST['pb_application_id']);
        $app_secret = sanitize_text_field($_REQUEST['pb_application_secret']);
        $website_url = esc_url_raw($_REQUEST['pb_website_url']);
        $safari_push_id = sanitize_text_field($_REQUEST['pb_safari_push_id']);
        $welcome_message_enabled = $_REQUEST['pb_enable_welcome_message'];

        if($_REQUEST['pb_enable_welcome_message'] === 'on' || !$_REQUEST['pb_enable_welcome_message']) {
          update_option( 'pb_enable_welcome_message', $welcome_message_enabled);          
        }
        
        update_option( 'pb_application_id',  $app_id);
        update_option( 'pb_application_secret', $app_secret );
        update_option( 'pb_website_url', $website_url);
        update_option( 'pb_safari_push_id', $safari_push_id );
        if($welcome_message_enabled) {
            update_option( 'pb_welcome_title', sanitize_text_field($_REQUEST['pb_welcome_title']));
            update_option( 'pb_welcome_message', sanitize_text_field($_REQUEST['pb_welcome_message']));
        }
        $this->message = __( 'Settings Saved.', 'pushbots' );
    }
    // Load Settings Form
    require_once __DIR__ . "/admin-settings.php";

}


  function cd_meta_box_add() {
    add_meta_box( 'pb-box', $this->plugin->displayName, array($this, 'article_push_checkbox'), 'post', 'side', 'high' );
  }

  function article_push_checkbox()
  {
    global $post;
    /* check if this is a post, if not then we won't add the custom field */
    /* change this post type to any type you want to add the custom field to */
    if (get_post_type($post) != 'post') return false;
    /* get the value corrent value of the custom field */
    ?>
    <div class="misc-pub-section">
      <label><input type="checkbox" name="pb_send_notification" id="pb_send_notification" /> Send push notification</label>
    </div>
    <?php
  }

  function send_article_notification($postid) {
      /* check if this is an autosave */
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

      /* check if the user can edit this page */
      if ( !current_user_can( 'edit_page', $postid ) ) return false;
      /* check if there's a post id and check if this is a post */
      /* make sure this is the same post type as above */
      if(empty($postid) || $_POST['post_type'] != 'post' ) return false;
      /* check if the custom field is submitted (checkboxes that aren't marked, aren't submitted) */
      if(isset($_POST['pb_send_notification']) && get_post_status($postid) == 'publish'){
          /* store the value in the database */
          $appID =  $this->settings['pb_application_id'];;
          $appSecret =  $this->settings['pb_application_secret'];

          $args = array(
            "nTitle"=>html_entity_decode(get_the_title($postid), ENT_QUOTES, "UTF-8"),
            "openURL" => get_permalink($postid)
          );

          if(has_post_thumbnail( $postid )) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $postid ), 'pb_notification', true );
            $args['icon'] = $image[0] ;
          }


          $curl = curl_init();
          $post = get_post($postid);
          $msg_data = array (
            'message' => 
            array (
              0 => 
              array (
                'language' => 'en',
                'body' => htmlspecialchars_decode(wp_trim_words($post->post_content, 55))
              ),
            ),
            'payload' => $args,
            'platforms' => [2,3,4,5],
            'source' => 'api',
          );
          $data_string = json_encode($msg_data);

          curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.pushbots.com/3/push/campaign",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
              "x-pushbots-appid: " . $appID,
              "x-pushbots-secret: " . $appSecret,
              "Content-Type: application/json",
              "Content-Type: text/plain"
            ),
          ));
          
          curl_exec($curl);
          curl_close($curl);
          
          setup_postdata( $postid );
          
      }
  }

}
