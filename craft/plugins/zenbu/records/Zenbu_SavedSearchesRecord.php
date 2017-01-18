<?php
namespace Craft;

class Zenbu_SavedSearchesRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'zenbu_saved_searches';
    }

    protected function defineAttributes()
    {
        return array(
            'label'       => array(AttributeType::String),
            'userId'      => array(AttributeType::Number),
            'userGroupId' => array(AttributeType::Number),
            'order'       => array(AttributeType::Number),
            //'sectionId'   => array(AttributeType::Number),
            //'entryTypeId' => array(AttributeType::Number),
            //'orderby'     => array(AttributeType::String),
            //'sort'        => array(AttributeType::String),
        );
    }

    public function defineRelations()
    {
        return array(
            'user' => array(static::BELONGS_TO, 'UserRecord', 'required' => true, 'onDelete' => static::CASCADE),
            //'section' => array(static::BELONGS_TO, 'SectionRecord', 'required' => true, 'onDelete' => static::CASCADE),
        );
    }
}