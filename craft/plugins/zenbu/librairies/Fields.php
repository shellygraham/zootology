<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\frameworks\craft\FieldsBase as FieldsBase;
use Zenbu\librairies;

class Fields extends FieldsBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFields($sectionId = FALSE, $subSectionId = FALSE)
    {
        $sectionId    = $sectionId === FALSE ? Request::param(Convert::col('sectionId')) : $sectionId;
        $subSectionId = $subSectionId === FALSE ? Request::param(Convert::col('subSectionId')) : $subSectionId;
        
        if(Cache::get('Zenbu_getFields_User' . Session::user()->id) !== FALSE)
        {
            $output = Cache::get('Zenbu_getFields_User' . Session::user()->id);
        }
        else
        {
            $fields = new FieldsBase();
            $output = $fields->getFields();

            Cache::set('Zenbu_getFields_User' . Session::user()->id, $output, 10);
        }

        if(isset($output[Convert::col('sectionId').'_'.$sectionId]))
        {
            if(! $subSectionId)
            {
                foreach($output[Convert::col('sectionId').'_'.$sectionId] as $etId => $array)
                {
                    $subSectionId = str_replace(Convert::col('subSectionId').'_', '', $etId);           
                }
            }

            return $output[Convert::col('sectionId').'_'.$sectionId][Convert::col('subSectionId').'_'.$subSectionId];        
        }
        else
        {
            return $this->std_fields;
        }

    } // END getFields

    // --------------------------------------------------------------------


    public function getOrderedFields($include_nonvisible = FALSE)
    {
        $settings = new Settings();
        $settings = $settings->getDisplaySettings();
        $fields   = $this->getFields();

        $vars = array();

        foreach($settings['fields'] as $key => $setting)
        {
            $fieldkey = $setting[Convert::col('fieldType')] == 'field' ? $setting[Convert::col('fieldId')] : $setting[Convert::col('fieldType')];
            if(isset($fields[$fieldkey]))
            {
                $vars[$fieldkey] = $fields[$fieldkey];
            }
        }

        if($include_nonvisible !== FALSE)
        {
            foreach($fields as $fieldkey => $field_data)
            {
                if( ! isset($vars[$fieldkey]) )
                {
                    $vars[$fieldkey] = $field_data;
                }
            }
        }
        
        return $vars;
    } // END getOrderedFields

    // --------------------------------------------------------------------
}