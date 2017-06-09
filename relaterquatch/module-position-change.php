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
