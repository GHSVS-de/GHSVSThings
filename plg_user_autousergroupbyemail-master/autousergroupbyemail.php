<?php
defined('_JEXEC') or die;

class PlgUserAutoUserGroupByEmail extends JPlugin
{

 protected $app;

 public function onUserAfterSave($user, $isnew, $success, $msg)
 {
  
  // Nur neue User. Nur Registrierungen im Frontend.
  if ($isnew && $this->app->isSite())
  {

   /*
   #__usergroups (kann bei jedem Joomla abweichen! Selber in DB schauen!):
   1:Public
   2:Registered
   3:Author
   4:Editor
   5:Publisher
   6:Manager
   7:Administrator
   8:Super Users
   10:Shop Suppliers (Example)
   12:Customer Group (Example)
   13:Guest
   */

   $groups = array(
    'firma1.com' => array(5),
    'firma2.com' => array(6, 10),
    'gmx.de' =>  array(7, 10, 12, 13),
    'default' => array(2),
   );
   
   $userNew = JFactory::getUser($user['id']);
   
   $parts = explode('@', $userNew->get('email'));
   
   if (!empty($parts[1]) && isset($groups[$parts[1]]))
   {
    $groups = $groups[$parts[1]];
   }
   else
   {
    $groups = $groups['default'];
   }
   
   $userNew->set('groups', $groups);   
   
   $userNew->save();
   
  }
 }

}
