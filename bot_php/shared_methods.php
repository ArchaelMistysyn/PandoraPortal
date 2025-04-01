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
function big_div($a, $b, $scale = 0) {
    $a_str = normalize_bcmath_number($a);
    $b_str = normalize_bcmath_number($b);
    if ($b_str === '0') return '0';
    return bcdiv($a_str, $b_str, $scale);
}
function big_truncate_div($a, $b, $scale) {
    $div = bcdiv($a, $b, $scale + 5);
    if (strpos($div, '.') === false) return $div;
    [$int, $dec] = explode('.', $div);
    $dec = substr($dec, 0, $scale);
    return rtrim($int . ($dec ? '.' . $dec : ''), '.');
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
function normalize_bcmath_number($value, $precision = 5) {
    if (is_float($value)) {
        return number_format($value, 99, '.', '');
    }
    return (string)$value;
}
function str_strip_decimal($a) {
    return explode('.', (string)$a)[0];
}
function big_rand($min, $max) {
    $range = big_add(big_sub($max, $min), '1');
    $bytes = (int) ceil(strlen($range) * log(10) / 8);
    do {
        $num = '0';
        $bin = random_bytes($bytes);
        foreach (str_split($bin) as $char) {
            $num = bcmul($num, '256');
            $num = bcadd($num, (string) ord($char));
        }
    } while (bccomp($num, $range) >= 0);

    return bcadd($min, $num);
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
    $divisor = big_pow('1000', $idx, $scale + 10);
    $scaled = big_truncate_div($input_number, $divisor, $scale + 10);
    $parts = explode('.', $scaled);
    $decimal = isset($parts[1]) ? substr($parts[1], 0, $scale) : '';
    $truncated = $parts[0] . ($decimal !== '' ? '.' . $decimal : '');
    $truncated = rtrim(rtrim($truncated, '0'), '.');
    $output = $truncated;
    // Apply label
    if ($idx > 0 && isset($labels[$idx])) {
        $output .= ' ' . $labels[$idx];
    }
    return $output;
}


?>