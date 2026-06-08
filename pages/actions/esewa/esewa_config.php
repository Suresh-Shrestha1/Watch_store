<?php
// ============================================================
//  esewa_config.php  –  eSewa configuration + helpers
// ============================================================

require_once '../../../config/db.php';   // provides $conn

// ── Constants ────────────────────────────────────────────────
define('ESEWA_MERCHANT_ID',  'EPAYTEST');
define('ESEWA_PRODUCT_CODE', 'EPAYTEST');
define('ESEWA_SECRET_KEY',   '8gBm/:&EnhH.1/q');

define('ESEWA_PAYMENT_URL',
    'https://rc-epay.esewa.com.np/api/epay/main/v2/form');

define('ESEWA_VERIFY_URL',
    'https://rc-epay.esewa.com.np/api/epay/transaction/status/');

define('SUCCESS_URL',
    'http://localhost/watch_store/pages/actions/esewa/payment_success.php');

define('FAILURE_URL',
    'http://localhost/watch_store/pages/actions/esewa/payment_failure.php');


// ── Signature generator ──────────────────────────────────────
/**
 * Build the HMAC-SHA256 / base64 signature eSewa requires.
 *
 * Signed message format (exactly as eSewa documents):
 *   "total_amount=<amt>,transaction_uuid=<uuid>,product_code=<code>"
 *
 * @param  string|float $total_amount     Grand total (e.g. "1500.00")
 * @param  string       $transaction_uuid Your unique UUID for this payment
 * @return string                         Base64-encoded HMAC signature
 */
function generate_esewa_signature($total_amount, $transaction_uuid): string
{
    // Format amount to exactly 2 decimal places — eSewa is strict
    $formatted_amount = number_format((float)$total_amount, 2, '.', '');

    // Build message in the exact field order eSewa specifies
    $message = "total_amount={$formatted_amount}"
             . ",transaction_uuid={$transaction_uuid}"
             . ",product_code=" . ESEWA_PRODUCT_CODE;

    $hash = hash_hmac('sha256', $message, ESEWA_SECRET_KEY, true);
    return base64_encode($hash);
}