<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
return;
$db = Factory::getDBO();

$query = $db->getQuery(true);
$query->select('*')->from('#__contact_details');
$db->setQuery($query);

$contacts = $db->loadObjectList();
//echo ' 4654sd48sa7d $contacts BEFORE <pre>' . print_r(count($contacts), true) . '</pre>';exit;

$standardParams = '{"show_contact_category":"","show_contact_list":"","show_tags":"","show_info":"","show_name":"1","show_position":"0","show_email":"1","add_mailto_link":"","show_street_address":"1","show_suburb":"0","show_state":"0","show_postcode":"0","show_country":"0","show_telephone":"1","show_mobile":"1","show_fax":"1","show_webpage":"1","show_image":"1","show_misc":"","allow_vcard":"","show_articles":"","articles_display_num":"","show_profile":"","contact_layout":"","show_links":"","linka_name":"","linka":"","linkb_name":"","linkb":"","linkc_name":"","linkc":"","linkd_name":"","linkd":"","linke_name":"","linke":"","show_email_form":"","show_email_copy":"","validate_session":"","custom_reply":"","redirect":""}';


foreach ($contacts as $contact)
{
	$contact->params = $standardParams;
	$db->updateObject('#__contact_details', $contact, 'id');
}

//echo ' 4654sd48sa7d $contacts DONE <pre>' . print_r(count($contacts), true) . '</pre>';exit;

return;
