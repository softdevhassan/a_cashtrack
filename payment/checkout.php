<?php
// Bridge route to standard checkout
$plan_id = intval($_GET['plan_id'] ?? 0);
$to = '/payment/proceed-to-checkout' . ($plan_id ? ('?plan_id=' . $plan_id) : '');
redirect($to);
?>
