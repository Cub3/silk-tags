<?php
/*
Plugin Name: * Silk Tags [BETA]
Plugin URI: http://webanyti.me
Description: Adds weight and allows query of weight on tags for content management
Version: 1.0
Author: Callum Silcock
Author URI: http://webanyti.me
Copyright (c) 2004-2006, 2008 Callum Silcock (http://webanyti.me)
This is a WordPress plugin (http://wordpress.org).

     _______. __   __       __  ___    .___________.    ___       _______      _______.
    /       ||  | |  |     |  |/  /    |           |   /   \     /  _____|    /       |
   |   (----`|  | |  |     |  '  /     `---|  |----`  /  ^  \   |  |  __     |   (----`
    \   \    |  | |  |     |    <          |  |      /  /_\  \  |  | |_ |     \   \    
.----)   |   |  | |  `----.|  .  \         |  |     /  _____  \ |  |__| | .----)   |   
|_______/    |__| |_______||__|\__\        |__|    /__/     \__\ \______| |_______/ 

*/

error_reporting(0); 	//Remove pesky warnings (if any)

global $wpdb;

define('SILK_TAGS_TABLE', $wpdb->prefix . 'silk_tags');

check_silk_tags(); 		//Check if need to install
check_silk_input(); 	//Check if user has input new tag
check_silk_delete(); 	//Check if user has deleted tag
check_silk_edit(); 		//Check if user has edited tag

/* Check If Database Exists --------------------------------------------------------------[CHECK_SILK_TAGS()]--- */

function check_silk_tags() {

global $wpdb;

//Set initial vars to assume this isn't a new install

$silk_tags_exists = false;

$tables = $wpdb->get_results("show tables");

	foreach ( $tables as $table ) {
		foreach ( $table as $value ) {
		  if ( $value == SILK_TAGS_TABLE ) {
		      $silk_tags_exists = true;
		    }
		}
	}
	if ($silk_tags_exists == false) {

		$sql = "CREATE TABLE " . SILK_TAGS_TABLE . " (
		                tag_id INT(11) NOT NULL AUTO_INCREMENT,
		                tag_name VARCHAR(30) NOT NULL,
		                tag_weight INT(11) NOT NULL,
		                tag_creation_date DATE NOT NULL,
		                PRIMARY KEY (tag_id)
		        )";
		$wpdb->get_results($sql); 		//Create the table if it dosen't exist...
	}
}

/* End database check ------------------------------------------------------------------------------------------ */

/* Check if input and ifset add to database ----------------------------------------------[CHECK_SILK_INPUT()]-- */

function check_silk_input() {
	$error_message = '';
	if(isset($_REQUEST['tag_name']) && isset($_REQUEST['tag_weight'])){
		foreach($_REQUEST['tag_name'] as $key => $tag_name){
			if($_REQUEST['tag_name'][$key] != 'Insert Tag Name' || $_REQUEST['tag_weight'][$key] != '#' || $_REQUEST['tag_name'][$key] != '' || $_REQUEST['tag_weight'][$key] != ''){
					global $wpdb;
					$sql = "INSERT INTO ".SILK_TAGS_TABLE." (tag_name, tag_weight, tag_creation_date) VALUES ('".strtolower($_REQUEST['tag_name'][$key])
					."','".$_REQUEST['tag_weight'][$key]."',NOW())";
   					$wpdb->get_results($sql);
			}
		}
	}
}

/* End input functions ----------------------------------------------------------------------------------------- */

/* Check actions to see if delete or edit ----------------------------------------------[CHECK_SILK_DELETE()]--- */

function check_silk_delete() {
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
		global $wpdb;
		$sql = "DELETE FROM ".SILK_TAGS_TABLE." WHERE tag_id = " . $_REQUEST['delete_id'];
		$wpdb->get_results($sql);
	}
}

function check_silk_edit() {
	if(isset($_REQUEST['tag_edit_id']) && isset($_REQUEST['tag_edit_name']) && isset($_REQUEST['tag_edit_weight'])) {
		foreach($_REQUEST['tag_edit_id'] as $key => $tag_id){
			if($_REQUEST['tag_edit_name'][$key] != 'Insert Tag Name' || $_REQUEST['tag_edit_weight'][$key] != '#') {
				global $wpdb;
				$sql = "UPDATE ".SILK_TAGS_TABLE." SET tag_name = '" . strtolower($_REQUEST['tag_edit_name'][$key]) . "', tag_weight = " . $_REQUEST['tag_edit_weight'][$key] . " WHERE tag_id = " . $_REQUEST['tag_edit_id'][$key];
				$wpdb->get_results($sql);
			}
		}
	}
}

/* End action functions ---------------------------------------------------------------------------------------- */

/* Start echoing functions for frontend --------------------------------------------------[GET_THE_WEIGHT()]---- */

function get_the_weight($current_post_id) {

	global $wpdb;
	global $wp_query;

	$post_tags = get_the_tags($current_post_id);

	$tag_name_array = array();
	foreach($post_tags as $tag){
		array_push($tag_name_array, strtolower($tag->name));
	}

	$tag_name_csv = implode("','", $tag_name_array);

	$tag_name_csv = "('".$tag_name_csv."')";

	$sql = "SELECT tag_weight FROM ".SILK_TAGS_TABLE." WHERE tag_name IN " . $tag_name_csv;

	$tag_weights = $wpdb->get_results($sql);

	$tag_weight_array = array();
	foreach($tag_weights as $tag_weight){
		array_push($tag_weight_array,$tag_weight->tag_weight);
	}

	$post_date = strtotime(get_the_date($d, $current_post_id));

	$now = time();

	$date_diff = $now - $post_date;

	$days_diff = floor($date_diff/(60*60*24)); //Amount of days ago

	$days_diff++;

	if($days_diff < 15) {
		$date_weight = (14 / $days_diff * 7); //Two weeks back in time, today gets 99 (kindof), day 14 gets 7.5
	}

	$weight->final = round((array_sum($tag_weight_array) / count($tag_weight_array)) + $date_weight);

	$weight->tags = round(array_sum($tag_weight_array) / count($tag_weight_array));


	$weight->days = $days_diff;

	return $weight;

}

/* End frontend functions -------------------------------------------------------------------------------------- */

/* Create Admin Page ----------------------------------------------------------------------[ADMINISTRATION]----- */

add_action('admin_menu', 'register_silk_tags_admin');

function register_silk_tags_admin() {
   add_menu_page('Silk Tags', 'Silk Tags', 'administrator', 'silk_tags/admin_area.php', '',   plugins_url('silk_tags/images/database.png'), 6);
}

/* End Admin Page Creation ------------------------------------------------------------------------------------- */


?>