<?php
/**
 * @package plugin.system mybaseplugin for Joomla!
 * @version See mybaseplugin.xml
 * @author G@HService Berlin Neukölln, Volkmar Volli Schlothauer
 * @copyright Copyright (C) 2020, G@HService Berlin Neukölln, Volkmar Volli Schlothauer. All rights reserved.
 * @license  GNU General Public License version 3 or later; see LICENSE.txt
 * @authorUrl https://www.ghsvs.de
 */
?>
<?php
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class PlgSystemmybaseplugin extends CMSPlugin
{
	protected $app;
	protected $db;
	protected $autoloadLanguage = true;
 
	protected $execute = null;
	
	// media/ path
	protected static $basepath = 'plg_system_mybaseplugin';
	
	public function onAfterDispatch()
	{
	HTMLHelper::addIncludePath(__DIR__ . '/html');
		if (Factory::getDocument()->getType() !== 'html')
		{
			$this->execute = false;
			return;
		}

		$this->execute = true;
	}
 
	/**
	 nach onBeforeRender
	*/
 public function onBeforeCompileHead()
 {
  if ($this->app->isClient('administrator'))
  {
			
		}
		else
		{
			
		}
	}

	/**
	 vor onBeforeCompileHead
	*/
	public function onBeforeRender()
	{
	}



 public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof Form))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
		
		
		
		   return true;
		
		
		
		$view = $this->app->input->get('view', '');
		$layout = $this->app->input->get('layout', '');
		
		$context = $form->getName();
		
  $allowedContext = array(
   'com_content.article',
  );
  
  ###Load from file:
  Form::addFormPath(__DIR__.'/myforms');
  //loads /pluginpath/myforms/zeugs.xml
  $form->loadFile('zeugs', $reset=false, $path=false);
  
  ###Build dynamically 1:
  $addform = new SimpleXMLElement('<form />');
  $fields = $addform->addChild('fields');
  $fields->addAttribute('name', 'attribs');
  $fieldset = $fields->addChild('fieldset');
  $fieldset->addAttribute('name', 'Zeugs2');
  $fieldset->addAttribute('description', 'Zeugs2 Beschreibung');
  $fieldset->addAttribute('label', 'Zeugs2 Label');
  $field = $fieldset->addChild('field');
  $field->addAttribute('name', 'zeug2');
  $field->addAttribute('type', 'text');
  $field->addAttribute('id', 'zeug2');
  $field->addAttribute('description', 'Zeuhs 2');
  $field->addAttribute('filter', 'string');
  $field->addAttribute('label', 'Was Zeugs 2:');
  $field->addAttribute('size', '30');
  $form->load($addform, $reset=false, $path=false);
  
  ###Build dynamically 2:
  //No leading space or linebreak!
  $addform='<?xml version="1.0" encoding="utf-8"?>
<form>
	<field
			id="irgendwas"
			name="irgendwas"
			type="text"
   label="IRGENDWAS" />
 <fields name="attribs">
  <fieldset name="zeugs3" label="Zeugs3">
   <field name="zeug3" type="text" id="zeug3"
    description="Zeuhs 3" filter="string"
    label="Was Zeugs 3:" size="30" />
  </fieldset>
 </fields>
</form>';
  $form->load($addform, $reset=false, $path=false);
  return true;
 } # OnCeontentPrepareForm



 /**
 Normally only fired in list views, not in edit views.
 */
 public function onContentChangeState($context, $pks, $value)
	{

		return;
	}
	public function onContentPrepareData($context, $data){
		
		return;
	
	}
	public function onContentAfterSave($context, $article, $isNew){
		
		
		return;

		
	}
 public function onContentBeforeSave($context, $article, $isNew){
		
		return;
		
 }

	
 public function onExtensionAfterSave($context, $table, $isNew)
 {
	}
 public function onExtensionBeforeDelete($context, $table)
 {
 }
 public function onExtensionBeforeSave($context, $table, $isNew)
 {
 }


}
