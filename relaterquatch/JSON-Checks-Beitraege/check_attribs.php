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

################### ATTRIBS START ###################

$empty['attribs'] = '{"article_layout":"","show_title":"","link_titles":"","show_tags":"","show_intro":"","info_block_position":"","info_block_show_title":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_associations":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_page_title":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';

$emptyAttribs = json_decode($empty['attribs']);
#echo ' 4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($emptyAttribs, true) . '</pre>';exit;

foreach ($articles as $article)
{
	if ($article->attribs === '')
	{
		echo ' Empty attriba: ' . $article->id . ': ' . $article->attribs . "\n";#exit;
		$article->attribs = $empty['attribs'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	if (strpos($article->attribs, '{&quot;') !== false)
	{
		#echo ' quot in attribs: ' . $article->id . ': ' . $article->attribs . "\n";#exit;
		echo ' quot in attribs: ' . $article->id . ': ' . "\n";#exit;

		$temp = str_replace('&quot;','"', $article->attribs);
		$temp = json_decode($temp);

		if (json_last_error())
		{
			echo ' JSON-Fehler nach str_replace und json_decode(): ' . $article->id . ': ' . $article->attribs . "\n";exit;
		}

		$newAttribs = new stdClass();

		foreach ($emptyAttribs as $attribsKey => $dummy)
		{
			// Ignore outdated attribsKey.
			$newAttribs->$attribsKey = isset($temp->$attribsKey) ? (string) $temp->$attribsKey : '';

			// Just a checker if it works like I think it should work.
			if (isset($temp->$attribsKey) && $temp->$attribsKey !== '')
			{
				# echo ' $temp->$attribsKey <pre>' . print_r($newAttribs, true) . '</pre>';exit;
			}
		}

		$article->attribs = json_encode($newAttribs);

		$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	if (strpos($article->attribs, '{') === false)
	{
		echo ' fehlende Klammer in attribs: ' . $article->id . ': ' . $article->attribs . "\n";exit;
		$article->attribs = $empty['attribs'];
		//public function updateObject($table, &$object, $key, $nulls = false)
		//$db->updateObject('#__content', $article, 'id');
	}
}

foreach ($articles as $article)
{
	$string = $article->attribs;

	json_decode($string);

	if (json_last_error())
	{
		echo ' JSON-Fehler: ' . $article->id . ': ' . $article->attribs . "\n";exit;
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
################### ATTRIBS END ###################

?>
