<?php
namespace Craft;

class m150304_120000_Zenbu_version0_2_1 extends BaseMigration
{
    public function safeUp()
    {
    	craft()->db->createCommand()->createTable('zenbu_display_settings', array(
			'fieldType'   => array(),
			'userId'      => array('maxLength' => 11, 'decimals' => 0, 'unsigned' => false, 'length' => 10, 'column' => 'integer'),
			'userGroupId' => array('maxLength' => 11, 'decimals' => 0, 'unsigned' => false, 'length' => 10, 'column' => 'integer'),
			'fieldId'     => array('maxLength' => 11, 'decimals' => 0, 'unsigned' => false, 'length' => 10, 'column' => 'integer'),
			'sectionId'   => array('maxLength' => 11, 'decimals' => 0, 'unsigned' => false, 'length' => 10, 'column' => 'integer'),
			'entryTypeId' => array('maxLength' => 11, 'decimals' => 0, 'unsigned' => false, 'length' => 10, 'column' => 'integer'),
			'show'        => array('maxLength' => 1, 'default' => false, 'required' => true, 'column' => 'tinyint', 'unsigned' => true),
			'order'       => array('maxLength' => 11, 'decimals' => 0, 'unsigned' => false, 'length' => 10, 'column' => 'integer'),
			'settings'    => array('column' => 'text'),
		), null, true);
        return true;
    }
}