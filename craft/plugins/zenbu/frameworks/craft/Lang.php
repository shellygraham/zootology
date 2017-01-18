<?php namespace Zenbu\frameworks\craft;

use Craft;

class Lang
{
	public static function t($key, $fallback = '')
	{
        return Craft\Craft::t($key, $fallback);
	}
}