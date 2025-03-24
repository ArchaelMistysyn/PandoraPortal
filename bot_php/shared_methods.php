<?php

function big_add($a, $b, $scale = 0) {
    $a_str = normalize_bcmath_number($a);
    $b_str = normalize_bcmath_number($b);
    return bcadd($a_str, $b_str, $scale);
}
function big_sub($a, $b, $scale = 0) {
    $a_str = normalize_bcmath_number($a);
    $b_str = normalize_bcmath_number($b);
    return bcsub($a_str, $b_str, $scale);
}
function big_div($a, $b, $scale = 10) {
    $a_str = normalize_bcmath_number($a);
    $b_str = normalize_bcmath_number($b);
    if ($b_str === '0') return '0';
    return bcdiv($a_str, $b_str, $scale);
}
function big_mul($a, $b, $scale = 0) {
    $a_str = normalize_bcmath_number($a);
    $b_str = normalize_bcmath_number($b);
    return bcmul($a_str, $b_str, $scale);
}
function big_pow($a, $b, $scale = 0) {
    $a_str = normalize_bcmath_number($a);
    $b_int = (int)$b;
    return bcpow($a_str, (string)$b_int, $scale);
}
function big_cmp($a, $b, $scale = 0) {
    $a_str = normalize_bcmath_number($a);
    $b_str = normalize_bcmath_number($b);
    return bccomp($a_str, $b_str, $scale);
}
function big_round($a, $precision = 0) {
    $a_str = normalize_bcmath_number($a);
    return big_add($a_str, '0', $precision);
}
function normalize_bcmath_number($value, $precision = 12) {
    if (is_numeric($value)) {
        if (is_float($value)) {
            return rtrim(rtrim(number_format($value, $precision, '.', ''), '0'), '.');
        }
        return number_format((float)$value, $precision, '.', '');
    }
    throw new InvalidArgumentException("Invalid numeric input for BCMath: " . print_r($value, true));
}

function number_conversion($input_number) {
    $labels = ['', 'K', 'M', 'B', 'T', 'Q', 'Qt', 'Z', 'Z+', 'Z++', 'Z+++', 
               'ZZ', 'ZZ+', 'ZZ++', 'ZZ+++', 'ZZZ', 'ZZZ+', 'ZZZ++', 'ZZZ+++'];
    // Return for small number
    if (big_cmp($input_number, '1000') < 0) {
        return (string)$input_number;
    }
    // Determine the index for scaling
    $idx = 0;
    while (big_cmp($input_number, big_pow('1000', $idx + 1)) >= 0) {
        $idx++;
    }
    // Scale down the number
    $scale = 2;
    $divisor = big_pow('1000', $idx, $scale + 2);
    $scaled = big_div($input_number, $divisor, $scale + 2);
    $truncated = big_div(big_mul($scaled, '100'), '100', $scale);
    $truncated = rtrim(rtrim($truncated, '0'), '.');
    $output = $truncated;
    // Apply label
    if ($idx > 0 && isset($labels[$idx])) {
        $output .= ' ' . $labels[$idx];
    }
    return $output;
}


?>