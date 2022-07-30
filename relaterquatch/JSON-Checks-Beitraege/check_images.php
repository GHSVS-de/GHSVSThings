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

################### IMAGES START ###################

$empty['images'] = '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}';

foreach ($articles as $article)
{
	if ($article->images === '')
	{
		echo ' Empty images: ' . $article->id . ': ' . $article->images . "\n";#exit;
		$article->images = $empty['images'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	if (strpos($article->images, '{&quot;') !== false)
	{
		echo ' quot in images: ' . $article->id . ': ' . $article->images . "\n";#exit;

		$wrong = '{&quot;image_intro&quot;:&quot;&quot;,&quot;float_intro&quot;:&quot;&quot;,&quot;image_intro_alt&quot;:&quot;&quot;,&quot;image_intro_caption&quot;:&quot;&quot;,&quot;image_fulltext&quot;:&quot;&quot;,&quot;float_fulltext&quot;:&quot;&quot;,&quot;image_fulltext_alt&quot;:&quot;&quot;,&quot;image_fulltext_caption&quot;:&quot;&quot;}';

		if ($article->images === $wrong)
		{
			$article->images = $empty['images'];
			//public function updateObject($table, &$object, $key, $nulls = false)
			$db->updateObject('#__content', $article, 'id');
		}

	}
}

foreach ($articles as $article)
{
	if (strpos($article->images, '{') === false)
	{
		echo ' fehlende Klammer: ' . $article->id . ': ' . $article->images . "\n";#exit;
		$article->images = $empty['images'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		$db->updateObject('#__content', $article, 'id');
	}
}


foreach ($articles as $article)
{
	$string = $article->images;

	json_decode($string);

	if (json_last_error())
	{
		echo ' JSON-Fehler: ' . $article->id . ': ' . $article->images . "\n";exit;
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
################### IMAGES END ###################






echo ' 4654sd48sa7d $articles <pre>' . print_r(count($articles), true) . '</pre>';exit;

return;


?>
