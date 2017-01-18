<?php
namespace Craft;

use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;

class ZenbuService extends BaseApplicationComponent
{
    var $std_fields;
    var $user;

    public function init()
    {
        $this->std_fields = array(
            'title' => array(
                'name'         => Lang::t('Title'),
                'handle'       => 'title',
                'query_handle' => 'title',
                'type'         => '-'
                ),
            'id'    => array(
                'name'         => Craft::t('ID'),
                'handle'       => 'id',
                'query_handle' => 'id',
                'type'         => '-'
                ),
            'uri' => array(
                'name'         => Craft::t('URI'),
                'handle'       => 'uri',
                'query_handle' => 'uri',
                'type'         => '-'
                ),
            'section' => array(
                'name'         => Craft::t('Section'),
                'handle'       => 'section',
                'query_handle' => 'section',
                'type'         => '-'
                ),
            'status' => array(
                'name'         => Craft::t('Status'),
                'handle'       => 'status',
                'query_handle' => 'status',
                'type'         => '-'
                ),
            'postDate' => array(
                'name'         => Craft::t('Post Date'),
                'handle'       => 'postDate',
                'query_handle' => 'postDate',
                'type'         => '-'
                ),
            );

        if(craft()->edition != 0)
        {
            $this->std_fields['author'] = array(
                    'name'         => Craft::t('Author'),
                    'handle'       => 'author',
                    'query_handle' => 'authorId',
                    'type'         => '-'
                    );
        }

        $this->user       = craft()->userSession->getUser();
        
    }

    public function getFirstFilterOptions()
    {
        $sections = craft()->sections->getEditableSections();

        // Using a non-numerical parameter will
        // output standard fields only, even if GET/POST present
        $default_fields = $this->getFields('default');
        unset($default_fields['section']); // Don't need this, we have it as the top dropdown

        foreach($default_fields as $handle => $field)
        {
            $output[0][0][$handle] = $field['name'];
        }

        foreach($sections as $section)
        {
            $entry_types = craft()->sections->getSectionById($section->id)->entryTypes;

            foreach($entry_types as $et)
            {
                $filter_fields = $this->getFields($section->id, $et->id);
                unset($filter_fields['section']); // Don't need this, we have it as the top dropdown

                foreach($filter_fields as $handle => $field)
                {
                    $output[$section->id][$et->id][$handle] = $field['name'];
                }
            }
        }

        return $output;
    }

    public function getSecondFilterOptions()
    {
        $output[] = array(
            'is' => Craft::t('is'),
            );
        $output[] = array(
            'is' => Craft::t('is'),
            'isnot' => Craft::t('is not'),
            );
        $output[] = array(
            'after' => Craft::t('after'),
            'before' => Craft::t('before'),
            'on' => Craft::t('on'),
            );
        $output[] = array(
            'contains' => Craft::t('contains'),
            'beginswith' => Craft::t('begins with'),
            'endswith' => Craft::t('ends with'),
            'doesnotcontain' => Craft::t('does not contain'),
            'doesnotbeginwith' => Craft::t('does not begin with'),
            'doesnotendwith' => Craft::t('does not end with'),
            );
        $output[] = array(
            'contains' => Craft::t('contains'),
            'beginswith' => Craft::t('begins with'),
            'endswith' => Craft::t('ends with'),
            'doesnotcontain' => Craft::t('does not contain'),
            'doesnotbeginwith' => Craft::t('does not begin with'),
            'doesnotendwith' => Craft::t('does not end with'),
            'isempty' => Craft::t('is empty'),
            'isnotempty' => Craft::t('is not empty'),
            );

        return $output;
    }

    public function getFields($sectionId = FALSE, $entryTypeId = FALSE)
    {
        $sectionId   = $sectionId === FALSE ? craft()->request->getParam('sectionId') : $sectionId;
        $entryTypeId = $entryTypeId === FALSE ? craft()->request->getParam('entryTypeId') : $entryTypeId;
        
        if(Cache::get('Zenbu_getFields_User' . $this->user->id) !== FALSE)
        {
            $output = Cache::get('Zenbu_getFields_User' . $this->user->id);
        }
        else
        {
            $sql = craft()->db->createCommand()
                    ->select('et.*')
                    ->from('entrytypes et')
                    ->queryAll();

            $output = array();
            $field_output = array();

            if(count($sql > 0))
            {
                foreach($sql as $row)
                {
                    $fieldLayout = craft()->fields->getLayoutById( $row['fieldLayoutId'] );
                    $fields      = $fieldLayout->getFields();
                    
                    foreach($fields as $f)
                    {
                        $field_output[$f->field->id] = $f->field->getAttributes();
                    }

                    $output['sectionId_'.$row['sectionId']]['entryTypeId_'.$row['id']] = $this->std_fields + $field_output;

                    $field_output = array();
                }

            }

            Cache::set('Zenbu_getFields_User' . $this->user->id, $output, 10);
        }

        if(isset($output['sectionId_'.$sectionId]))
        {
            if(! $entryTypeId)
            {
                foreach($output['sectionId_'.$sectionId] as $etId => $array)
                {
                    $entryTypeId = str_replace('entryTypeId_', '', $etId);           
                }
            }

            return $output['sectionId_'.$sectionId]['entryTypeId_'.$entryTypeId];        
        }
        else
        {
            return $this->std_fields;
        }

    } // END getFields

