<?php
/*
about-africa. J4-Vorbereitungen.
Migriere veralteteten Subtitles-Kram ($params->get('terminStartGhsvs') und
$params->get('terminEndGhsvs')) vom
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

	if (trim($params->get('terminStartGhsvs')) || trim($params->get('terminEndGhsvs')))
	{
		// geht nach #__bs3ghsvs_article: article_id, key:termin, value: {"bs3ghsvs_termin_active":1,"start":"2021-08-19","end":"2021-10-29"}

		$articleId = $article->id;
		$start = $params->get('terminStartGhsvs');
		$end = $params->get('terminEndGhsvs');
		$collector[$articleId] = $start . ':' . $end;
		$key = 'termin';
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
			. ', ' . $db->q('{"bs3ghsvs_termin_active":1,"start":"' . $start . '","end":"' . $end .  '"}');

		$query = $db->getQuery(true)
			->insert($db->qn('#__bs3ghsvs_article'))
			->columns($db->qn(['article_id', 'key', 'value']))
			->values($tuples);
		$db->setQuery($query);
		$db->execute();
	}
}
echo ' 4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($collector, true) . '</pre>';exit;
