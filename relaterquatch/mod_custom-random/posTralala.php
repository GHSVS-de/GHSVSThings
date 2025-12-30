<?php
## ANleitung: Siehe https://github.com/GHSVS-de/GHSVSThings/wiki/mod_custom%E2%80%90random-posTralala.php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;

$modId = 'mod-custom' . $module->id;

// Konfiguriere Modulposition der zu ladenden Module:
$modulePosition = $params->get('modulePosition', 'tralala');

$moduleOrdering = $params->get('moduleOrdering', 'random');

if ($params->get('backgroundimage')) {
    /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->addInlineStyle('
#' . $modId . '{background-image: url("' . Uri::root(true) . '/' . HTMLHelper::_('cleanImageURL', $params->get('backgroundimage'))->url . '");}
', ['name' => $modId]);
}
?>

<div id="<?php echo $modId; ?>" class="mod-custom custom">
		<?php
		// Get an array of modules in specified position.
		$modules = ModuleHelper::getModules($modulePosition);

		if (!empty($modules))
		{
			// random ordering?
			if ($moduleOrdering === 'random')
			{
				shuffle($modules);
			}

			$module->content = trim(ModuleHelper::renderModule($modules[0]));
		}

		echo $module->content;

		?>
</div>
