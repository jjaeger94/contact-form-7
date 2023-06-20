<?php
/**
 * User registration module
 * 20.06.2023
 * Jan Jäger
 * copied from https://github.com/WPPlugins/frontend-registration-contact-form-7/
 */
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
function create_user_from_registration($cfdata) {
	//$cmtagobj = new WPCF7_Shortcode( $tag );
	$post_id = sanitize_text_field($_POST['_wpcf7']);
    $cf7frr = get_post_meta($post_id, "_cf7frr_", true);

	$enable = get_post_meta($post_id,'_cf7fr_enable_registration');
	if($enable[0]!=0)
	{
		    if (!isset($cfdata->posted_data) && class_exists('WPCF7_Submission')) {
		        $submission = WPCF7_Submission::get_instance();
		        if ($submission) {
		            $formdata = $submission->get_posted_data();
		        }
		    } elseif (isset($cfdata->posted_data)) {
		        $formdata = $cfdata->posted_data;
		    } 
        $password = wp_generate_password( 12, false );

        $email = $formdata["".get_post_meta($post_id, "email", true).""];
        $firstname = $formdata["".get_post_meta($post_id, "firstname", true).""];
        $lastname = $formdata["".get_post_meta($post_id, "lastname", true).""];
        $company = $formdata["".get_post_meta($post_id, "company", true).""];
        $address = $formdata["".get_post_meta($post_id, "address", true).""];
        $city = $formdata["".get_post_meta($post_id, "city", true).""];
        $postcode = $formdata["".get_post_meta($post_id, "postcode", true).""];
        $country = $formdata["".get_post_meta($post_id, "country", true).""];

        // Construct a username from the user's name
        // $username = strtolower(str_replace(' ', '', $name));
        // $name_parts = explode(' ',$name);
        if ( !email_exists( $email ) ) 
        {
            // Find an unused username
            // $username_tocheck = $username;
            // $i = 1;
            // while ( username_exists( $username_tocheck ) ) {
            //     $username_tocheck = $username . $i++;
            // }
            // $username = $username_tocheck;
            // Create the user
            $userdata = array(
                'user_login' => $email,
                'user_pass' => $password,
                'user_email' => $email,
                'display_name' => $name,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'role' => $cf7frr
            );
            $user_id = wp_insert_user( $userdata );
            if ( !is_wp_error($user_id) ) {
                $customer = new WC_Customer( $user_id ); // Get an instance of the WC_Customer Object from user Id
                if($customer){
                    $customer->set_billing_first_name($firstname);
                    $customer->set_billing_last_name($lastname);
                    $customer->set_billing_company($company);
                    $customer->set_billing_address($address);
                    $customer->set_billing_city($city);
                    $customer->set_billing_postcode($postcode);
                    $customer->set_billing_country($country);
    
                    $customer->set_shipping_first_name($firstname);
                    $customer->set_shipping_last_name($lastname);
                    $customer->set_shipping_company($company);
                    $customer->set_shipping_address($address);
                    $customer->set_shipping_city($city);
                    $customer->set_shipping_postcode($postcode);
                    $customer->set_shipping_country($country);
                
                    $customer->save(); // Save data to database (add the user meta data)
                }
                // Email login details to user
                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
                $message = "Welcome! Your login details are as follows:" . "\r\n";
                $message .= sprintf(__('Username: %s'), $email) . "\r\n";
                $message .= sprintf(__('Password: %s'), $password) . "\r\n";
                $message .= wp_login_url() . "\r\n";
                wp_mail($email, sprintf(__('[%s] Your username and password'), $blogname), $message);
	        }
	        
	    }

	}
    return $cfdata;
}
add_action('wpcf7_before_send_mail', 'create_user_from_registration', 1, 2);
?>