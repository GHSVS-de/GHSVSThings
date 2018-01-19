<?php
/*
JLayout password_hint.php


Copy to path:

templates/YOUR-TEMPLATE/html/layouts/joomla/password_hint.php


Create language string:

GHSVS_PASSWORD_REQUIREMENTS="<div class=password-requirements><ul class='list-unstyled list-striped'><li>muss eine Länge von mindestens %s Zeichen haben,</li><li>mindestens %s Zahlen enthalten,</li><li>mindestens %s Sonderzeichen enthalten (! &#64; # $ etc.),</li><li>mindestens %s Großbuchstaben enthalten</li><li>und darf keine Leerzeichen enthalten.</li></ul></div>"


Call somewhere in Joomla-Code (e.g. Template override of registration form):

echo JLayoutHelper::render('joomla.password_hint', array('style' => '.p4password_hint_button{margin-top:-14px}'));

*/ 
?>

<?php
defined('JPATH_BASE') or die;
?>

<p class="p4password_hint_button"><a class="btn btn-primary btn-xs" data-toggle="modal" data-target=".bs-example-modal-sm">
 <?php echo JText::_('GHSVS_PASSWORD_REQUIREMENTS_BTN'); ?>
</a></p>

<div class="modal bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" style="display:none">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
         <?php echo JText::_('GHSVS_PASSWORD_REQUIREMENTS_HEADLINE'); ?>
        </h4>
      </div>
      <div class="modal-body">
<?php
    $userParams = JComponentHelper::getParams('com_users');
    echo JText::sprintf('GHSVS_PASSWORD_REQUIREMENTS',
			 $userParams->get('minimum_length'),
				$userParams->get('minimum_integers'),
				$userParams->get('minimum_symbols'),
				$userParams->get('minimum_uppercase')
			); ?>
      </div>
    </div>
  </div>
</div>
<?php
if (!empty($displayData['style']))
{
	echo '<style>' . $displayData['style'] . '</style>';
}
