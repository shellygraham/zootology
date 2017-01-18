<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\frameworks\craft\SectionBase as SectionBase;

class Sections extends SectionBase
{
    public static function getSections()
    {
        return SectionBase::getSections();
    }

    public static function getSubSections($section_id = 0)
    {
        return SectionBase::getSubSections($section_id);
    }

    /**
     * Retrieve a list of sections for settings select dropown
     * @return array The section array
     */
    public function getSectionSelectOptions()
    {
        $sections         = $this->getSections();
        $dropdown_options = SectionBase::buildSelectOptions($sections);

        return $dropdown_options;

    } // END getSectionSelectOptions

    // --------------------------------------------------------------------
}