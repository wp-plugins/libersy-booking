<?php
/*
Plugin Name: Libersy Booking
Plugin URI: http://dev.jigar.biz/
Description: show appointment related services of libersy booking on a Wordpress page.
Version: 1
Author: Jigar Shah
*/

global $wpdb;

define('PLUGIN_LIBERSY_BOOKING_001', 'libersy-booking');
define('lblevel','6');

// calls the zh_install function on activation
register_activation_hook( __FILE__, 'lb_install' );
// calls the zh_uninstall function on deactivation
register_deactivation_hook( __FILE__, 'lb_uninstall' );


// does the initial setup
function lb_install() {

	global $wpdb;
 
}

add_action('init', 'lb_do_filter');

function lb_do_filter() {
	add_filter('the_content', 'lb_content_filter', 1);
	
}
function lb_content_filter($post) {
	
	
	if (substr_count($post, '[Libersy-Data]') > 0) {
		$post = str_replace('[Libersy-Data]', lb_show_data(), $post);
	}
	
	return $post;
}
// Admin Menu 
add_action('admin_menu', 'lb_add_admin');

// Add option page
function lb_add_admin() {
	 
 	add_options_page('Libersy Booking', 'Libersy-Booking', lblevel, 'libersysettings', 'lb_options'); // add setting page
	
}

function lb_options() {
		
		if (isset($_POST['lb-set-submit'])) {
			
			update_option('lb_service_url', $_POST['lb_service_url']);
			update_option('lb_page_id', $_POST['lb_page_id']);
			
			update_option('lb_iframe_width', $_POST['lb_iframe_width']);
			update_option('lb_iframe_height', $_POST['lb_iframe_height']);
			update_option('lb_iframe_scrolling', $_POST['lb_iframe_scrolling']);
			
			echo '<div id="message" class="updated fade"><p><strong>Changes Saved</strong></p></div>';
		}
?>

	<div class="wrap">
			
		<h2>Libersy Booking Plugin </h2><br />
		<table class="widefat" cellspacing="0" id="active-plugins-table">
			<thead>
			<tr>
				<th scope="col"><?php _e('Introduction'); ?></th>
			</tr>
			</thead>
			
			<tbody class="plugins">
				<tr class="inactive">
					<td class="desc">
						<p><?php _e('show appointment related services of libersy booking on a Wordpress page.'); ?></p>
						<h4><?php _e('Installation'); ?></h4>
						
						  <p><?php  _e('1. Unzip and upload the Libersy Booking folder to wp-content/plugins/'); ?> </p>
						  <p><?php  _e('2. Activate the plugin in WP Admin -> Plugins.'); ?> </p>
						  <p><?php echo _e('3. Copy below short-code and paste it into your page content.'); ?><br />&nbsp;[Libersy-Data] </p>
						  <p><?php echo _e('4. At the bottom of your page at "Libersy Booking Options" you can put the service url.'); ?></p>
						  
						   
					</td>
				</tr>
			</tbody>
		</table>
		<br />
		
		<form action="" method="post">
		<table class="widefat" cellspacing="0" border="1" id="inactive-plugins-table">
			<thead>
				<tr>
					<th scope="col" colspan="2"><?php echo 'Setting'; ?></th>
					
				</tr>
			</thead>
	
			<tbody class="plugins">
			
				<tr class='active'>
					<td width="25%" class='name'><?php _e('Services URL'); ?></td>
					<th  scope='row' class='check-column'>
						<input type="text" name="lb_service_url" value="<?php echo get_option('lb_service_url');?>" size="50"  />
					</th>
				</tr>
				
				<tr class='active'>
					<td width="25%" class='name'><?php _e('Select Page'); ?></td>
					<th  scope='row' class='check-column'>
						<? /*<input type="text" name="lb_page_id" value="<?php echo get_option('lb_page_id');?>" size="50" style="padding:3px;" />*/ ?>
						<?php wp_dropdown_pages("name=lb_page_id&show_option_none=".__('- Select -')."&selected=" .get_option('lb_page_id')); ?>
						
					</th>
				</tr>
				
				<tr class='active'>
					<td width="25%" class='name'><?php _e('Iframe Width'); ?></td>
					<th  scope='row' class='check-column'>
						<input type="text" name="lb_iframe_width" value="<?php echo get_option('lb_iframe_width');?>" size="10"  />
					</th>
				</tr>
				
				<tr class='active'>
					<td width="25%" class='name'><?php _e('Iframe Height'); ?></td>
					<th  scope='row' class='check-column'>
						<input type="text" name="lb_iframe_height" value="<?php echo get_option('lb_iframe_height');?>" size="10"  />
					</th>
				</tr>
				
				<tr class='active'>
					<td width="25%" class='name'><?php _e('Iframe Scrolling'); ?></td>
					<th  scope='row' class='check-column'  style="margin-left:8px;">

						<select name="lb_iframe_scrolling">
							<option value="auto">Auto</option>
							<option <?php if(get_option('lb_iframe_scrolling') == 'off') { echo 'selected="selected"';} ?> value="off">No</option>
						</select> 
					</th>
				</tr>
				
				
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="lb-set-submit"   value="<?php _e('Update Options') ?>" /></p>
		</form>
		
		
		
	</div>
<?php
}
// admin_init is triggered before any other hook when a user access the admin area.
add_action('admin_init', 'lb_admin_init_custom');

function lb_admin_init_custom() {
	
	add_meta_box( 'post_libersy_booking_box', 'Libersy Booking Options', 'lb_post_meta_box', 'page', 'advanced', 'high', 1 );

}

function lb_post_meta_box() {

	global $post;
	$lb_url_key_val = get_post_meta($post->ID, 'lb_url_key_val', true); 
	
?>
	
	<table class="form-table editcomment">
		<tbody>
			<tr valign="top">
				<td class="first">PAGE URL:</td>
				<td><input type="text"  name="lb_url_key_val" value="<?php echo $lb_url_key_val; ?>" size="129" id="lb_url_key_val" class=""/></td>
			</tr>
		</tbody>
	</table>
	
<?php
}

// lb_show_data function called from theme file to display IFRAME on a particular page
function lb_show_data() {
	
	global $post;
	$lb_url_key_val = get_post_meta($post->ID, 'lb_url_key_val', true); 
	$str = '';
	
	if($post->ID == get_option('lb_page_id') ) {
		
		$lb_service_url = get_option('lb_service_url');
		
		$istr = '';
		$iwidth = get_option('lb_iframe_width');
		if(!empty($iwidth)) {
			$istr .= 'width="'.$iwidth.'"';
		}
		
		$iheight = get_option('lb_iframe_height');
		if(!empty($iheight)) {
			$istr .= 'height="'.$iheight.'"';
		}
		
		$iscrolling = get_option('lb_iframe_scrolling');
		if(empty($iscrolling)) {
			$iscrolling = 'yes';
		}
		$istr .= 'scrolling="'.$iscrolling.'"';
		
		$str = '<iframe '.$istr.' src="'.$lb_service_url.'"></iframe>';
	
	} else if(!empty($lb_url_key_val)) {	
		
		$str = '<iframe width="100%" height="600px" src="'.$lb_url_key_val.'"></iframe>';
		
	}
	
	return $str;
	
}

add_action('save_post', 'lb_save_post');

function lb_save_post($post_id) {
	
	  if ( $_POST['post_type'] == 'page'  ) {
	  	
	  	if(isset($_POST['lb_url_key_val']) ) {
	  		update_post_meta($post_id,'lb_url_key_val',$_POST['lb_url_key_val']);
	  	}
	  		
	  }
}

?>