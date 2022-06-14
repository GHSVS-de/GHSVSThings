<?php
/*
####EXTRAKT-START docBlock
- Aus Original-Sidemenü-Yaml-Datei wird:
- - Pfade zu md-Dateien ermittelt.
- - - Jeweils Frontmatter aus Datei ausgelesen.
- - - - Falls nötig, weight bzw. sortByMenu aktualisiert.
- - - - slug ausgelesen und ggf. in Sidemenü-Yaml aktualisiert.
- - Falls Änderungen, wird jeweilige md-Datei neu geschrieben.
- - Falls Änderungen, wird Original-Sidemenü-Yaml-Datei neu geschrieben.
- - Anschließend ein weiterer Schritt für Erstellung prevNext-Yaml-Datei.
####EXTRAKT-END docBlock
*/

use Spatie\YamlFrontMatter\YamlFrontMatter;
use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/vendor/autoload.php';

/*
Voreinstellung:
Eine Art Debug-Switch, ob folgender Code Dateien bei Bedarf neu speichern soll.
Damit sind md-Dateien und z.B. die Original-Menü-yaml-Datei gemeint.
*/
$doIt = true;

/*
Sammelt Log-Einträge, die abschließend in die Log-Datei eingetragen werden.
*/
$logs = [];
$logFile = __DIR__ . '/Console/prepareMenu-php_log.txt';

if ($doIt === false)
{
	$logs[] = '!!!!!$doIt === false!!!!! Es werden keine Dateien gespeichert!!!';
}

/*
Voreinstellung:
Welches Menü im Ordner PATH_DATA/sidemenu/ ist Basis für folgenden Code?
*/
$menuName = 'my-hugo';
//$menuName = 'hugo';

$baseDir = dirname(__DIR__);

/*
Ein Switch, der im folgenden Code manipuliert wird.
Ist es nötig, das aktuelle Menü-yml final neu zu schreiben?
*/
$needsRenewal = 0;

// Absoluter Pfad zum obersten Ordner mit den Content-Dateien in Unterordnern.
$contentPath = $baseDir . '/site/content/' . $menuName;

// Absoluter Pfad zum Ordner mit Menü-Definitions-Dateien.
$pathData = $baseDir . '/site/data';

// Für korrekte URLs.
$urlSafeMuster = '/(\s|[^A-Za-z0-9\-])+/';

// Ggf. neue weight: nötig. Kalkuliere mit folgenden Werten:
$weightHighest = 0;
$weightFactor = 500;

// Lese das Yaml-Menü ein. Ein multidimensionales Array mit Objekten.
$yamlFile = $pathData . '/sidemenu/' . $menuName . '.yml';
$yamlFileContent = file_get_contents($yamlFile);

// Ggf. muss das .yml-Datei neu geschrieben werden? Dafür ist $origMenu.
$menu = Yaml::parse($yamlFileContent, Yaml::PARSE_OBJECT_FOR_MAP);
$origMenu = Yaml::parse($yamlFileContent, Yaml::PARSE_OBJECT_FOR_MAP);

// Nur "Console" zum leichter anschauen, was rauskommt.
file_put_contents(
	__DIR__ . '/Console/' . $menuName . '-menu.txt',
	'4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($menu, true) . '</pre>'
);

/*
- Folgende Ziele: Finde die Content-Dateien via Array ['pages'] eines
Menüeintrags erster Ebene.
- z.B. erste Ebene (=Mama) ist gedanklich auf Dateien bezogen
/my-hugo/Shortcode/_index.md.
- - Zweite Ebene (=Child) /my-hugo/Shortcode/xyz.md.
*/

