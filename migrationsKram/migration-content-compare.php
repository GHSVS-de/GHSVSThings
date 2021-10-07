<?php
/*
Artikel der Produktiv-Seite mit Spiegel-Seite abgleichen.
Spiegel ist die Seite auf der dieses Script läuft.
Führt keine Änderungen durch.
*/

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

######
return;
######

$nl = '<br>' . PHP_EOL;

$optionsProduktiv = array(
	'driver' => 'mysqli',
	'host' => 'localhost',
	'user' => '',
	'password' => '',
	'database' => '',
	'prefix' => 'kloz_'
);

$dbSpiegel = Factory::getDbo();
$dbProduktiv = \JDatabaseDriver::getInstance($optionsProduktiv);

try
{
	$tablesProduktiv = $dbProduktiv->getTableList();
	$tablesSpiegel = $dbSpiegel->getTableList();
	#echo ' 4654sd48sa7d98sD81s8d71dsa $tablesProduktiv <pre>' . print_r($tablesProduktiv, true) . '</pre>';#exit;
	#echo ' 4654sd48sa7d98sD81s8d71dsa $tablesSpiegel <pre>' . print_r($tablesSpiegel, true) . '</pre>';exit;
}
catch (Exception $e)
{
	echo 'DB_NOT_CONNECTABLE';
	return;
}

$query = $dbProduktiv->getQuery(true)->select('*')->from('#__content');
$dbProduktiv->setQuery($query);
$articlesProduktiv = $dbProduktiv->loadObjectList('id');

# echo ' 4654sd48sa7d98s $articlesProduktiv <pre>' . print_r($articlesProduktiv, true) . '</pre>';exit;

$query = $dbSpiegel->getQuery(true)->select('*')->from('#__content');
$dbSpiegel->setQuery($query);
$articlesSpiegel= $dbSpiegel->loadObjectList('id');

# echo ' 4654sd48sa7d98s $articlesProduktiv <pre>' . print_r($articlesSpiegel, true) . '</pre>';exit;

$countDiff = count($articlesProduktiv) - count($articlesSpiegel);

echo ' 4654sd48sa7d98s Anzahl Produktiv - Spiegel: <pre>' . print_r($countDiff, true) . '</pre>' . $nl;exit;

$fehlendeGefunden = false;

foreach ($articlesProduktiv as $id => $articleProduktiv)
{
	if (!isset($articlesSpiegel[$id]))
	{
		$fehlendeGefunden = true;
		echo ' 4654sd48sa7d98s Fehlender Artikel in Spiegel: <pre>' . print_r($id . ': Titel: ' . $articleProduktiv->title, true) . '</pre>' . $nl;
	}
}

if ($fehlendeGefunden)
{
	echo 'Verwende J2XML zum Kopieren! Vergesse Verzeichnisschutz nicht!' . $nl;
	echo 'Vergesse danach nicht: Tabelle kloz_bs3ghsvs_article!' . $nl;
	echo 'Vergesse danach nicht: Tabelle kloz_autorbeschreibungghsvs_content_map!' . $nl;
	echo 'Vergesse danach nicht: Tabelle kloz_contact_details!' . $nl;
}
else
{
	echo 'Der ID-Vergleich (pures exists?) ergab keine Unterschiede!' . $nl;
}

##### exit;

$unterschiedGefunden = false;

foreach ($articlesProduktiv as $id => $articleProduktiv)
{
	if ($articleProduktiv->introtext !== $articlesSpiegel[$id]->introtext)
	{
		$unterschiedGefunden = true;
		echo ' 4654sd48sa7d98s Unterschied in Artikel-INTRO-Texten: <pre>' . print_r($id . ': Titel: ' . $articleProduktiv->title, true) . '</pre>' . $nl;
	}
}

if ($unterschiedGefunden)
{
	echo $nl . $nl . $nl;
	echo 'Es wurden Introtext-Unterschiede gefunden!' . $nl;
	echo 'Verwende J2XML zum Kopieren oder händisch! Achte auf die Richtung!! Vergesse Verzeichnisschutz nicht!' . $nl;
	echo '!!!!LEIDER MUSS JEDER ARTIKEL IM BACKEND IM JCE GEÖFFNET WERDEN UND NEU GESPEICHERT, UM LETZTE UNTERSCHIEDE ZU ENTFERNEN!!!!' . $nl;
}
else
{
	echo 'Der INTRO-Text-Vergleich ergab keine Unterschiede!' . $nl;
}

#exit;

// fulltext starts
$unterschiedGefunden = false;

foreach ($articlesProduktiv as $id => $articleProduktiv)
{
	if ($articleProduktiv->fulltext !== $articlesSpiegel[$id]->fulltext)
	{
		$unterschiedGefunden = true;
		echo ' 4654sd48sa7d98s Unterschied in Artikel-FULL-Texten: <pre>' . print_r($id . ': Titel: ' . $articleProduktiv->title, true) . '</pre>' . $nl;
	}
}

if ($unterschiedGefunden)
{
	echo $nl . $nl . $nl;
	echo 'Es wurden FULL-Text-Unterschiede gefunden!' . $nl;
	echo 'OK ist aber id:132.' . $nl;
	echo 'Verwende J2XML zum Kopieren oder händisch! Achte auf die Richtung!! Vergesse Verzeichnisschutz nicht!' . $nl;
	echo '!!!!Zu J2XML: LEIDER MUSS JEDER ARTIKEL IM BACKEND IM JCE GEÖFFNET WERDEN UND NEU GESPEICHERT, UM LETZTE UNTERSCHIEDE ZU ENTFERNEN!!!!' . $nl;
}
else
{
	echo 'Der INTRO-Text-Vergleich ergab keine Unterschiede!' . $nl;
}

