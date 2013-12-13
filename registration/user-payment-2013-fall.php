/*
* PHP code for the body of web form node 6165 (user/payment/2013/fall)
*
*/
<!--<p><b>Today's date:</b> <? print(Date("l F d, Y")); ?></p>-->
<?php
global $user;
if ($user->uid > 0) {

$nwll_paypal_env = variable_get('nwll_paypal_env');
$nwll_season = variable_get('nwll_season');
$nwll_year = variable_get('nwll_year');
$nwll_invoice_season = variable_get('nwll_invoice_season');

// REDIRECT to payment details page if payment already made for this user
$payment_status_completed = 'Completed';
if ($nwll_paypal_env == 'sandbox') {
  $nwll_paypal_env_table = 'paypal_ipn_sandbox';
  $nwll_paypal_form_action = '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">';
  $nwll_paypal_form_hidden = '<input name="currency_code" type="hidden" value="USD" /> <input alt="PayPal - The safer, easier way to pay online!" border="0" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" type="image" /> <img alt="" border="0" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" /><input name="image_url" type="hidden" value="http://www.northwalllittleleague.com/sites/default/files/nwll-banner-2-50.png" /> <input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="3VQANNVBYECGL" />';
  $current_timestamp = time();
  $nwll_paypal_inv = '<input name="invoice" type="hidden" value="2013FB-'.$user->uid.'-'.$current_timestamp.'" />';
  $nwll_check_form = '<form action="http://www.northwalllittleleague.com/user/payment/'.variable_get('nwll_year').'/'.strtolower(variable_get('nwll_season')).'/check" method="post">';
  drupal_set_message($nwll_paypal_env);
} else {
  $nwll_paypal_env_table = 'paypal_ipn'; 
  $nwll_paypal_form_action = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
  $nwll_paypal_form_hidden = '<input name="currency_code" type="hidden" value="USD" /> <input alt="PayPal - The safer, easier way to pay online!" border="0" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" type="image" /> <img alt="" border="0" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" /><input name="image_url" type="hidden" value="http://www.northwalllittleleague.com/sites/default/files/nwll-banner-2-50.png" /> <input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="DA4S6CHSAVAAL" />';
  $nwll_paypal_inv = '<input name="invoice" type="hidden" value="2013FB-'.$user->uid.'" />';
  $nwll_check_form = '<form action="http://www.northwalllittleleague.com/user/payment/'.variable_get('nwll_year').'/'.strtolower(variable_get('nwll_season')).'/check" method="post">';
}
if (db_query("SELECT txn_id FROM {".$nwll_paypal_env_table."} WHERE custom = :uid AND payment_status = :payment_status AND SUBSTR(invoice, 1, 6) = :invoice", array(':uid' => $user->uid, ':payment_status' => $payment_status_completed, ':invoice' => $nwll_invoice_season))->fetchField()) {
  //ob_start();
  header("Location: /user/payment/last");
  //ob_end_flush(); //now the headers are sent
} elseif (db_query("SELECT nid FROM {node} INNER JOIN {field_data_field_payment_invoice} ON node.nid = field_data_field_payment_invoice.entity_id WHERE node.type = 'payment' AND node.status = 1 AND node.uid = :uid AND field_data_field_payment_invoice.entity_type = 'node' AND SUBSTR(field_data_field_payment_invoice.field_payment_invoice_value, 1, 6) = :invoice", array(':uid' => $user->uid, ':invoice' => $nwll_invoice_season))->fetchField()) {
  //ob_start();
  header("Location: /user/payment/last");
  //ob_end_flush(); //now the headers are sent
  //drupal_set_message("SELECT nid FROM {node} INNER JOIN {field_data_field_payment_invoice} ON node.nid = field_data_field_payment_invoice.entity_id WHERE node.type = 'payment' AND node.uid = :uid AND field_data_field_payment_invoice.entity_type = 'node' AND SUBSTR(field_data_field_payment_invoice.field_payment_invoice_value, 1, 6) = :invoice");
}

$players = db_query("SELECT count(nid) FROM {node} INNER JOIN {field_data_field_season} ON node.nid = field_data_field_season.entity_id WHERE node.type = 'players' AND node.status = 1 AND node.uid = ".$user->uid." AND field_data_field_season.entity_type = 'node' AND field_data_field_season.field_season_value = '".variable_get('nwll_invoice_season')."'")->fetchField();
if ($players > 0) {
?>

<!--
<p>Please click on the appropriate button below to process your online payment.</p>
<p>Thank you!</p>
<p>&nbsp;</p>
-->
<!--
<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
-->
<?php
print $nwll_paypal_form_action;
?>
	<table border="1">
		<tbody>
			<tr>
				<td colspan="2">
					<input name="on0" type="hidden" value="Please click on the &quot;Pay Now&quot; button below to process payment" />We accept payments via online through PayPal or by sending a check to our mailing address (see right side). Please click on the appropriate payment method below to make your payment and finalize your registration. Thank you and enjoy another great season!</td>
			</tr>
			<tr>
				<td colspan="2">
					<select name="os0"><option value="2013 Fall Ball Registration - 1 Family">2013 Fall Ball Registration - 1 Family $50.00 USD</option></select></td>
			</tr>
			<tr>
				<td>
					Click the &quot;<b>Pay Now</b>&quot; button below to process your payment online through PayPal.<br/>
					<?php 
					print $nwll_paypal_form_hidden; 
					global $user;
					$profile = user_load($user->uid);
					print '<input name="first_name" type="hidden" value="'.$profile->field_first_name['und'][0]['value'].'" />';
					print '<input name="last_name" type="hidden" value="'.$profile->field_last_name['und'][0]['value'].'" />';
					print '<input name="address1" type="hidden" value="'.$profile->field_address_1['und'][0]['value'].'" />';
					if (is_object($profile->field_address_2)) {
					  print '<input name="address2" type="hidden" value="'.$profile->field_address_2['und'][0]['value'].'" />';
					}
					print '<input name="city" type="hidden" value="'.$profile->field_address_city['und'][0]['value'].'" />';
					print '<input name="state" type="hidden" value="'.$profile->field_address_state['und'][0]['value'].'" />';
					print '<input name="zip" type="hidden" value="'.$profile->field_address_zip['und'][0]['value'].'" />';
					print '<input name="email" type="hidden" value="'.$user->mail.'" />';
					print '<input name="custom" type="hidden" value="'.$user->uid.'" />';
					print $nwll_paypal_inv;
					?>
					</form>
				</td>
				<td>
					<?php print $nwll_check_form; ?>
					<input name="payment" type="hidden" value="C" />
					<input name="cost_total" type="hidden" value="50" />
					Click the &quot;<b>Pay by Check</b>&quot; button below to send in a check for your payment.<br/>
					<?php 
					print $nwll_paypal_inv;
					?>
					<input alt="Pay by Check" border="0" name="submit" src="/sites/default/files/nwll-pay-by-check-100.jpg" type="image" />
					</form>
				</td>
			</tr>
		</tbody>
	</table>
<?php
}
else {
  print 'You first need to <a href="/user/players">add a player</a> for the upcoming season before you can make the payment.';
}

} else {
  print 'Please <a href="/user/login">login</a> before you can make a payment.';
}
?>
