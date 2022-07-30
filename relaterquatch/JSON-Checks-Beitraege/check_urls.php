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

################### URLS START ###################

$empty['urls'] = '{"urla":false,"urlatext":"","targeta":"","urlb":false,"urlbtext":"","targetb":"","urlc":false,"urlctext":"","targetc":""}';

$emptyUrls = json_decode($empty['urls']);
#echo ' 4654sd48sa7d98sD81s8d71dsa <pre>' . print_r(json_encode($emptyUrls), true) . '</pre>';exit;

foreach ($articles as $article)
{
	if ($article->urls === '')
	{
		echo ' Empty urls: ' . $article->id . ': ' . $article->urls . "\n";#exit;
		$article->urls = $empty['urls'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	if (strpos($article->urls, '{&quot;') !== false)
	{
		echo ' quot in urls: ' . $article->id . ': ' . "\n";#exit;

		$temp = str_replace('&quot;','"', $article->urls);
		$temp = json_decode($temp);

		if (json_last_error())
		{
			echo ' JSON-Fehler nach str_replace und json_decode() in urls: ' . $article->id . ': ' . $article->urls . "\n";exit;
		}

		$newUrls = new stdClass();

		foreach ($emptyUrls as $urlsKey => $value)
		{
			$newUrls->$urlsKey = isset($temp->$urlsKey) ? $temp->$urlsKey : $value;

			// Just a checker if it works like I think it should work.
			if (isset($temp->$urlsKey) && $temp->$urlsKey)
			{
				//echo ' $temp->$urlsKey <pre>' . print_r($newUrls, true) . '</pre>';exit;
			}
		}

		$article->urls = json_encode($newUrls);

		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	if (strpos($article->urls, '{') === false)
	{
		echo ' fehlende Klammer in urls: ' . $article->id . ': ' . $article->urls . "\n";exit;
		$article->urls = $empty['urls'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		//$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	$string = $article->urls;

	json_decode($string);

	if (json_last_error())
	{
		echo ' JSON-Fehler: ' . $article->id . ': ' . $article->urls . "\n";exit;
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
################### URLS END ###################

?>
