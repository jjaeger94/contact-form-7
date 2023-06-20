<?php
/**
 * User registration module
 * 20.06.2023
 * Jan Jäger
 * copied from https://github.com/WPPlugins/frontend-registration-contact-form-7/
 */

/**
 * 
 * @access      public
 * @since       1.1
 * @return      $content
*/
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

require_once (dirname(__FILE__) . '/frontend-registration-opt-cf7.php');

function cf7fr_editor_panels_reg ( $panels ) {
		
		$new_page = array(
			'Error' => array(
				'title' => __( 'Registration Settings', 'contact-form-7' ),
				'callback' => 'cf7fr_admin_reg_additional_settings'
			)
		);
		
		$panels = array_merge($panels, $new_page);
		
		return $panels;
		
	}
	add_filter( 'wpcf7_editor_panels', 'cf7fr_editor_panels_reg' );

function cf7fr_admin_reg_additional_settings( $cf7 )
{
	
	$post_id = sanitize_text_field($_GET['post']);
	$tags = $cf7->form_scan_shortcode();
	$enable = get_post_meta($post_id, "_cf7fr_enable_registration", true);
	$cf7fru = get_post_meta($post_id, "_cf7fru_", true);
	$cf7fre = get_post_meta($post_id, "_cf7fre_", true);
	$cf7frr = get_post_meta($post_id, "_cf7frr_", true);
	$selectedrole = $cf7frr;
	if(!$selectedrole)
	{
		$selectedrole = 'subscriber';
	}
	if ($enable == "1") { $checked = "CHECKED"; } else { $checked = ""; }
	
	$selected = "";
	$admin_cm_output = "";
	
	$admin_cm_output .= "<div id='additional_settings-sortables' class='meta-box'><div id='additionalsettingsdiv'>";
	$admin_cm_output .= "<div class='handlediv' title='Click to toggle'><br></div><h3 class='hndle ui-sortable-handle'><span>Frontend Registration Settings</span></h3>";
	$admin_cm_output .= "<div class='inside'>";
	
	$admin_cm_output .= "<div class='mail-field'>";
	$admin_cm_output .= "<input name='enable' value='1' type='checkbox' $checked>";
	$admin_cm_output .= "<label>Enable Registration on this form</label>";
	$admin_cm_output .= "</div>";

	$admin_cm_output .= "<br /><table>";
	
	$admin_cm_output .= "<tr><td>Selected Field Name For User Name :</td></tr>";
	$admin_cm_output .= "<tr><td><select name='_cf7fru_'>";
	$admin_cm_output .= "<option value=''>Select Field</option>";
	foreach ($tags as $key => $value) {
		if($cf7fru==$value['name']){$selected='selected=selected';}else{$selected = "";}			
		$admin_cm_output .= "<option ".$selected." value='".$value['name']."'>".$value['name']."</option>";
	}
	$admin_cm_output .= "</select>";
	$admin_cm_output .= "</td></tr>";

	$admin_cm_output .= "<tr><td>Selected Field Name For Email :</td></tr>";
	$admin_cm_output .= "<tr><td><select name='_cf7fre_'>";
	$admin_cm_output .= "<option value=''>Select Field</option>";
	foreach ($tags as $key => $value) {
		if($cf7fre==$value['name']){$selected='selected=selected';}else{$selected = "";}
		$admin_cm_output .= "<option ".$selected." value='".$value['name']."'>".$value['name']."</option>";
	}
	$admin_cm_output .= "</select>";
	$admin_cm_output .= "</td></tr><tr><td>";
	$admin_cm_output .= "<input type='hidden' name='email' value='2'>";
	$admin_cm_output .= "<input type='hidden' name='post' value='$post_id'>";
	$admin_cm_output .= "</td></tr>";
	$admin_cm_output .= "<tr><td>Selected User Role:</td></tr>";
	$admin_cm_output .= "<tr><td>";
	$admin_cm_output .= "<select name='_cf7frr_'>";
	$editable_roles = get_editable_roles();
    foreach ( $editable_roles as $role => $details ) {
     $name = translate_user_role($details['name'] );
         if ( $selectedrole == $role ) // preselect specified role
             $admin_cm_output .= "<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
         else
             $admin_cm_output .= "<option value='" . esc_attr($role) . "'>$name</option>";
    }
    $admin_cm_output .="</select>";
	$admin_cm_output .= "</td></tr>";
	$admin_cm_output .="</table>";
	$admin_cm_output .= "</div>";
	$admin_cm_output .= "</div>";
	$admin_cm_output .= "</div>";

	echo $admin_cm_output;
	
}
// hook into contact form 7 admin form save
add_action('wpcf7_save_contact_form', 'cf7_save_reg_contact_form');

function cf7_save_reg_contact_form( $cf7 ) {

		$tags = $cf7->form_scan_shortcode();
	
		$post_id = sanitize_text_field($_POST['post']);
		
		if (!empty($_POST['enable'])) {
			$enable = sanitize_text_field($_POST['enable']);
			update_post_meta($post_id, "_cf7fr_enable_registration", $enable);
		} else {
			update_post_meta($post_id, "_cf7fr_enable_registration", 0);
		}

		$key = "_cf7fru_";
		$vals = sanitize_text_field($_POST[$key]);
		update_post_meta($post_id, $key, $vals);

		$key = "_cf7fre_";
		$vals = sanitize_text_field($_POST[$key]);
		update_post_meta($post_id, $key, $vals);	

		$key = "_cf7frr_";
		$vals = sanitize_text_field($_POST[$key]);
		update_post_meta($post_id, $key, $vals);	
}
?>