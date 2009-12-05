<?php
/*
Plugin Name: Remind Me
Plugin URI: http://imod.co.za/remind-me/
Description: Remind Me helps you create deep links on your blog easily by showing related posts with its fancy jQuery look up window in the Write Panel of your Dashboard.
Version: 0.1
Author: Christopher Mills
Author URI: http://www.christophermills.co.za
*/
define('JT_PATH', trailingslashit(dirname(__FILE__)));
add_action('wp_ajax_remindMe', 'remindMe');
		
if (!function_exists("remindMe")){
	function remindMe() {
		require_once(JT_PATH . 'jtFrame/class.mainframe.php'); 
		$jtMainFrame = jtMainFrame::newInstance(dirname(plugin_basename(__FILE__)));		
		$jtDispatcher = new jtDispatcherRemindMe(is_admin());
		// Setup the installer on activation of the plugin
		register_activation_hook(__FILE__, array(&$jtDispatcher, 'install'));
		if (is_admin() && $jtMainFrame->ajax){
			$jtDispatcher->adminDispatch();		
		} else {
			$jtDispatcher->execute();
		}
	}
}
if (function_exists("remindMe") && !isset($_POST["jt_ajax"])) {		
		remindMe();
}
?>
