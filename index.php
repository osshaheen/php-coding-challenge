<?php

$inputFile = 'sample-log.txt';
$outputFile = 'sample-output.txt';

function formatDateTime($raw)
{
    $dt = DateTime::createFromFormat('Y-m-d H:i', trim($raw));
    return $dt ? $dt->format('D, d F Y H:i:00') : '';
}

function naturalSort($array)
{
    natcasesort($array);
    return array_values($array);
}

$lines = file($inputFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$records = [];
$allIds = [];
$uniqueUserIds = [];

foreach ($lines as $line) {
    $id       = trim(substr($line, 0, 12));
    $userId   = trim(substr($line, 12, 6));
    $bytesTX  = trim(substr($line, 18, 8));
    $bytesRX  = trim(substr($line, 26, 8));
    $datetime = trim(substr($line, 34, 17));

    $formattedTX = number_format((int)$bytesTX);
    $formattedRX = number_format((int)$bytesRX);
    $formattedDT = formatDateTime($datetime);

    $records[] = "$userId|$formattedTX|$formattedRX|$formattedDT|$id";
    $allIds[] = $id;
    $uniqueUserIds[] = $userId;
}

$allIds = naturalSort($allIds);
$uniqueUserIds = array_unique($uniqueUserIds);
$uniqueUserIds = naturalSort($uniqueUserIds);

$outputContent = [];
$outputContent[] = "=== PIPE-DELIMITED LOG ===";
$outputContent = array_merge($outputContent, $records);

$outputContent[] = "";
$outputContent[] = "=== SORTED IDs ===";
$outputContent = array_merge($outputContent, $allIds);

$outputContent[] = "";
$outputContent[] = "=== UNIQUE SORTED USERIDs ===";
foreach ($uniqueUserIds as $i => $uid) {
    $outputContent[] = "[" . ($i + 1) . "] $uid";
}

file_put_contents($outputFile, implode(PHP_EOL, $outputContent));

echo "✅ Done : $outputFile" . PHP_EOL;
