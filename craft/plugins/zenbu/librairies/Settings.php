<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\librairies\Fields;

class Settings
{
    var $fields;

    public function __construct()
    {
        $this->fields = new Fields();
    }

    public function getBaseSettings()
    {
        return Craft\craft()->plugins->getPlugin('zenbu')->getSettings();
    }

	public function getDisplaySettings($what = '')
    {
    	$sql = '/* '.__FUNCTION__.' */ SELECT zds.* FROM zenbu_display_settings zds
		    	WHERE zds.sectionId = '. Request::param('sectionId', 0) . '
		    	AND zds.userId = ' . Session::user()->id;

		if($what != 'all')
        {
            $sql .= " AND zds.show = 1";
        }

        if(Request::param(Convert::col('subSectionId')))
        {
            $sql .= " AND zds.".Convert::col('subSectionId')." = " . Request::param(Convert::col('subSectionId'), 0);
        }

        $sql .= " ORDER BY `order` ASC";

        $results = Db::rawQuery($sql);

        $display_settings = array();

        if(count($results) > 0)
        {
            foreach($results as $row)
            {
				$display_settings['sectionId']                  = $row['sectionId'];
				$display_settings[Convert::col('subSectionId')] = $row[Convert::col('subSectionId')];
				$display_settings['fields'][$row['order']]      = array(
                    'show'      => $row['show'],
                    'fieldType' => $row[Convert::col('fieldType')],
                    'fieldId'   => $row[Convert::col('fieldId')],
                    'order'     => $row['order'],
                    'settings'  => json_decode($row['settings'])
                    );
                $handle = $row[Convert::col('fieldType')] == 'field' ? $row[Convert::col('fieldId')] : $row[Convert::col('fieldType')];
                $display_settings['settings'][$handle] = json_decode($row['settings']);
            }
        }
        else
        {
            $display_settings = $this->getDefaultDisplaySettings();
        }

        return $display_settings;

    } // END getDisplaySettings

    // --------------------------------------------------------------------

    public function getDefaultDisplaySettings()
    {
        $fields                          = $this->fields->getFields();
        $display_settings['sectionId']   = Request::post('sectionId');
        $display_settings['entryTypeId'] = Request::post('entryTypeId');

        foreach($fields as $handle => $field)
        {
            $display_settings['fields'][] = array(
                'show'     => 1,
                'fieldType'=> is_integer($handle) ? 'field' : $handle,
                'fieldId'  => is_integer($handle) ? $handle : 0,
                'order'    => 0,
                'settings' => ''
                );
        }

        return $display_settings;
    }
}