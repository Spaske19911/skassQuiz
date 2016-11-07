<?php

header("Content-Type: text/plain");

$codes = array();
$hashes = array();
$key = "apps";

for ($i = 0; $i < 100; $i++) {
    $codes[] = $key . $i;
}

foreach ($codes as $code) {
    $hashes[] = md5($code);
}

echo "String[] codes = { ";
for ($i = 0; $i < 100; $i++) {
    echo "\"" . $codes[$i] . "\"";
    if ($i == 99) {
        echo " ";
    } else {
        echo ", ";
    }
}
echo "};";

echo "String[] hashes = { ";
for ($i = 0; $i < 100; $i++) {
    echo "\"" . $hashes[$i] . "\"";
    if ($i == 99) {
        echo " ";
    } else {
        echo ", ";
    }
}
echo "};";
