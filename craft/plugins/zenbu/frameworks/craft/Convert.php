<?php namespace Zenbu\frameworks\craft;

use Craft;

class Convert
{
	public static function col($col)
	{
		$cols = array(
			'fieldId'      => 'fieldId',
			'fieldType'    => 'fieldType',
			'sectionId'    => 'sectionId',
			'subSectionId' => 'entryTypeId',
			);
        return $cols[$col];
	}

	public static function string($str)
	{
		$strings = array(
			'subSubsections' => 'entryTypes',
			'sections'       => 'sections',
			);
	}
}