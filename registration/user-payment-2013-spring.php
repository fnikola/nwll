/*
* PHP code for the body of web form node 1057 (user/payment/2013/spring/registration)
*
*/
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
  drupal_set_message($nwll_paypal_env);
} else {
  $nwll_paypal_env_table = 'paypal_ipn'; 
}
if (db_query("SELECT txn_id FROM {".$nwll_paypal_env_table."} WHERE custom = :uid AND payment_status = :payment_status AND SUBSTR(invoice, 1, 6) = :invoice", array(':uid' => $user->uid, ':payment_status' => $payment_status_completed, ':invoice' => $nwll_invoice_season))->fetchField()) {
  //ob_start();
  header("Location: /user/payment/details");
  //ob_end_flush(); //now the headers are sent
} elseif (db_query("SELECT nid FROM {node} INNER JOIN {field_data_field_payment_invoice} ON node.nid = field_data_field_payment_invoice.entity_id WHERE node.type = 'payment' AND node.status = 1 AND node.uid = :uid AND field_data_field_payment_invoice.entity_type = 'node' AND SUBSTR(field_data_field_payment_invoice.field_payment_invoice_value, 1, 6) = :invoice", array(':uid' => $user->uid, ':invoice' => $nwll_invoice_season))->fetchField()) {
  //ob_start();
  header("Location: /user/payment/details");
  //ob_end_flush(); //now the headers are sent
  //drupal_set_message("SELECT nid FROM {node} INNER JOIN {field_data_field_payment_invoice} ON node.nid = field_data_field_payment_invoice.entity_id WHERE node.type = 'payment' AND node.uid = :uid AND field_data_field_payment_invoice.entity_type = 'node' AND SUBSTR(field_data_field_payment_invoice.field_payment_invoice_value, 1, 6) = :invoice");
}

//INVOICE (i.e. 2013SB-C1-VD-CB)
$players = db_query("SELECT count(nid) FROM {node} INNER JOIN {field_data_field_season} ON nid = entity_id WHERE status = 1 AND type = 'players' AND entity_type = 'node' AND uid = ".$user->uid." AND field_season_value = '".$nwll_invoice_season."'")->fetchField();
if ($players > 0) {

  $profile = user_load($user->uid);
  //drupal_set_message('<pre>'. check_plain(print_r($profile, TRUE)) .'</pre>');
  //drupal_set_message('<pre>'. check_plain(print_r($user, TRUE)) .'</pre>');
  /*
  if (is_object($profile->field_first_name)) {
    $welcome = 'Hi '.$profile->field_first_name['und']['0']['value'].',';
  } else {
    $welcome = 'Hello,';
  }
  */
  $welcome = 'Hi '.$profile->field_first_name['und']['0']['value'].',';
  ?>
  <p><?php print $welcome; ?></p>
  <p>You have added <?php print $players; ?> family members for your NWLL <?php print $nwll_season.' '.$nwll_year; ?> Registration:</p>
  <?php print views_embed_view('players','block_2'); ?>
  <p>If this is incorrect please <a href="/user/players">review your family</a> and make your changes.</p>
  <p style="font-weight: bold;">If this is correct, please make the following selections and click &quot;Complete Registration&quot; to finalize your registration.</p>
  <!--<hr/>-->
<?php
}
else {
  //ob_start();
  header("Location: /user/players");
  //ob_end_flush(); //now the headers are sent
  //print 'You first need to <a href="/register/player">add a player</a> before you can register and make your payment online.';
}

} else {
  print 'Please <a href="/user/login">login</a> before you can register and make your payment online.';
}
?>
