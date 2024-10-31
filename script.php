<?php
wp_enqueue_script( 'pushbots-manifest', plugins_url( 'sdk/empty.js', __FILE__ ) );

add_filter( 'script_loader_tag', 'add_id_to_script', 10, 3 );

function add_id_to_script( $tag, $handle, $src ) {
    if ( $handle === 'pushbots-js' ) {
        $tag = '<script type="text/javascript" src="' . esc_url( $src ) . '" onload="PB.init()" async></script>';
    }
    return $tag;
}

$inline_js = 'var PB = PB || {};PB.q = PB.q || [];PB.events = PB.events || [];' . "\n";
$inline_js .= 'PB.app_id = "' . $this->settings["pb_application_id"] . '";' . "\n";
$inline_js .= 'PB.domain = "' . $this->settings['pb_website_url'] . '";' . "\n";
$inline_js .= 'PB.worker_url = "' . $this->plugin->url . 'sdk/pushbots-worker.js.php";' . "\n";
$inline_js .= 'PB.manifest_url = "' . $this->plugin->url . 'sdk/pushbots-push-manifest.json";' . "\n";
if($this->settings['pb_safari_push_id']) {
    $inline_js .= 'PB.safari_push_id = "' . $this->settings['pb_safari_push_id'] . '";' . "\n";
}
$inline_js .=  'PB.logging_enabled = false;' . "\n";
$inline_js .=  'PB.auto_subscribe = true;' . "\n";
if($this->settings['pb_enable_welcome_message']){
    //Welcome notification message
    $inline_js .= 'PB.welcome = {' . "\n";
    $inline_js .= 'title:"' . $this->settings['pb_welcome_title'] . '",' . "\n";
    $inline_js .= 'message:"' . $this->settings['pb_welcome_message'] . '",' . "\n";
    $inline_js .= 'url : PB.domain' . "\n";
    $inline_js .= '};' . "\n";
}
$inline_js .= 'function sendNotification(){' . "\n";
$inline_js .= 'PB.register();' . "\n";
$inline_js .= 'PB.q.push(["sendNotification", {title:"Hey 🐬",message:"Why not?", url :"https://google.com"}]);' . "\n";
$inline_js .= '}' . "\n";

$inline_js .= 'PB.events.push(["onRegistered", function(data){console.log("onRegistered" , data);}]);' . "\n";
$inline_js .= 'PB.events.push(["onRegisteredOnPushBots", function(data){console.log("onRegisteredOnPushBots", data);}]);' . "\n";

wp_add_inline_script('pushbots-manifest', $inline_js);
wp_enqueue_script( 'pushbots-js', '//cdn.pushbots.com/js/sdk.min.js' );