<?php
/*
	Plugin Name: Event Espresso - EE4 Voucher Import
	Plugin URI: http://eventespresso.com/
	Description: Allows the import of social coupons into Event Espresso 4

	Version: 0.1

	Author: Event Espresso
	Author URI: http://www.eventespresso.com

	Copyright (c) 2008-2015 Event Espresso  All Rights Reserved.

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

define("ESPRESSO_VOUCHER_IMPORT_PLUGINPATH", "/" . plugin_basename(dirname(__FILE__)) . "/");
define("ESPRESSO_VOUCHER_PLUGINFULLURL", WP_PLUGIN_URL . ESPRESSO_VOUCHER_IMPORT_PLUGINPATH);

function ee4_coupon_import_admin_page() {
	global $ee4_social_import;
	
	$ee4_social_import = add_options_page( 
		__('EE4 Soial Coupon Import', 'event_espresso'),
		__('EE4 Coupon Import', 'event_espresso'),
		'read',
		'espresso_ee4_coupon_import',
		'espresso_ee4_coupon_import'
	);
}
add_action( 'admin_menu', 'ee4_coupon_import_admin_page' );

function espresso_ee4_coupon_import() {
?>
		<h3>EE4 Voucher Import</h3>
		<ul>
			<li>
				<p>This page is for importing your voucher codes from a comma separated file (CSV) directly into the the database.</p>
				<p style=" font-weight:bold">Usage:</p>
				<ol>
					<li>I have included a template file <a href="<?php echo ESPRESSO_VOUCHER_PLUGINFULLURL; ?>vouchers.csv">here</a> that I recommend you download and use.  It is very easy to work with it in excel, just remember to save it as a csv and not excel sheet.</li>
					<li>The file name should be vouchers.csv in order for it to work.</li>
					<li>One final note, you will see that the header row, first column has a 0 while other rows have a 1.  This tells the upload to ignore rows that have the 0 identifier and only use rows with the 1.</li>
				</ol>
				<?php voucher_uploader( 1, array("csv"), 1048576, '../wp-content/uploads/espresso/'); ?>
			</li>
		</ul>		
<?php
}


/*
	uploader([int num_uploads [, arr file_types [, int file_size [, str upload_dir ]]]]);

	num_uploads = Number of uploads to handle at once.

	file_types = An array of all the file types you wish to use. The default is txt only.

	file_size = The maximum file size of EACH file. A non-number will results in using the default 1mb filesize.

	upload_dir = The directory to upload to, make sure this ends with a /
 */