#exit;

// images starts
$unterschiedGefunden = false;

foreach ($articlesProduktiv as $id => $articleProduktiv)
{
	if ($articleProduktiv->images !== $articlesSpiegel[$id]->images)
	{
		$unterschiedGefunden = true;
		echo ' 4654sd48sa7d98s Unterschied in Artikel-images: <pre>' . print_r($id . ': Titel: ' . $articleProduktiv->title, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articleProduktiv->images: <pre>' . print_r($articleProduktiv->images, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articlesSpiegel[$id]->images: <pre>' . print_r($articlesSpiegel[$id]->images, true) . '</pre>' . $nl;
	}
}

if ($unterschiedGefunden)
{
	echo $nl . $nl . $nl;
	echo 'Es wurden images-Unterschiede gefunden!' . $nl;
	echo 'Händisch prüfen oder mit j2xml, falls veraltetes Zeugs auf altem Plugin! Achte auf die Richtung!! Vergesse Verzeichnisschutz nicht!' . $nl;
	echo '!!!!Zu J2XML: LEIDER MUSS JEDER ARTIKEL IM BACKEND IM JCE GEÖFFNET WERDEN UND NEU GESPEICHERT, UM LETZTE UNTERSCHIEDE ZU ENTFERNEN!!!!' . $nl;
}
else
{
	echo 'Der images-Vergleich ergab keine Unterschiede!' . $nl;
}

#exit;

// params starts
$unterschiedGefunden = false;

foreach ($articlesProduktiv as $id => $articleProduktiv)
{
	if ($articleProduktiv->params !== $articlesSpiegel[$id]->params)
	{
		$unterschiedGefunden = true;
		echo ' 4654sd48sa7d98s Unterschied in Artikel-params: <pre>' . print_r($id . ': Titel: ' . $articleProduktiv->title, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articleProduktiv->params: <pre>' . print_r($articleProduktiv->params, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articlesSpiegel[$id]->params: <pre>' . print_r($articlesSpiegel[$id]->params, true) . '</pre>' . $nl;
	}
}

if ($unterschiedGefunden)
{
	echo $nl . $nl . $nl;
	echo 'Es wurden params-Unterschiede gefunden!' . $nl;
	#echo 'Händisch prüfen! Achte auf die Richtung!! Vergesse Verzeichnisschutz nicht!' . $nl;
	#echo '!!!!Zu J2XML: LEIDER MUSS JEDER ARTIKEL IM BACKEND IM JCE GEÖFFNET WERDEN UND NEU GESPEICHERT, UM LETZTE UNTERSCHIEDE ZU ENTFERNEN!!!!' . $nl;
}
else
{
	echo 'Der params-Vergleich ergab keine Unterschiede!' . $nl;
}

#exit;

// urls starts
$unterschiedGefunden = false;

foreach ($articlesProduktiv as $id => $articleProduktiv)
{
	if ($articleProduktiv->urls !== $articlesSpiegel[$id]->urls)
	{
		$unterschiedGefunden = true;
		echo ' 4654sd48sa7d98s Unterschied in Artikel-urls: <pre>' . print_r($id . ': Titel: ' . $articleProduktiv->title, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articleProduktiv->urls: <pre>' . print_r($articleProduktiv->urls, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articlesSpiegel[$id]->urls: <pre>' . print_r($articlesSpiegel[$id]->urls, true) . '</pre>' . $nl;
	}
}

if ($unterschiedGefunden)
{
	echo $nl . $nl . $nl;
	echo 'Es wurden urls-Unterschiede gefunden!' . $nl;
	#echo 'Händisch prüfen! Achte auf die Richtung!! Vergesse Verzeichnisschutz nicht!' . $nl;
	#echo '!!!!Zu J2XML: LEIDER MUSS JEDER ARTIKEL IM BACKEND IM JCE GEÖFFNET WERDEN UND NEU GESPEICHERT, UM LETZTE UNTERSCHIEDE ZU ENTFERNEN!!!!' . $nl;
}
else
{
	echo 'Der urls-Vergleich ergab keine Unterschiede!' . $nl;
}

#exit;

// catid starts
$unterschiedGefunden = false;

foreach ($articlesProduktiv as $id => $articleProduktiv)
{
	if ($articleProduktiv->catid !== $articlesSpiegel[$id]->catid)
	{
		$unterschiedGefunden = true;
		echo ' 4654sd48sa7d98s Unterschied in Artikel-catid: <pre>' . print_r($id . ': Titel: ' . $articleProduktiv->title, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articleProduktiv->catid: <pre>' . print_r($articleProduktiv->catid, true) . '</pre>' . $nl;
		echo ' 4654sd48sa7d98s $articlesSpiegel[$id]->catid: <pre>' . print_r($articlesSpiegel[$id]->catid, true) . '</pre>' . $nl;
	}
}

if ($unterschiedGefunden)
{
	echo $nl . $nl . $nl;
	echo 'Es wurden catid-Unterschiede gefunden!' . $nl;
	echo 'Händisch prüfen! VDFAK-Artikel sind aber OK, da neue Kategorie eingerichtet, andere archiviert!' . $nl;
	#echo '!!!!Zu J2XML: LEIDER MUSS JEDER ARTIKEL IM BACKEND IM JCE GEÖFFNET WERDEN UND NEU GESPEICHERT, UM LETZTE UNTERSCHIEDE ZU ENTFERNEN!!!!' . $nl;
}
else
{
	echo 'Der catid-Vergleich ergab keine Unterschiede!' . $nl;
}

exit;
