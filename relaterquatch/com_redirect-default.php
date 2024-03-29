<?php
/*
- Zeige Kommentare in weiterer Spalte.
- Kopiere nach /administrator/templates/isis/html/com_redirect/links/default.php
- Joomla 3.10
- Quelle: https://github.com/GHSVS-de/GHSVSThings/blob/master/relaterquatch/com_redirect-default.php
- Quelle: https://forum.joomla.de/thread/16090-komponente-umleitung-kommentar-in-%C3%BCbersicht-anzeigen
2022-06-09
*/
/**
 * @package     Joomla.Administrator
 * @subpackage  com_redirect
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_redirect&view=links'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		<?php if ($this->redirectPluginId) : ?>
			<?php $link = JRoute::_('index.php?option=com_plugins&client_id=0&task=plugin.edit&extension_id=' . $this->redirectPluginId . '&tmpl=component&layout=modal'); ?>
			<?php echo JHtml::_(
				'bootstrap.renderModal',
				'plugin' . $this->redirectPluginId . 'Modal',
				array(
					'url'         => $link,
					'title'       => JText::_('COM_REDIRECT_EDIT_PLUGIN_SETTINGS'),
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => '70',
					'modalWidth'  => '80',
					'closeButton' => false,
					'backdrop'    => 'static',
					'keyboard'    => false,
					'footer'      => '<button type="button" class="btn" data-dismiss="modal"'
						. ' onclick="jQuery(\'#plugin' . $this->redirectPluginId . 'Modal iframe\').contents().find(\'#closeBtn\').click();">'
						. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
						. '<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="jQuery(\'#plugin' . $this->redirectPluginId . 'Modal iframe\').contents().find(\'#saveBtn\').click();">'
						. JText::_("JSAVE") . '</button>'
						. '<button type="button" class="btn btn-success" onclick="jQuery(\'#plugin' . $this->redirectPluginId . 'Modal iframe\').contents().find(\'#applyBtn\').click(); return false;">'
						. JText::_("JAPPLY") . '</button>'
				)
			); ?>
		<?php endif; ?>

		<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		<?php else :
		#echo ' 4654sd48sa7d98sD81s8d71dsa <pre>' . print_r($this->items, true) . '</pre>';exit;
		?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%" class="center nowrap">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="center nowrap">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap title">
							<?php echo JHtml::_('searchtools.sort', 'COM_REDIRECT_HEADING_OLD_URL', 'a.old_url', $listDirn, $listOrder); ?>
						</th>
						<th width="30%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_REDIRECT_HEADING_NEW_URL', 'a.new_url', $listDirn, $listOrder); ?>
						</th>
						<th width="30%" class="nowrap hidden-phone hidden-tablet">
							<?php echo JHtml::_('searchtools.sort', 'COM_REDIRECT_HEADING_REFERRER', 'a.referer', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone hidden-tablet">
							<?php echo JHtml::_('searchtools.sort', 'COM_REDIRECT_HEADING_CREATED_DATE', 'a.created_date', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_REDIRECT_HEADING_HITS', 'a.hits', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_REDIRECT_HEADING_STATUS_CODE', 'a.header', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap">
							COMMENT
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="9">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$canEdit   = $user->authorise('core.edit',       'com_redirect');
					$canChange = $user->authorise('core.edit.state', 'com_redirect');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('redirect.published', $item->published, $i); ?>
								<?php // Create dropdown items and render the dropdown list.
								if ($canChange)
								{
									JHtml::_('actionsdropdown.' . ((int) $item->published === 2 ? 'un' : '') . 'archive', 'cb' . $i, 'links');
									JHtml::_('actionsdropdown.' . ((int) $item->published === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'links');
									echo JHtml::_('actionsdropdown.render', $this->escape($item->old_url));
								}
								?>
							</div>
						</td>
						<td class="break-word">
							<?php if ($canEdit) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_redirect&task=link.edit&id=' . $item->id); ?>" title="<?php echo $this->escape($item->old_url); ?>">
									<?php echo $this->escape(str_replace(JUri::root(), '', rawurldecode($item->old_url))); ?></a>
							<?php else : ?>
									<?php echo $this->escape(str_replace(JUri::root(), '', rawurldecode($item->old_url))); ?>
							<?php endif; ?>
						</td>
						<td class="small break-word">
							<?php echo $this->escape(rawurldecode($item->new_url)); ?>
						</td>
						<td class="small break-word hidden-phone hidden-tablet">
							<?php echo $this->escape($item->referer); ?>
						</td>
						<td class="small hidden-phone hidden-tablet">
							<?php echo JHtml::_('date', $item->created_date, JText::_('DATE_FORMAT_LC4')); ?>
						</td>
						<td class="hidden-phone">
							<?php echo (int) $item->hits; ?>
						</td>
						<td class="hidden-phone">
							<?php echo (int) $item->header; ?>
						</td>
						<td class="hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
						<td class="">
							<?php echo $item->comment; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if (!empty($this->items)) : ?>
			<?php echo $this->loadTemplate('addform'); ?>
		<?php endif; ?>
		<?php // Load the batch processing form if user is allowed ?>
			<?php if ($user->authorise('core.create', 'com_redirect')
				&& $user->authorise('core.edit', 'com_redirect')
				&& $user->authorise('core.edit.state', 'com_redirect')) : ?>
				<?php echo JHtml::_(
					'bootstrap.renderModal',
					'collapseModal',
					array(
						'title'  => JText::_('COM_REDIRECT_BATCH_OPTIONS'),
						'footer' => $this->loadTemplate('batch_footer'),
					),
					$this->loadTemplate('batch_body')
				); ?>
			<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