function voucher_uploader($num_of_uploads = 1, $file_types_array = array("csv"), $max_file_size = 1048576, $upload_dir = "../wp-content/uploads/espresso/", $success_messages = '', $error_messages='') {

		
	
		if (!is_numeric($max_file_size)) {
				$max_file_size = 1048576;
		}
		if (!isset($_POST["submitted"])) {

			$yes_no_values = array(
				array('id' => true, 'text' => __('Yes', 'event_espresso')),
				array('id' => false, 'text' => __('No', 'event_espresso'))
			);

			$type_values = array(
				array('id' => 2, 'text' => __('Percent Discount', 'event_espresso')),
				array('id' => 3, 'text' => __('Dollar Discount', 'event_espresso'))
			);


			$scope_values = array(
				array('id' => 'Event', 'text' => __('Event', 'event_espresso')),
			);

			?>
			<form action='admin.php?page=espresso_ee4_coupon_import&action=voucher_import' method='post' enctype='multipart/form-data'>
				<table class="form-table" id="promotion-details-form">
					<tr>
						<th scope="row">
							<label for="PRC_name"><?php _e('Name', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<input type="text" class="regular-text" id="PRC_name" name="PRC_name">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRT_ID"><?php _e('Type', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<?php echo EEH_Form_Fields::select_input( 'PRT_ID', $type_values, 2 ); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRC_amount"><?php _e('Amount', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<input type="text" class="regular-text ee-numeric" id="PRC_amount" name="PRC_amount" value="100">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_uses"><?php _e('Apply Promo to ALL Scope Items', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<?php echo EEH_Form_Fields::select_input( 'PRO_global', $yes_no_values, FALSE );	?>
							<p class="description"><?php _e('If set to "Yes" then this promotion will be applied to ALL items of the Scope type selected above, without having to manually select the individual items via the "Promotion applies to..." metabox in the sidebar.', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_scope"><?php _e('Scope (applied to)', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<?php echo EEH_Form_Fields::select_input( 'PRO_scope', $scope_values, TRUE ); ?>
							<p class="description"><?php _e('This determines what type of items the promotion can be applied to (see sidebar to select items)', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_event_id"><?php _e('Event ID this Promotion applies to', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<input type="text" class="regular-text ee-numeric" id="PRO_event_id" name="PRO_event_id" value="">
							<p class="description"><?php _e('Set the ID of the promotions should apply to', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_exclusive"><?php _e('Promo Is Exclusive', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<?php
								echo EEH_Form_Fields::select_input( 'PRO_exclusive', $yes_no_values, TRUE );
							?>
							<p class="description"><?php _e('If set to "Yes" then this promotion can not be combined with any other promotions', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_uses"><?php _e('Number of Uses', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<input type="text" class="regular-text ee-numeric" id="PRO_uses" name="PRO_uses" value="1">
							<p class="description"><?php _e('per scope item (see above) - leave blank for no limit', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_start"><?php _e('Valid From', 'event_espresso'); ?></label>
						</th>
						<td class="field-column ee-date-column">
							<input type="text" data-context="start" data-container="main" data-next-field="#PRO_end" class="regular-text ee-datepicker" id="PRO_start" name="PRO_start">
							<p class="description"><?php _e('Date and time the promotion is valid from, must be in the format YYYY-MM-DD H:MM am/pm (Example - 2017-12-25 8:00 pm)', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_end"><?php _e('Valid Until', 'event_espresso'); ?></label>
						</th>
						<td class="field-column ee-date-column">
							<input type="text" data-context="end" data-container="main" data-next-field="#PRO_uses" class="regular-text ee-datepicker" id="PRO_end" name="PRO_end">
							<p class="description"><?php _e('Date and time the promotion is valid until, must be in the format YYYY-MM-DD H:MM am/pm (Example - 2017-12-25 8:00 pm)', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRC_desc"><?php _e('Banner Text / Description', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<textarea class="ee-full-textarea-inp" id="PRC_desc" name="PRC_desc"></textarea>
							<p class="description"><?php _e('This is the text that will be displayed in the Promotion Banners if they are being used (see Settings Tab) as well as anywhere that the Promotion details are listed.', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_accept_msg"><?php _e('Accepted Message', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<textarea class="ee-full-textarea-inp" id="PRO_accept_msg" name="PRO_accept_msg"></textarea>
							<p class="description"><?php _e('If using Promotion Codes, this will be shown when a code has been successfully verified and applied to a registrant\'s order.', 'event_espresso'); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="PRO_decline_msg"><?php _e('Declined Message', 'event_espresso'); ?></label>
						</th>
						<td class="field-column">
							<textarea class="ee-full-textarea-inp" id="PRO_decline_msg" name="PRO_decline_msg"></textarea>
							<p class="description"><?php _e('If using Promotion Codes, this will be shown when a code entered by a registrant can not be verified or applied to their order.', 'event_espresso'); ?></p>
						</td>
					</tr>
				</table>
					
				<p>Upload voucher CSV:</p>
				<?php
					for ($x = 0; $x < $num_of_uploads; $x++) {
							echo "<p><font color='red'>*</font><input type='file' name='file[]'>";
					}
				?>
				<input type='hidden' name='submitted' value='TRUE' id='<?php echo time(); ?>'>
				<input name='action' type='hidden' value='voucher_import' />
				<?php wp_nonce_field('espresso_voucher_import'); ?>
				<input type='hidden' name='MAX_FILE_SIZE' value='"<?php echo $max_file_size; ?>"'>
				<input class='button-primary' type='submit' value='Upload Vouchers'></p>
			</form>

		<?php
		} else {
			foreach ($_FILES["file"]["error"] as $key => $value) {
				if ($_FILES["file"]["name"][$key] != "") {
					if ($value == UPLOAD_ERR_OK) {
						$origfilename = $_FILES["file"]["name"][$key];
						$filename = explode(".", $_FILES["file"]["name"][$key]);
						$filenameext = $filename[count($filename) - 1];
						unset($filename[count($filename) - 1]);
						$filename = implode(".", $filename);
						$filename = substr($filename, 0, 15) . "." . $filenameext;
						$file_ext_allow = FALSE;
						for ($x = 0; $x < count($file_types_array); $x++) {
								if ($filenameext == $file_types_array[$x]) {
										$file_ext_allow = TRUE;
								}
						}
						if ($file_ext_allow) {
								if ($_FILES["file"]["size"][$key] < $max_file_size) {
										if (move_uploaded_file($_FILES["file"]["tmp_name"][$key], $upload_dir . $filename)) {
												$success_messages .= "<p>File uploaded successfully. - <a href='" . $upload_dir . $filename . "' target='_blank'>" . $filename . "</a></p>";
										} else {
												$error_messages .= '<p>'.$origfilename . " was not successfully uploaded</p>";
										}
								} else {
										$error_messages .= '<p>'.$origfilename . " was too big, not uploaded</p>";
								}
						} else {
								$error_messages .= '<p>'.$origfilename . " had an invalid file extension, not uploaded</p>";
						}
					} else {
						$error_messages .= '<p>'.$origfilename . " was not successfully uploaded</p>";
					}
				}
			}
		}

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'voucher_import') {

		load_vouchers_to_db( $success_messages, $error_messages );
	
	}
}

function load_vouchers_to_db( $success_messages, $error_messages ) {
			
		$retrieved_nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $retrieved_nonce, 'espresso_voucher_import' ) ) {
			//Failed Nonce check, lets get out of here.
			die( 'Failed security check, try again.' );
		}

		global $wpdb;

		$fieldseparator = ",";
		$lineseparator = "\n";
		$csvfile = "../wp-content/uploads/espresso/vouchers.csv";

		function getCSVValues($string, $separator = ",") {
				global $wpdb;
				$wpdb->show_errors();
				$elements = explode($separator, $string);
				for ($i = 0; $i < count($elements); $i++) {
						$nquotes = substr_count($elements[$i], '"');

						if ($nquotes % 2 == 1) {
								for ($j = $i + 1; $j < count($elements); $j++) {
										if (substr_count($elements[$j], '"') > 0) {
												// Put the quoted string's pieces back together again
												array_splice($elements, $i, $j - $i + 1, implode($separator, array_slice($elements, $i, $j - $i + 1)));
												break;
										}
								}
						}

						if ($nquotes > 0) {
								// Remove first and last quotes, then merge pairs of quotes
								$qstr = & $elements[$i];
								$qstr = substr_replace($qstr, '', strpos($qstr, '"'), 1);
								$qstr = substr_replace($qstr, '', strrpos($qstr, '"'), 1);
								$qstr = str_replace('""', '"', $qstr);
						}
				}

				return $elements;
		}

		if (!file_exists($csvfile)) {
			$error_messages .= '<p>File not found. Make sure you specified the correct path.</p>';
		espresso_display_voucher_import_messages( $success_messages, $error_messages );
			 exit;
		}

		$file = fopen($csvfile, "r");

		if (!$file) {
			$error_messages .= '<p>Error opening data file.</p>';
			espresso_display_voucher_import_messages( $success_messages, $error_messages );
			 exit;
		}

		$size = filesize($csvfile);

		if (!$size) {
			$error_messages .= '<p>File is empty.</p>';
			espresso_display_voucher_import_messages( $success_messages, $error_messages );
			exit;
		}

		$file = file_get_contents($csvfile);
		$dataStrings = explode("\r", $file);

		$i = 0;
		$tot_records = 0;

		$PRC_ID_variable = NULL;	 
 
		/*echo '<pre style="height:auto;border:2px solid lightblue;">' . print_r( $dataStrings, TRUE ) . '</pre><br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>';die();*/

		foreach ($dataStrings as $data) {
				++$i;

			for ($j = 0; $j < $i; ++$j) {
					$strings = getCSVValues($dataStrings[$j]);
			}
		
			//echo "<pre>".print_r($strings,true)."</pre>";
			//echo '<h4>$valid : ' . $valid . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
				 
			if (array_key_exists('0', $strings)) {
				
				//  echo "The  element is in the array<br />";
				$skip = $strings[0];
	
				if ($skip >= "1") {
																				
					//Add voucher data
					$promotion_values = array(
						'PRO_ID' => 0,
						'PRO_code' => $strings[1], 
						'PRO_scope' => !empty($_POST['PRO_scope']) ? sanitize_text_field($_POST['PRO_scope']) : 'Event',
						'PRO_start' => !empty($_POST['PRO_start']) ? sanitize_text_field($_POST['PRO_start']) : NULL,
						'PRO_end' => !empty($_POST['PRO_end']) ? sanitize_text_field($_POST['PRO_end']) : NULL,
						'PRO_global' => is_numeric($_POST['PRO_global'])  ? TRUE : FALSE,
						'PRO_exclusive' => is_numeric($_POST['PRO_exclusive'])  ? TRUE : FALSE,
						'PRO_uses' => !empty($_POST['PRO_uses']) && is_numeric($_POST['PRO_uses']) ? $_POST['PRO_uses'] : 1,
						'PRO_accept_msg' => !empty($_POST['PRO_accept_msg']) ? sanitize_text_field($_POST['PRO_accept_msg']) : '',
						'PRO_decline_msg' => !empty($_POST['PRO_decline_msg']) ? sanitize_text_field($_POST['PRO_decline_msg']) : ''
					);
					$promo_price_values = array(
						//If this is not the first coupon insert, use the PRC_ID to group all the coupons together.
						'PRC_ID' => $PRC_ID_variable != NULL ? $PRC_ID_variable : 0,
						'PRC_name' => isset($_POST['PRC_name']) ? sanitize_text_field($_POST['PRC_name']) : __('Social Coupon Promotion', 'event_espresso'),
						'PRT_ID' => !empty($_POST['PRT_ID']) && is_numeric($_POST['PRT_ID']) ? $_POST['PRT_ID'] : 2,
						'PRC_amount' => !empty($_POST['PRC_amount']) && is_numeric($_POST['PRC_amount']) ? $_POST['PRC_amount'] : 100,
						'PRC_desc' => !empty($_POST['PRC_desc']) ? sanitize_text_field($_POST['PRC_desc']) : ''
					);
					//first handle the price object
					$price = EE_Price::new_instance( $promo_price_values );

					//save price
					$price->save();

					//next handle the promotions
					$promotion = EE_Promotion::new_instance( $promotion_values, null, array( 'Y-m-d', 'g:i a' ));

					
					//new promotion so let's add the price id for the price relation
					$promotion->set( 'PRC_ID', $price->ID() );

					//save promotion
					$promotion->save();


					$pro_objects = $promotion->promotion_objects();

					//Add the promotion objects for the specific event these promotions are for.
					if( !is_numeric($_POST['PRO_global']) && !empty($_POST['PRO_event_id']) ) {
						$promotion_obj = EE_Promotion_Object::new_instance(
							array(
								'PRO_ID'   => $promotion->ID(),
								'OBJ_ID'   => $_POST['PRO_event_id'],
								'POB_type' => 'Event',
								'POB_used' => 0
							)
						);
						$promotion_obj->save();
					}

					//Store the new Price ID if this is the first coupon insert.
					$PRC_ID_variable = $PRC_ID_variable == NULL ? $price->ID() : $PRC_ID_variable;

					$tot_records++;
				}
			}
					
		}

		unlink($csvfile);
		if (!file_exists($csvfile)) {
				$success_messages .= '<p>Temporary upload file has been successfully deleted.</p>';
		}
	
	$success_messages .='<p>Added a total of '.$tot_records.' vouchers to the database.</p>';
	espresso_display_voucher_import_messages( $success_messages, $error_messages );

}

function espresso_display_voucher_import_messages( $success_messages = '', $error_messages = '' ) {
	if ($success_messages != '') {
		//showMessage( $success_messages );
		echo '<div id="message1" class="updated fade"><p>' . $success_messages . '</p></div>';
	}

	if ($error_messages != '') {
		//showMessage( $error_messages, TRUE );
		echo '<div id="message2" class="error fade fade-away"><p>' . $error_messages . '</p></div>';
	}	
}