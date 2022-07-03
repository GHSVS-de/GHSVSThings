<?php
/*
Re:Later 2017-06-09
Simple hard coded possibility to exchange a module position on specific pages (Itemids).
https://forum.joomla.de/index.php/Thread/4004-JModuleHelper-getModule-findet-Modul-über-Titel-nicht/
*/

public function onAfterModuleList($modules)
{

 if (JFactory::getApplication()->isClient('administrator'))
 {
  return;
 }
 // echo 'DEBUG $modules-Array: '.print_r($modules, true);exit;
 // Auf welcher Seite bin ich? Plump. Sonst muss man halt Menüs abfragen o.ä.
 $currentPageId = JFactory::getApplication()->input->get('Itemid');
 // echo 'DEBUG $currentPageId: '.print_r($currentPageId,true);exit;

 // Plump. Hartkodiert. In welchen Menü-Ids Position austauschen.
 $exchangeIn = array(435, 272, 254, 862);

 if (in_array($currentPageId, $exchangeIn))
 {
  //echo 'DEBUG To do: '.print_r('Change position!', true);exit;

  foreach ($modules as $module)
  {

   // Weitere Abfrage-Möglichkeit $module->title.
   if ($module->module == 'mod_custom' && $module->position == 'position-8')
   {
    // Modulpos. neu setzen.
    $module->position = 'banner';
    //echo 'DEBUG DONE: '.print_r('Position exchanged!', true);exit;

   }
  }
 }
}

/*
Und so könnten die Felder für Konfiguration aussehen (in Joomla 4!), die man den Modulen
zusätzlich unterjubeln muss, z.B. via onContentPrepareForm im selben System-Plugin wie oben.
Joomla 3 kennt type="ModulesPositionedit" nicht und zeigt dann nur ein Textfeld
an! Muss man wahrscheinlich type="modulesposition" versuchen. Oder lebt eben mit
dem Textfeld.
*/
<field name="modulePosition" type="ModulesPositionedit"
	client="site"
	label="PLG_SYSTEM_BS3GHSVS_MODULEPOSITION"/>

<field name="modulePositionMenuItems" type="Menuitem"
	multiple="true" layout="joomla.form.field.groupedlist-fancy-select"
	label="PLG_SYSTEM_BS3GHSVS_MODULEPOSITION_MENUITEMS">
	<option value="0">JNONE</option>
</field>
