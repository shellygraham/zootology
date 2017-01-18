<?php namespace Zenbu\frameworks\craft;

use Craft;
use Zenbu\frameworks\craft\Lang;

class SectionBase
{
	public static function getSections()
	{
		return Craft\craft()->sections->getEditableSections();
	}

	public static function getSubSections($section_id)
	{
		return Craft\craft()->sections->getSectionById($section_id)->entryTypes;
	}

	public static function buildSelectOptions($sections)
	{
		$singles    = array();
        $channels   = array();
        $structures = array();
        
        $dropdown_options['sections'] = array(
            '' => Lang::t('All Sections'),
            );

        foreach($sections as $section)
        {
            if(count($section->entryTypes) > 1)
            {
                foreach($section->entryTypes as $et)
                {
                    $entry_types[$section['id']][$et->id] = $et->name;
                }
            }
        }

        $dropdown_options['entryTypes'] = isset($entry_types) ? $entry_types : array();

        foreach($sections as $section)
        {
            if($section['type'] == 'single')
            {
                $singles[] = $section;
            }

            if($section['type'] == 'channel')
            {
                $channels[] = $section;
            }

            if($section['type'] == 'structure')
            {
                $structures[] = $section;
            }
        }

        if(count($singles) > 0)
        {
            $dropdown_options['sections']['sections_optgroup']['optgroup'] =  Lang::t('Singles');

            foreach($singles as $section)
            {
                $dropdown_options['sections'][$section['id']] = $section['name'];
            }
        }

        if(count($channels) > 0)
        {
            $dropdown_options['sections']['channel']['optgroup'] = Lang::t('Channel');

            foreach($channels as $section)
            {
                $dropdown_options['sections'][$section['id']] = $section['name'];
            }
        }

        if(Craft\craft()->edition != 0)
        {
            if(count($structures) > 0)
            {
                $dropdown_options['sections']['structures']['optgroup'] = Lang::t('Structures');

                foreach($structures as $section)
                {
                    $dropdown_options['sections'][$section['id']] = $section['name'];
                }
            }
        }

        return $dropdown_options;
	}
}