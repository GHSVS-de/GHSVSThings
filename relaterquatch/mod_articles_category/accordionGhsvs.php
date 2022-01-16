<?php

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

if (!$list)
{
	return;
}

Factory::getApplication()
	->getDocument()
	->getWebAssetManager()
	->useScript('bootstrap.collapse');

$id = 'accordionGhsvs-' . $module->id;
?>
<div class="accordion" id="<?php echo $id; ?>">
	<?php
	foreach ($list as $key => $item)
	{
		$itemId = $id . '-' . $key;
	?>
		<div class="accordion-item">
			<h2 class="accordion-header" id="<?php echo $itemId; ?>Header">
				<button class="accordion-button" type="button" data-bs-toggle="collapse"
					data-bs-target="#<?php echo $itemId; ?>" aria-expanded="false"
					aria-controls="<?php echo $itemId; ?>">
					<?php echo $item->title; ?>
				</button>
			</h2>
			<div id="<?php echo $itemId; ?>" class="accordion-collapse collapse"
				aria-labelledby="<?php echo $itemId; ?>Header"
				data-bs-parent="#<?php echo $id; ?>">
				<div class="accordion-body">
					<?php echo $item->introtext; ?>
				</div>
			</div><!--/accordion-collapse-->
		</div><!--/accordion-item-->
	<?php
	} ?>
</div><!--/accordion -->
