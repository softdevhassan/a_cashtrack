<?php
$invoice_id = intval($_GET['invoice_id'] ?? 0);
if ($invoice_id > 0) {
    update('billing_invoices', ['status' => 'failed'], ['id' => $invoice_id]);
}
set_alert('warning', 'Payment cancelled.');
redirect('/plans');
exit;
