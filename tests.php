<?php

/* $mac = crc32("5d:ca:7w:o0");
$program = crc32("creator");
$version = crc32("v1");
echo $program . '-' .  $mac . '-' . $version; */
        $mac = substr(md5('5d:ca:7w:o5'), 0, 10);
        $program = substr(md5('creator'), 0, 10);
        $version = substr(md5('v1'), 0, 10);
        $full =  $mac . '-' . $program . '-' . $version;

//echo $full;
$mm =  md5($full);
$parts = str_split($mm, 8);
$final = implode("-", $parts);