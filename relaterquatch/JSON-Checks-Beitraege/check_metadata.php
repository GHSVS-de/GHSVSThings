<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

$db = Factory::getDbo();

$query = $db->getQuery(true);
$query->select('*')->from('#__content');
$db->setQuery($query);

$articles = $db->loadObjectList();

$collector = [];

$empty = [];

################### metadata START ###################

$empty['metadata'] = '{"robots":"","author":"","rights":"","xreference":""}';

$emptyMetadata = json_decode($empty['metadata']);
#echo ' 4654sd48sa7d98sD81s8d71dsa <pre>' . print_r(json_encode($emptyMetadata), true) . '</pre>';exit;

foreach ($articles as $article)
{
	if ($article->metadata === '')
	{
		echo ' Empty metadata: ' . $article->id . ': ' . $article->metadata . "\n";#exit;
		$article->metadata = $empty['metadata'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	if (strpos($article->metadata, '{&quot;') !== false)
	{
		echo ' quot in metadata: ' . $article->id . ': ' . "\n";#exit;

		$temp = str_replace('&quot;','"', $article->metadata);
		$temp = json_decode($temp);

		if (json_last_error())
		{
			echo ' JSON-Fehler nach str_replace und json_decode() in metadata: ' . $article->id . ': ' . $article->metadata . "\n";exit;
		}

		if (count(get_object_vars($emptyMetadata)) !== count(get_object_vars($temp)))
		{
			//echo " \nunterschiedliche Parameter-Zahl in metadata: \n<pre>" . print_r($temp, true) . '</pre>';#exit;
		}

		$newData = new stdClass();

		foreach ($emptyMetadata as $Key => $Value)
		{
			$newData->$Key = isset($temp->$Key) ? $temp->$Key : $Value;
		}

		$article->metadata = json_encode($newData);
#echo " \blubber: \n<pre>" . print_r($article->metadata, true) . '</pre>';#exit;
		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	if (strpos($article->metadata, '{') === false)
	{
		echo ' fehlende Klammer in metadata: ' . $article->id . ': ' . $article->metadata . "\n";exit;
		$article->metadata = $empty['metadata'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	$string = $article->metadata;

	json_decode($string);

	if (json_last_error())
	{
		echo ' JSON-Fehler: ' . $article->id . ': ' . $article->metadata . "\n";exit;
	}

/* 	switch (json_last_error()) {
		case JSON_ERROR_NONE:
			echo ' - No errors';
		break;
		case JSON_ERROR_DEPTH:
			echo ' - Maximum stack depth exceeded';
		break;
		case JSON_ERROR_STATE_MISMATCH:
			echo ' - Underflow or the modes mismatch';
		break;
		case JSON_ERROR_CTRL_CHAR:
			echo ' - Unexpected control character found';
		break;
		case JSON_ERROR_SYNTAX:
			echo ' - Syntax error, malformed JSON';
		break;
		case JSON_ERROR_UTF8:
			echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
		break;
		default:
			echo ' - Unknown error';
		break;
	} */
}

echo ' 4654sd48sa7d $articles <pre>' . print_r(count($articles), true) . '</pre>';exit;

return;
################### METADATA END ###################

?>
