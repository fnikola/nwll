/*
* PHP code for the body of PayPal page node 1063 (user/payment/details)
*
*/
<?php
global $user;

// variable_set('nwll_paypal_env', 'sandbox');
// variable_set('nwll_season', 'Spring');
// variable_set('nwll_year', 2013);
// variable_set('nwll_invoice_season', '2013SB');
$nwll_paypal_env = variable_get('nwll_paypal_env');
$nwll_season = variable_get('nwll_season');
$nwll_year = variable_get('nwll_year');
$nwll_invoice_season = variable_get('nwll_invoice_season');

$payment_type = '';
$payment_type_paypal = '';
$payment = '';
$payment_date = '';
$payer_email = '';
$first_name = '';
$last_name = '';
$payment_status = '';
$invoice = '';
if ($nwll_paypal_env == 'sandbox') {
  $query = db_select('paypal_ipn_sandbox', 'p');
  drupal_set_message($nwll_paypal_env);
} else {
  $query = db_select('paypal_ipn', 'p');
}
$query->fields('p');
$query->condition('custom', $user->uid,'=');
$result = $query->execute();
//print '<p>'.var_dump($result->rowCount()).'</p>';
if ($result->rowCount() > 0) {
  while($record = $result->fetchAssoc()) {
    //print_r($record);
    $payment_type = "PayPal";
    $payment_type_paypal = true;
    $payment = money_format('$%i', $record["payment_gross"]);
    $payment_date = date('M d, Y h:i a', strtotime($record["payment_date"]));
    $payer_email = $record["payer_email"];
    $first_name = $record["first_name"];
    $last_name = $record["last_name"];
    $payment_status = $record["payment_status"];
    $invoice = $record["invoice"];
  }
} else {
  $query = db_select('node', 'n');
  $query->join('field_data_field_payment_invoice', 'i', 'n.nid = i.entity_id');
  $query->join('field_data_field_payment_amount', 'a', 'n.nid = a.entity_id');
  $query->join('field_data_field_payment_date', 'd', 'n.nid = d.entity_id');
  $query->join('field_data_field_status', 's', 'n.nid = s.entity_id');
  $query->fields('n');
  $query->fields('i');
  $query->fields('a', array('field_payment_amount_value'));
  $query->fields('d', array('field_payment_date_value'));
  $query->fields('s', array('field_status_value'));
  $query->condition('n.status', 1, '=');
  $query->condition('n.type', 'payment', '=');
  $query->condition('n.uid', $user->uid,'=');
  $query->condition('i.field_payment_invoice_value', '%'.$nwll_invoice_season.'%','LIKE');
  $query->orderBy('n.changed', 'DESC');
  $query->range(0, 1);
  $result2 = $query->execute();
  //print '<p>'.var_dump($result2->rowCount()).'</p>';
  if ($result2->rowCount() > 0) {
    while($record2 = $result2->fetchAssoc()) {
      //print_r($record2);
      $payment_type = "Check";
      $payment = money_format('$%i', $record2["field_payment_amount_value"]);
      $payment_date = date('M d, Y h:i a', strtotime($record2["field_payment_date_value"]));
      //$payer_email = $record2["payer_email"];
      //$first_name = $record2["first_name"];
      //$last_name = $record2["last_name"];
      $payment_status = $record2["field_status_value"];
      $invoice = $record2["field_payment_invoice_value"];
    }
  } else {
    //ob_start();
    header("Location: /user/payment/".variable_get('nwll_year')."/".strtolower(variable_get('nwll_season'))."/registration");
    //ob_end_flush(); //now the headers are sent
    //drupal_set_message("->".$result2->rowCount());
    //print_r($query->__toString());
  }
}

$children = '';
$volunteer = '';
$candy = '';
//INVOICE (i.e. 2013SB-C1-VD-CB)
$year = substr($invoice, 0, 4);
switch (substr($invoice, 4, 2)) {
  case 'SB';
    $season = 'Spring';
    break;
  case 'FB';
    $season = 'Fall';
    break;
}
switch (substr($invoice, 7, 2)) {
  case 'C1';
    $children = '1 Child';
    break;
  case 'C2';
    $children = '2 Children';
    break;
  case 'C3';
    $children = '3 Children';
    break;
  case 'C4';
    $children = '4 Children';
    break;
  case 'C5';
    $children = '5 Children';
    break;
}
switch (substr($invoice, 10, 2)) {
  case 'VD';
    $volunteer = 'Volunteer Refundable Deposit';
    break;
  case 'VB';
    $volunteer = 'Volunteer Buyout';
    break;
}
switch (substr($invoice, 13, 2)) {
  case 'CB';
    $candy = 'Candy Buyout';
    break;
  case 'CS';
    $candy = 'Candy Sell';
    break;
}
?>

<?php if ($payment_type == 'PayPal') { ?>
<p>Your payment for <b><?php print $nwll_season.' '.$nwll_year; ?> Registration</b> has been processed at <a href="http://www.paypal.com" target="new">www.paypal.com</a>.<br/>Please <a href="/contact/payment">contact us</a> if you have any questions or concerns regarding your payment.</p>
<?php } else { ?>
  <?php if ($payment_status == 'Completed') { ?>
    <p>Your payment for <b><?php print $nwll_season.' '.$nwll_year; ?> Registration</b> has been received via <b>Check</b>.<br/>Please <a href="/contact/payment">contact us</a> if you have any questions or concerns regarding your payment.</p>
  <?php } else { ?>
    <p>Your payment for <b><?php print $nwll_season.' '.$nwll_year; ?> Registration</b> is being processed via <b>Check</b>.<br/>Please <a href="/contact/payment">contact us</a> if you have any questions or concerns regarding your payment.</p>
  <?php } ?>
<?php } ?>

<p></p>
<!--<p><b style="color:#0000CD;"><?php print $nwll_season.' '.$nwll_year; ?></b></p>-->
<b>Payment:</b> <?php print $payment; ?><br/>
<b>Pay Date:</b> <?php print $payment_date; ?><br/>
<?php if ($payment_type_paypal) { ?><b>Payer Name:</b> <?php print $first_name.' '.$last_name; ?><br/><?php } ?>
<?php if ($payment_type_paypal) { ?><b>Payer Email:</b> <?php print $payer_email; ?><br/><?php } ?>
<b>Payment Status:</b> Paid by <?php print $payment_type; ?> - <?php print $payment_status; ?><br/>
<p></p>
<b>Registration Summary:</b><br/>
<?php print '&nbsp;&nbsp;'.$children.'<br/>'; ?>
<?php print '&nbsp;&nbsp;'.$volunteer.'<br/>'; ?>
<?php print '&nbsp;&nbsp;'.$candy.'<br/>'; ?>
<p>&nbsp;</p>
Thank you for your continued support and enjoy a great season!<br/>
NWLL