foreach ($menu as $key => $level_1)
{
	if (!empty($level_1->pages))
	{
		// Die pfad-relevanten Angaben der aktuellen ersten Ebene. path: oder title:
		$mama = !empty($level_1->path) ? $level_1->path : $level_1->title;

		/*
		- Relative URL der Mama in erster Ebene. Im Normalfall dann auf die
		*gerenderte* _index.md-Seite
		- Todo. Hier muss noch slug: berücksichtigt werden!
		- Derzeit noch nicht, da wir uns in der Mama befinden, die noch keine slug:
		kennt.
		*/

		// Yes. Remove slashes first.
		$mamaUrl = trim($menuName . '/' . strtolower($mama), '/');
		$menu[$key]->url = '/' . $mamaUrl . '/';

		/*
		- Weiter geht es mit den Menüeinträgen zweiter Ebene, also die Kinder in
		'pages' der aktuellen Mama.
		- Content-Dateipfade finden.
		*/

		$mamaPath = $contentPath . '/' . $mama;

		$logs[] = '$mama ist: ' . $mama;
		$logs[] = '$mamaUrl ist: ' . $mamaUrl;
		$logs[] = '$mamaPath ist: ' . $mamaPath;

		if (is_dir($mamaPath))
		{
			foreach ($level_1->pages as $i => $level_2)
			{
				/*
				Switch wird vom folgenden Code manipuliert, falls md-Datei neu
				geschrieben werden muss.
				*/
				$needsFmRenewal = 0;

				// Im Moment beschäftigen wir uns noch nur mit Datei-Pfaden.
				$child = (!empty($level_2->path) ? $level_2->path : $level_2->title);

				// B\C. Ist da eine Dateiendung eingegeben?
				if (($ext = strrchr($child, '.')) !== false)
				{
					// Entferne Dateiendung.
					$child = basename($child, $ext);
				}
				else
				{
					$ext = '.md';
				}

				$childFile = $mamaPath . '/' . $child . $ext;

				/*
				Derzeit bisserl redndant.
				Muss frühzeitig passieren. Ggf. unten slug zu korrigieren.
				Es gibt Einträge wie "Einleitung", die auf die Mama gehen.
				Die haben dann aber keine md-Datei.
				*/
				$childUrl = $child;
				$childUrl = preg_replace($urlSafeMuster, '-', $childUrl);
				$childUrl = trim($childUrl, '-');

				$logs[] = '$child ist: ' . $child;
				$logs[] = '$childUrl ist: ' . $childUrl;
				$logs[] = '$childFile ist: ' . $childFile;

				if (is_file($childFile))
				{
					$level_2->file = $childFile;
					$frontMatter = YamlFrontMatter::parse(file_get_contents($childFile));
					$level_2->frontMatter = $frontMatter->matter();

					/*
					Check for an alternative front-matter slug. Hugo builds Links with
					that if exists. Therefore we have to override url here, too.
					*/

					$slug = $frontMatter->slug;

					/*
					Eventuell ist in der PATH_DATA/sidemenu/xyz.yml schon ein Slug drinnen.
					Und eine unnötige Neuschreibung der xyz.yml möchte ich vermeiden.
					Also prüfen!
					*/
					if (empty($slug) && isset($level_2->slug))
					{
						unset($level_2->slug, $origMenu[$key]->pages[$i]->slug);
						$needsRenewal = 1;
					}
					elseif (!empty($slug) &&
						(empty($level_2->slug) || $level_2->slug !== $slug)
					){
						$origMenu[$key]->pages[$i]->slug = $slug;
						$level_2->slug = $slug;
						$needsRenewal = 1;
					}

					$linktitle = $frontMatter->linktitle;

					/*
					Eventuell ist in der PATH_DATA/sidemenu/xyz.yml schon ein Slug drinnen.
					Und eine unnötige Neuschreibung der xyz.yml möchte ich vermeiden.
					Also prüfen!
					*/
					if (empty($linktitle) && isset($level_2->linktitle))
					{
						unset($level_2->linktitle, $origMenu[$key]->pages[$i]->linktitle);
						$needsRenewal = 1;
					}
					elseif (!empty($linktitle) &&
						(empty($level_2->linktitle) || $level_2->linktitle !== $linktitle)
					){
						$origMenu[$key]->pages[$i]->linktitle = $linktitle;
						$level_2->linktitle = $linktitle;
						$needsRenewal = 1;
					}

					$childUrl = $slug ?: $childUrl;
					$childUrl = preg_replace($urlSafeMuster, '-', $childUrl);
					$childUrl = strtolower(trim($childUrl, '-'));

					$logs[] = '$childUrl after slug check is: ' . $childUrl;

					// For later calculation of previous/next.
					$level_2->fmTitle = $frontMatter->title ?: $level_2->title;

					// Set weight: and sortByMenu: if needed.
					$weightHighest += $weightFactor;

					// Ich weiß noch nicht, ob ich beide brauche.
					if ((int) $frontMatter->weight !== $weightHighest
						|| (int) $frontMatter->sortByMenu !== $weightHighest
					){
						$level_2->frontMatter['weight'] = $weightHighest;
						$level_2->frontMatter['sortByMenu'] = $weightHighest;

						$needsFmRenewal = 1;
					}

					if ($needsFmRenewal === 1)
					{
						$newContent = '---' . PHP_EOL;
						$newContent .= trim(Yaml::dump($level_2->frontMatter, 3, 1)) . PHP_EOL;
						$newContent .= '---' . PHP_EOL;
						$newContent .= $frontMatter->body();

						if ($doIt === true)
						{
							file_put_contents($childFile, $newContent);
							$logs[] = 'File rewritten: '
							. str_replace($contentPath, '', $childFile) . '"'
							. PHP_EOL;
						}
					}
					else
					{
						$logs[] = 'No renewal necessary: '
						. str_replace($contentPath, '', $childFile) . '"'
						. PHP_EOL;
					}
				}
				else
				{
					$logs[] = 'Child-File not found:: '
					. str_replace($contentPath, '', $childFile) . '"'
					. PHP_EOL;
				}

				// For later calculation of previous/next.
				$level_2->url = '/' . trim($mamaUrl . '/' . $childUrl, '/')
					. '/';
			}
		}
		else
		{
			$logs[] = 'Dieser $mamaPath existiert nicht: ' . $mamaPath;
		}
	}
}

