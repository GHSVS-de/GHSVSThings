<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Path;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

// Ersatz für {mp3playlist}goettingen/vortraege/GO-2008HT-00/GO-2008HT-00-xml4player0{/mp3playlist}

// videoplaylist sollte analog gehen.

// Check for parent. Is outer main tag of xml.
$parentTag = 'playlist';

// Container tag of data.
$childTag = 'trackList';

$url = 'images\AudioVideo\goettingen\vortraege\GO-2008HT-00\GO-2008HT-00-xml4player0.xml';
$fileAbs = JPATH_SITE . '/' . Path::clean($url, '/');

// Disable libxml errors and allow to fetch error information as needed
libxml_use_internal_errors(true);

echo '<p>Loading: <a href="' . Uri::root() . $url . '">' . $url . '</a></p>';

@$xml = simplexml_load_file($fileAbs, null, LIBXML_NOCDATA);

if ($xml === false)
{
	echo 'Errors with file ' . $url . "\n<br>";

	foreach (libxml_get_errors() as $error)
	{
		echo $error->message . "\n<br>";
	}
	return;
}
elseif (empty($xml))
{
	echo 'Empty result with file ' . $url . "\n<br>";
	return;
}
elseif (!($xml instanceof SimpleXMLElement))
{
	echo 'Errors with file ' . $url . ". Not instencaof SimpleXMLElement.\n<br>";
	return;
}
elseif ($xml->getName() !== $parentTag)
{
	echo 'Errors with file <code>' . $url . '</code>. No parent &lt;'
		. $parentTag . "&gt; tag.\n<br>";
	return;
}
elseif (empty($xml->$childTag) || !($xml->$childTag instanceof SimpleXMLElement))
{
	echo 'Errors with file ' . $url . ". No " . $childTag . " child tag or child tag not instencaof SimpleXMLElement.\n<br>";
	return;
}
elseif (empty($xml->$childTag->track) || !($xml->$childTag->track instanceof SimpleXMLElement))
{
	echo 'Errors with file ' . $url . ". No " . $childTag . "-&gt;track child tag or child tag not instencaof SimpleXMLElement.\n<br>";
	return;
}

$trackList = [];
$i = 0;

foreach ($xml->$childTag->track as $track)
{
	unset($track->image);

	if (empty($track) || empty($track->location))
	{
		continue;
	}

	$location = new Uri($track->location);
	$track->location = ltrim($location->getPath(), '\\/');

	if (!is_file(JPATH_SITE . '/' . $track->location))
	{
		continue;
	}

	$location = implode('/', array_map('rawurlencode',
		explode('/', $track->location)));
	$track->src = Uri::root() . $location;
	$track->filename = basename($track->location);

	$preTitle = ($i + 1) . ': ';

	if (!isset($track->title) || !(trim($track->title)))
	{
		$track->title = $preTitle . $track->filename;
	}
	else
	{
		$track->title = $preTitle . $track->title;
	}

	$track->description = '';

	if (isset($track->annotation) && $annotation = trim($track->annotation))
	{
		$track->description = '<div class="trackDescription">' . $annotation
			. '</div>';
	}

	$trackList[++$i] = $track;
}

if (empty($trackList))
{
	echo 'No tracks found.';
	return;
}

$playlistId = 'mp3Playlist-' . $module->id;
$playerId = 'player-' . $playlistId;
$statusDiv = $playlistId . '-Status';
?>
<div id="<?php echo $statusDiv; ?>" class="small bg-success text-white px-3 py-1"></div>
<div>
<audio id="<?php echo $playerId; ?>" class="w-100" preload="auto" tabindex="0" controls="" >
  <source src="<?php echo $trackList[1]->src; ?>">
  Ihr Browser unterstützt leider diesen Audio-Player nicht.
</audio>
</div>

<ul id="<?php echo $playlistId; ?>" class="list-group"
	style="max-height: 250px; overflow: auto;">
<?php
	foreach ($trackList as $key => $track)
	{ 	?>
		<li class="list-group-item<?php echo $key === 1 ? ' active' : ''; ?>"
			<?php echo $key === 1 ? 'aria-current="true"' : ''; ?>
			data-mediaFile="<?php echo $track->src; ?>"
			data-mediaIndex="<?php echo ($key - 1); ?>"
			data-mediaTitle="<?php echo htmlspecialchars($track->title, ENT_QUOTES, 'utf-8'); ?>"
		>
			<b><?php echo $track->title; ?></b>
			<?php echo $track->description; ?>
		</li>
	<?php
	} ?>
</ul><!--/<?php echo $playlistId; ?>-->

<?php
HTMLHelper::_('jquery.framework');

$js = <<<JS
jQuery(document).ready(function ()
{
	init();
	function init()
	{
		const audio = jQuery('#$playerId');
		const playlist = jQuery('#$playlistId');
		const tracks = playlist.find('li.list-group-item');

		if (!tracks || !tracks.length)
		{
			return;
		}

		const headline = '<h6 class="text-underline">Momentan geladen:</h6>';

		jQuery('#$statusDiv').html(headline + jQuery(tracks[0]).html());

		const len = tracks.length - 1;

		playlist.on('click','li.list-group-item', function(e)
		{
			e.preventDefault();
			link = jQuery(this);
			run(link, audio[0], tracks, headline);
		});

		audio[0].addEventListener('ended',function(e)
		{
			currentIndex = jQuery('#$playlistId li.list-group-item.active')
				.attr('data-mediaIndex');

			if (currentIndex == len)
			{
				return;
			}
			else
			{
				currentIndex++;
			}
			link = jQuery(tracks[currentIndex]);
			//link.addClass('bg-warning');
			run(jQuery(link),audio[0], tracks, headline);
		});
	}
	function run(link, player, tracks, headline)
	{
		jQuery('#$statusDiv').html(headline + link.html());
		tracks.removeClass('active').removeAttr('aria-current');
		link.addClass('active').attr('aria-current', 'true');
		player.src = link.attr('data-mediaFile');
		player.load();
		player.play();
	}
});
JS;

Factory::getDocument()->addScriptDeclaration($js);

//echo ' 4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($js, true) . '</pre>';exit;
//$url = 'https://raw.githubusercontent.com/GHSVS-de/upadateservers/master/' . $whichFile . '-changelog.xml';



//images\AudioVideo\goettingen\vortraege\GO-2008HT-00\GO-2008HT-00-xml4player0.xml
