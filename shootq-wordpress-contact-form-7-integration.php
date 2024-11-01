<?php
/*
Plugin Name: ShootQ for Contact Form 7
Plugin URI: http://wakeless.net/archive/2010/02/shootq-wordpress-contact-form-7-integration
Description: Submit to ShootQ from Contact Form 7
Author: Michael Gall
Version: 0.3
Author URI: http://terroir.me/
*/

function shootq_getdata($data, $name) {
	if(isset($data[$name])) return $data[$name];
	else return $data["your-$name"];
}

function shootq_send($contactForm) {
	$post = $contactForm->posted_data;
	$apiKey = get_option('shootq-api-key');
	$brand = get_option('shootq-brand-abbreviation');
	if(!$apiKey || !$brand) return;
	
	$url = "https://app.shootq.com/api/{$brand}/leads";
	$data = array();
	$data["api_key"] = $apiKey;
	$contact = array();
	if($name = shootq_getdata($post, "name")) {
		$name = explode(" ", $name);
		$contact["first_name"] = $name[0];
		$contact["last_name"] = $name[1];
		
	} else {
		$contact["first_name"] = shootq_getdata($post, "first-name");
		$contact["last_name"] = shootq_getdata($post, "last-name");
	}
	
	
	$contact["phones"] = array(array( "number" => shootq_getdata($post, "phonenumber")));
	$contact["emails"] = array(array( "email" => shootq_getdata($post, "email") ) );
	$data["contact"] = $contact;
	
	$type = shootq_getdata($post, "type");
	
	$date = shootq_getdata($post, "date");
	if(get_option("shootq-aus-dates")) {
		$date = preg_replace('#(\d+)/(\d+)/(\d+)#', '\2/\1/\3', $date);
	}
	$date = date("m/d/Y", strtotime($date));
	
	$data["event"] = array(
		"type" => $type ? $type : "Job",
		"date" => $date,
		"referred_by" => shootq_getdata($post, "referrer"),
		"remarks" => shootq_getdata($post, "subject")."\n\r\n\r".shootq_getdata($post, "message"),
	);
	
	
	$lead_json = json_encode($data);

/* send this data to ShootQ via the API */
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	
//	curl_setopt($ch, CURLOPT_HEADER, true);
//	curl_setopt($ch, CURLOPT_VERBOSE, true);
	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $lead_json);
	/* get the response from the ShootQ API */
	$response_json = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$response = json_decode($response_json);
	/* the HTTP code will be 200 if there is a success */
	if (curl_errno($ch) == 0 && $httpcode == 200) {
	//    echo "SUCCESS!\n";
	} else {
		error_log("ShootQ Error: ".curl_errno($ch).": $httpcode $response_json");
//		echo $url;
//		echo curl_errno($ch);
//		echo $reponse_json;
//	    echo "There was a problem: ".$httpcode."\n\n";
	}
	curl_close($ch);
}	
add_action("wpcf7_before_send_mail", shootq_send);


// create custom plugin settings menu
add_action('admin_menu', 'shootq_wpcf7_create_menu');

function shootq_wpcf7_create_menu() {

	//create new top-level menu
	add_menu_page('ShootQ Plugin Settings', 'ShootQ Settings', 'administrator', __FILE__, 'shootq_wpcf7_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'shootq_qpcf7_register_mysettings' );
}


function shootq_qpcf7_register_mysettings() {
	//register our settings
	register_setting( 'shootq_wpcf7-settings-group', 'shootq-api-key' );
	register_setting( 'shootq_wpcf7-settings-group', 'shootq-brand-abbreviation' );
	register_setting( 'shootq_wpcf7-settings-group', 'shootq-aus-dates' );
	
}

function shootq_wpcf7_settings_page() {
?>
<div class="wrap">
<h2>Shoot Q for Contact Form 7</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'shootq_wpcf7-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">ShootQ API Key</th>
        <td><input type="text" name="shootq-api-key" value="<?php echo get_option('shootq-api-key'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">ShootQ Brand Abbreviation</th>
        <td><input type="text" name="shootq-brand-abbreviation" value="<?php echo get_option('shootq-brand-abbreviation'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Use dd/mm/yyyy dates</th>
        
        <td><input type="hidden" name="shootq-aus-dates" value="" /><input type="checkbox" name="shootq-aus-dates" value="1" <?php if(get_option('shootq-aus-dates')) echo "checked='checked'"; ?> /></td>
        </tr>
        
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>
