<?php

/**
 * @package Crmery.Plugin
 * @subpackage Crmery.Search
 * 
 * @copyright 2017
 * @license Dual MIT/GPL3
 */

if (!defined('_JEXEC')){
    die('Restricted Access');
}

class PlgSearchCrmery extends JPlugin
{
    // Standard function to return search areas
    public function onContentSearchAreas()
    {
	static $areas = array(
	    'crmery' => 'Crmery'
	);
	return $areas;
    }

    public function onContentSearch(string $text, string $phrase='', $ordering='', $areas=null)
    {
	JLoader::registerPrefix('Crmery', JPATH_BASE . '/components/com_crmery');
        JPluginHelper::importPlugin('crmery');

	$user = JFactory::getUser();

	if ($user->guest) {
	    return [];
	}

	if ($text === '') {
	    return [];
	}

        if (is_array($areas) && !array_intersect($areas, array_keys($this->onContentSearchAreas())))
        {
            return [];
        }

	if (!class_exists('CrmeryModelCompany'))
	{
	    return [ (object) [
		'href' => 'administrator/index.php',
		'title' => 'CRMery is not Available',
		'section' => '',
		'created' => (new DateTime()),
		'text' => 'Please Re-enable It',
		'browsernav' => 1,
	    ]];
	}

	$db    = JFactory::getDBO();
	$query = $db->getQuery(true)
		    ->select("c.*, cf.value as abbreviation")
		    ->from("#__crmery_companies AS c")
		    ->leftJoin("#__crmery_users AS u on u.id = c.owner_id")
		    ->leftJoin("#__crmery_company_custom_cf as cf on cf.company_id = c.id")
		    ->where('cf.custom_field_id = 8')
		    ->where("c.name LIKE " . $db->quote('%' . $text . '%')
		            . ' OR '
			    . "cf.value LIKE "  . $db->quote('%' . $text . '%'));
	$companies = $db->setQuery($query)->loadAssocList();
	
    return array_map( function($company){
        $id = $company['id'];
        $result = [
            'href' => "index.php?option=com_crmery&view=companies&layout=company&id=$id&Itemid=771",
            'title' => $company['name'],
            'section' => 'Companies',
            'created' => (new DateTime()),
            'text' => $company['abbreviation'],
            'browsernav' => 1,
        ];
        return (object) $result;
        }, $companies);
    }

    public function onAfterInitialise()
    {
	JLoader::registerPrefix('Crmery', JPATH_ADMINISTRATOR . '/components/com_crmery');
    }
}
