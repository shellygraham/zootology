<?php namespace Zenbu\frameworks\craft;

use Craft;

class Db
{
	public static function rawQuery($sql)
	{
		$sql = preg_replace("/zenbu\_([a-z\_]*)/i", '{{zenbu_$1}}', $sql);
		return Craft\craft()->db->createCommand($sql)->queryAll();
	}
}