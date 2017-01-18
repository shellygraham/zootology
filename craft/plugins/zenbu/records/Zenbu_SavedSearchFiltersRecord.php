<?php
namespace Craft;

class Zenbu_SavedSearchFiltersRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'zenbu_saved_search_filters';
    }

    /**
     * definedAttributes
     * filterAttribute1:
     *     - sectionId
     *     - entryTypeId
     *     - (1|title|status|etc...)
     *     - orderby
     * filterAttribute2:
     *     - for orderby: (id|title|etc...)
     *     - for others: (is|isnot|contains|etc...)
     * filterAttribute3:
     *     - for orderby: (asc|desc)
     *     - for others: [string] keyword
     */
    protected function defineAttributes()
    {
        return array(
            'searchId'         => array(AttributeType::Number),
            'filterAttribute1' => array(AttributeType::String),
            'filterAttribute2' => array(AttributeType::String),
            'filterAttribute3' => array(AttributeType::String),
            'order'            => array(AttributeType::Number),
        );
    }

    public function defineRelations()
    {
        return array(
            'search' => array(static::BELONGS_TO, 'Zenbu_SavedSearchesRecord', 'required' => true, 'onDelete' => static::CASCADE),
        );
    }
}