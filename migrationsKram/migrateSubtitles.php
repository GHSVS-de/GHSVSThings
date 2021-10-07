<?php
/*
about-africa. J4-Vorbereitungen.
Migriere veralteteten Subtitles-Kram ($params->get('articlesubtitle1') vom
mittlerweile hart entrümpelten Plugin plg_system_articlesubtitleghsvs
nach #__bs3ghsvs_article vom Plugin plg_system_bs3ghsvs_bs5.
*/
defined('_JEXEC') or die;

return;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

$db = Factory::getDbo();

$query = $db->getQuery(true);
$query->select('*')->from('#__content');
$db->setQuery($query);

$articles = $db->loadObjectList();

$collector = [];

foreach ($articles as $article)
{
	$params = new Registry($article->attribs);

	if (trim($params->get('articlesubtitle1')))
	{
		// geht nach #__bs3ghsvs_article: article_id, key:various, value: {"bs3ghsvs_various_active":1,"articlesubtitle":"Internetseiten, Webanwendungen, Planung, Entwicklung, Realisierung","articleStatus":0}
		// $collector[$article->id . ':' . $article->title] = $params->get('articlesubtitle1');

		$articleId = $article->id;
		$articlesubtitle = $params->get('articlesubtitle1');
		$collector[] = $articlesubtitle;
		$key = 'various';
		$tuples = [];

		// Delete all old rows in db table.
		$query = $db->getQuery(true)
			->delete($db->qn('#__bs3ghsvs_article'))
			->where($db->qn('article_id') . ' = ' . $db->q($articleId))
			->where($db->qn('key') . ' = ' . $db->q($key))
			;
		$db->setQuery($query);
		$db->execute();

		$tuples[] = $articleId
			. ', ' . $db->q($key)
			. ', ' . $db->q('{"bs3ghsvs_various_active":1,"articlesubtitle":"' . $articlesubtitle . '","articleStatus":0}');

		$query = $db->getQuery(true)
			->insert($db->qn('#__bs3ghsvs_article'))
			->columns($db->qn(['article_id', 'key', 'value']))
			->values($tuples);
		$db->setQuery($query);
		$db->execute();
	}
}