    // --------------------------------------------------------------------


    /**
     * Retrieve a list of sections for settings select dropown
     * @return array The section array
     */
    public function getSectionSelectOptions()
    {
        $sections   = craft()->sections->getEditableSections();
        $singles    = array();
        $channels   = array();
        $structures = array();

        $dropdown_options['sections'] = array(
            '' => Craft::t('All Sections'),
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
            $dropdown_options['sections']['sections_optgroup']['optgroup'] =  Craft::t('Singles');

            foreach($singles as $section)
            {
                $dropdown_options['sections'][$section['id']] = $section['name'];
            }
        }

        if(count($channels) > 0)
        {
            $dropdown_options['sections']['channel']['optgroup'] = Craft::t('Channel');

            foreach($channels as $section)
            {
                $dropdown_options['sections'][$section['id']] = $section['name'];
            }
        }

        if(craft()->edition != 0)
        {
            if(count($structures) > 0)
            {
                $dropdown_options['sections']['structures']['optgroup'] = Craft::t('Structures');

                foreach($structures as $section)
                {
                    $dropdown_options['sections'][$section['id']] = $section['name'];
                }
            }
        }

        return $dropdown_options;

    } // END getSectionSelectOptions

    // --------------------------------------------------------------------

    /**
     * Retrieve Show X results dropdown
     * @return array
     */
    public function getLimitSelectOptions()
    {
        $output = array(
            //'1' => Craft::t('Show') . ' 1 ' . Craft::t('result'),
            //'2' => Craft::t('Show') . ' 2 ' . Craft::t('results'),
            '5' => Craft::t('Show') . ' 5 ' . Craft::t('results'),
            '10' => Craft::t('Show') . ' 10 ' . Craft::t('results'),
            '25' => Craft::t('Show') . ' 25 ' . Craft::t('results'),
            '50' => Craft::t('Show') . ' 50 ' . Craft::t('results'),
            '100' => Craft::t('Show') . ' 100 ' . Craft::t('results'),
            '200' => Craft::t('Show') . ' 200 ' . Craft::t('results'),
            '500' => Craft::t('Show') . ' 500 ' . Craft::t('results'),
            );

        return $output;

    } // END getLimitSelectOptions

    // --------------------------------------------------------------------


    /**
     * Retrieve Order By dropdown
     * @return array
     */
    public function getOrderBySelectOptions()
    {
        foreach($this->std_fields as $handle => $field)
        {
            $output[$field['query_handle']] = strtolower($field['name']);
        }

        return $output;

    } // END getOrderBySelectOptions

    // --------------------------------------------------------------------

    /**
     * Retrieve saved searches
     * @return array
     */
    public function getSavedSearches()
    {
        if(Cache::get('Zenbu-SavedSearches-User' . $this->user->id) !== FALSE)
        {
            return Cache::get('Zenbu-SavedSearches-User' . $this->user->id);
        }

        $sql = craft()->db->createCommand()
                ->select('*')
                ->from('zenbu_saved_searches')
                ->where('userId = ' . $this->user->id)
                ->order('order ASC')
                ->queryAll();

        $output = array();

        if(count($sql > 0))
        {
            foreach($sql as $row)
            {
                $output[] = $row;
            }
        }

        Cache::set('Zenbu-SavedSearches-User' . $this->user->id, $output, 10);

        return $output;

    } // END getSavedSearches()

    // --------------------------------------------------------------------


    public function getSavedSearchFilters()
    {
        $searchId = craft()->request->getParam('searchId');

        if( ! empty($searchId) && ctype_digit($searchId) )
        {
            $sql = craft()->db->createCommand()
                    ->select('ssf.*')
                    ->from('zenbu_saved_search_filters ssf')
                    ->join('zenbu_saved_searches ss', 'ss.id = ssf.searchId')
                    ->where('ssf.searchId = ' . $searchId)
                    ->andWhere('ss.userId = ' . $this->user->id)
                    ->order('ssf.order ASC')
                    ->queryAll();

            $output = array();

            if(count($sql > 0))
            {
                foreach($sql as $row)
                {
                    $output[] = $row;
                }
            }

            return $output;
        }
        else
        {
            return array();
        }
    } // END getSavedSearchFilters()

    // --------------------------------------------------------------------
}