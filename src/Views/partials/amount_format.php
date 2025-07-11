<?php
// Usage: include and call amount_format($value)
function amount_format($val) {
    $out = rtrim(rtrim(number_format($val, 8, '.', ''), '0'), '.');
    return $out === '' ? '0.00' : $out;
}
