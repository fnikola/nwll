/*
* PHP code for the body of PayPal page node 6218 (user/payment/2013/fall/check)
*
*/
<?php
global $user;
if ($user->uid > 0) {

$nwll_paypal_env = variable_get('nwll_paypal_env');
$nwll_season = variable_get('nwll_season');
$nwll_year = variable_get('nwll_year');
$nwll_invoice_season = variable_get('nwll_invoice_season');

$payment = $_POST['payment'];
$cost_total = $_POST['cost_total'];

if($payment == 'C') {

  global $user;
  $nodeCheck = new stdClass(); // Create a new node object
  $nodeCheck->type = "payment"; // Or page, or whatever content type you like
  node_object_prepare($nodeCheck); // Set some default values
  // If you update an existing node instead of creating a new one,
  // comment out the three lines above and uncomment the following:
  // $nodeCheck = node_load($nid); // ...where $nid is the node id
  //$nodeCheck->title    = "";
  $nodeCheck->language = LANGUAGE_NONE; // Or e.g. 'en' if locale is enabled
  $nodeCheck->uid = $user->uid; // UID of the author of the node; or use $node->name
  $nodeCheck->field_payment_user_ref[$nodeCheck->language][]['uid'] = $user->uid;
  $nodeCheck->field_year[$nodeCheck->language][0]['value'] = $nwll_year.'-01-01 00:00:00'; //2013-00-00 00:00:00
  $nodeCheck->field_season[$nodeCheck->language][0]['value'] = $nwll_invoice_season;
  date_default_timezone_set('America/New_York');
  $nodeCheck->field_payment_date[$nodeCheck->language][0]['value'] = date('Y-m-d H:i:s', REQUEST_TIME); //REQUEST_TIME
  //drupal_set_message(date_default_timezone_get());
  $nodeCheck->field_payment_type[$nodeCheck->language][0]['value'] = "Check";
  $nodeCheck->field_payment_amount[$nodeCheck->language][0]['value'] = $cost_total;
  $nodeCheck->field_status[$nodeCheck->language][0]['value'] = 'Pending';
  $nodeCheck->field_payment_invoice[$nodeCheck->language][0]['value'] = $nwll_invoice_season.'-'.$user->uid;
  $nodeCheck->field_paypal_txn_id[$nodeCheck->language][0]['value'] = NULL;
  if($nodeCheck = node_submit($nodeCheck)) { // Prepare node for saving
    node_save($nodeCheck);
  }

  //ob_start();
  header("Location: /user/payment/last");
  //ob_end_flush(); //now the headers are sent

} else {

}
}
?>
