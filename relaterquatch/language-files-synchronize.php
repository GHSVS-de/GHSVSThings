<?php
/*
https://github.com/GHSVS-de/GHSVSThings/blob/master/relaterquatch/language-files-synchronize.php

en-GB ($mama) und de-DE ($child) Datei werden beide einzeln nach evtl. doppelten Lang-Platzhaltern durchsucht.
Wenn findet, Abbruch und Meldung zum Korrigieren.

Anschleißend:

Basierend auf dem $mama-INI-File wird das $child-File nach fehlenden Strings durchsucht.
Falls welche fehlen, werden die aus $mama mit einleitendem ";;NOT-FOUND-IN-en;;" in $child eingesetzt.

Falls mutmaßlich nicht übersetzte Strings gefunden werden (= in beiden Dateien identisch), gibt das Script diese abschließend aus.

Beide Dateien werden alfabetisch sortiert und jeweils in eine Datei mit Suffix $copiedFileEnding im selben Ordner geschrieben.
Original bleibt also unberührt, falls $copiedFileEnding nicht leergelassen wird.
*/
$langsPath = '/plugins/system/bs3ghsvs/language/';
$filePart  = 'plg_system_bs3ghsvs';
$mama      = 'en-GB';
$child     = 'de-DE';
$sepa = '|-[x::SEPA-CHECKER::]-|';
$outputLineEndings = "\n\n";
$copiedFileEnding = '-copy';

$mamaFile = $langsPath . $mama . '/' . $mama . '.' . $filePart;
$mamaFileAbs = JPATH_SITE . $mamaFile;

$mamaLines   = file($mamaFileAbs . '.ini', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$mamaStrings = parse_ini_file($mamaFileAbs . '.ini');

$mamaLinesDoubleCheck = $sepa . implode($sepa, $mamaLines);

foreach ($mamaStrings as $key => $string)
{
	if (substr_count($mamaLinesDoubleCheck, $sepa . $key . '=') > 1)
	{
		echo "DOPPELTER key in $mamaFile : " . PHP_EOL . $key . PHP_EOL . PHP_EOL;
		exit;
	}
}

$childFile = $langsPath . $child . '/' . $child . '.' . $filePart;
$childFileAbs = JPATH_SITE . $childFile;
 
$childLines   = file($childFileAbs . '.ini', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$childStrings = parse_ini_file($childFileAbs . '.ini');
 
$childLinesDoubleCheck = $sepa . implode($sepa, $childLines);
 
foreach ($childStrings as $key => $string)
{
	if (substr_count($childLinesDoubleCheck, $sepa . $key . '=') > 1)
	{
		echo "DOPPELT in $childFile : " . PHP_EOL . $key . PHP_EOL . PHP_EOL;
		exit;
	}
}
 
# Mama OK. Saver her.
sort($mamaLines);
file_put_contents($mamaFileAbs . $copiedFileEnding . '.ini', implode($outputLineEndings, $mamaLines));

$collectChilds = array();
ksort($mamaStrings);

foreach ($mamaStrings as $key => $string)
{
	$mamaStrings[$key] = str_replace('"', '_QQ_', $string);
}

foreach ($childStrings as $key => $string)
{
	$childStrings[$key] = str_replace('"', '_QQ_', $string);
}

$collectIdenticals = array();

foreach ($mamaStrings as $key => $string)
{
	if (!isset($childStrings[$key]))
	{
		$collectChilds[] = ';;NOT-FOUND-IN-en;;' .  $key . '="' . str_replace('_QQ_', '\"', $string) . '"';
	}
	else
	{
		if ($string === $childStrings[$key])
		{
			$collectIdenticals[$key] = htmlspecialchars(str_replace('_QQ_', '\"', $string));
		}

		$collectChilds[] = $key . '="' . str_replace('_QQ_', '\"', $childStrings[$key]) . '"';
	}
}

file_put_contents($childFileAbs . $copiedFileEnding . '.ini', implode($outputLineEndings, $collectChilds));

echo '<h1>Bin durch, aber Folgende eventuell noch nicht übersetzt? In beiden Dateien gleich.</h1>' . PHP_EOL;
echo '<pre>' . print_r($collectIdenticals, true) . '</pre>' . PHP_EOL;
