<?php
namespace Craft;

class Zenbu_DisplaySettingsRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'zenbu_display_settings';
    }

    protected function defineAttributes()
    {
        return array(
            'fieldType'   => array(AttributeType::String),
            'userId'      => array(AttributeType::Number),
            'userGroupId' => array(AttributeType::Number),
            'fieldId'     => array(AttributeType::Number),
            'sectionId'   => array(AttributeType::Number),
            'entryTypeId' => array(AttributeType::Number),
            'show'        => array(AttributeType::Bool),
            'order'       => array(AttributeType::Number),
            'settings'    => array(AttributeType::Mixed),
        );
    }

    public function defineRelations()
    {
        return array(
            'user'    => array(static::BELONGS_TO, 'UserRecord', 'required' => true, 'onDelete' => static::CASCADE),
            //'section' => array(static::BELONGS_TO, 'SectionRecord', 'required' => true, 'onDelete' => static::CASCADE),
        );
    }
}