// Original Menü-yaml-Datei erneuern.
if ($needsRenewal === 1)
{
	$comment = trim(
'
# This file can be edited to add new menu items.
# However, it is occasionally rewritten by a buildBefore-script..
# No basic data (title:, path:) is changed in the process.
# BUT slug:-values are manipulated by this script. Also deleted.
# # So it makes no sense to edit them in this file.
# # Use the front-matter blocks for this purpose.
# ' . date("Y-m-d H:i:s") . ' (' . basename(__FILE__) . ')');

	$dumped = Yaml::dump($origMenu, 3, 1, Yaml::DUMP_OBJECT_AS_MAP);

	if ($doIt === true)
	{
		file_put_contents($yamlFile, $comment . PHP_EOL . PHP_EOL . $dumped);
		$logs[] = PHP_EOL . 'Menü file rewritten: "' . $yamlFile;
	}
}
else
{
	$logs[] = PHP_EOL . 'Menü file NOT rewritten (no changes necessary): "'
	. $yamlFile;
}

// Nur "Console" zum leichter anschauen, was rauskommt.
/* file_put_contents(__DIR__ . '/Console/test.yaml',
		$comment . PHP_EOL . PHP_EOL . $dumped);
*/


/* file_put_contents(
	__DIR__ . '/Console/OrigMenu.txt',
	'4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($origMenu, true) . '</pre>'
); */

// Keine Ahnung, ob das was bringt.
unset($origMenu);
unset($dumped);

// Nur "Console" zum leichter anschauen, was rauskommt.
file_put_contents(
	__DIR__ . '/Console/' . $menuName . '-menu-2.txt',
	'4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($menu, true) . '</pre>'
);

// Next step. Calculate previous/next in submenu and safe in data file.
$prevNext = [];
$dumped = 'Error';
$prevNectFile = $pathData . '/prevNext/' . $menuName . '.yml';

foreach ($menu as $key => $level_1)
{
	if (!empty($level_1->pages))
	{
		$pages = $level_1->pages;

		foreach ($pages as $i => $child)
		{
			$url = $child->url;
			$prevNext[$url] = new stdClass;

			if (isset($pages[$i + 1]))
			{
				$prevNext[$url]->next = $pages[$i + 1]->url;
				$prevNext[$url]->nextTitle = !empty($pages[$i + 1]->fmTitle)
					? $pages[$i + 1]->fmTitle : $pages[$i + 1]->title;
			}

			if (isset($pages[$i - 1]))
			{
				$prevNext[$url]->prev = $pages[$i - 1]->url;
				$prevNext[$url]->prevTitle = !empty($pages[$i - 1]->fmTitle)
					? $pages[$i - 1]->fmTitle : $pages[$i - 1]->title;
			}
		}
	}
}

if ($prevNext)
{
	$comment = trim(
'
# This file has been created automazically.
# It makes no sense to edit it.
# It depends on settings in ' . str_replace($baseDir, '', $yamlFile) . PHP_EOL
. '# (' . basename(__FILE__) . ')');

	$dumped = Yaml::dump($prevNext, 3, 4, Yaml::DUMP_OBJECT_AS_MAP);

	if ($doIt === true)
	{
		file_put_contents(
			$prevNectFile,
			$comment . PHP_EOL . PHP_EOL . $dumped
		);
		$logs[] = PHP_EOL . 'Previous next file rewritten: "'
		. $prevNectFile;
	}
}
else
{
	$logs[] = PHP_EOL . 'Previous next file NOT rewritten. No data provided: '
	. $prevNectFile;
}

// Nur "Console" zum leichter anschauen, was rauskommt.
file_put_contents(
	__DIR__ . '/Console/' . $menuName . '-prevNext-dumped.txt',
	$comment . PHP_EOL . PHP_EOL . $dumped
);

// Log
file_put_contents(
	$logFile,
	implode(PHP_EOL, $logs)
);

echo 'Log written: ' . $logFile . PHP_EOL;
echo 'Ready! Good bye!' . PHP_EOL;
