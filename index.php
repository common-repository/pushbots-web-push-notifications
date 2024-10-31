<?php
/**
 * Plugin Name:       PushBots - Web Push Notifications
 * Plugin URI:        https://pushbots.com/
 * Description:       Reach out to your Wordpress visitors with browser desktop push notifications
 * Version:           1.1.0
 * Tested up to:      5.4.0
 * Author:            PushBots
 * Author URI:        https://github.com/PushBots
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
  exit;
}
require_once __DIR__ . "/pushbots.php";

$wpPushBots = new WPPushBots();

