<?php
/*
en-GB ($mama) und de-DE ($child) Datei werden beide einzeln nach evtl. doppelten Lang-Platzhaltern durchsucht.
Wenn findet, Abbruch und Meldung zum Korrigieren.

Anschleißend:

Basierend auf dem $mama-INI-File wird das $child-File nach fehlenden Strings durchsucht.
Falls welche fehlen, werden die aus $mama mit einleitendem ";;;;;;;;;;" in $child eingesetzt.

Beide Dateien werden alfabetisch sortiert und jeweils in eine Datei *-copy.ini im selben Ordner geschrieben.
Original bleibt also unberührt.
*/
$langsPath = '/plugins/system/hyphenateghsvs/language/';
$filePart  = 'plg_system_hyphenateghsvs';
$mama      = 'en-GB';
$child     = 'de-DE';
 
$mamaFile = JPATH_SITE . $langsPath . $mama . '/' . $mama . '.' . $filePart;
 
$mamaLines   = file($mamaFile . '.ini', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$mamaStrings = parse_ini_file($mamaFile . '.ini');
 
$mamaLinesDoubleCheck = implode("", $mamaLines);
 
foreach ($mamaStrings as $key => $string)
{
    if (substr_count($mamaLinesDoubleCheck, $key . '=') > 1)
    {
        echo "DOPPELT: $mama : " . $key;
        exit;
    }
}
 
$childFile = JPATH_SITE . $langsPath . $child . '/' . $child . '.' . $filePart;
 
$childLines   = file($childFile . '.ini', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$childStrings = parse_ini_file($childFile . '.ini');
 
$childLinesDoubleCheck = implode("", $childLines);
 
foreach ($childStrings as $key => $string)
{
    if (substr_count($childLinesDoubleCheck, $key . '=') > 1)
    {
        echo "DOPPELT: $child : " . $key;
        exit;
    }
}
 
# Mama OK. Saver her.
sort($mamaLines);
file_put_contents($mamaFile . '-copy.ini', implode("\n\n", $mamaLines));
 
$collectChilds = array();
ksort($mamaStrings);
 
foreach ($mamaStrings as $key => $string)
{
    if (!isset($childStrings[$key]))
    {
        $collectChilds[] = ';;;;;;;;;;' .  $key . '="' . $string . '"';
    }
    else
    {
        $collectChilds[] = $key . '="' . $childStrings[$key] . '"';
    }
}
 
file_put_contents($childFile . '-copy.ini', implode("\n\n", $collectChilds));
 
echo ' 4654sd48sa7d98sD81s8d71dsa ' . print_r($collectChilds, true);exit;
