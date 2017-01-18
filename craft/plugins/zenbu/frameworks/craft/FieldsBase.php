<?php namespace Zenbu\frameworks\craft;

use Craft;
use Zenbu\frameworks\craft\Convert;
use Zenbu\librairies;

class FieldsBase
{
	var $std_fields;

	public function __construct()
	{
		$this->std_fields = array(
            'title' => array(
                'name'         => Lang::t('Title'),
                'handle'       => 'title',
                'query_handle' => 'title',
                'type'         => '-'
                ),
            'id'    => array(
                'name'         => Lang::t('ID'),
                'handle'       => 'id',
                'query_handle' => 'id',
                'type'         => '-'
                ),
            'uri' => array(
                'name'         => Lang::t('URI'),
                'handle'       => 'uri',
                'query_handle' => 'uri',
                'type'         => '-'
                ),
            'section' => array(
                'name'         => Lang::t('Section'),
                'handle'       => 'section',
                'query_handle' => 'section',
                'type'         => '-'
                ),
            'status' => array(
                'name'         => Lang::t('Status'),
                'handle'       => 'status',
                'query_handle' => 'status',
                'type'         => '-'
                ),
            'postDate' => array(
                'name'         => Lang::t('Post Date'),
                'handle'       => 'postDate',
                'query_handle' => 'postDate',
                'type'         => '-'
                ),
            );

        if(Craft\craft()->edition != 0)
        {
            $this->std_fields['author'] = array(
                    'name'         => Lang::t('Author'),
                    'handle'       => 'author',
                    'query_handle' => 'authorId',
                    'type'         => '-'
                    );
        }
	}

	public function getFields()
	{
		$sql = Craft\craft()->db->createCommand()
                    ->select('et.*')
                    ->from('entrytypes et')
                    ->queryAll();

        $output = array();
        $field_output = array();

        if(count($sql > 0))
        {
            foreach($sql as $row)
            {
                $fieldLayout = Craft\craft()->fields->getLayoutById( $row['fieldLayoutId'] );
                $fields      = $fieldLayout->getFields();
                
                foreach($fields as $f)
                {
                    $field_output[$f->field->id] = $f->field->getAttributes();
                }

                $output[Convert::col('sectionId').'_'.$row[Convert::col('sectionId')]][Convert::col('subSectionId').'_'.$row['id']] = $this->std_fields + $field_output;

                $field_output = array();
            }

        }

        return $output;
	}
}