<?php namespace Zenbu\frameworks\craft;

use Craft;

class Cache
{
	public static function set($key, $value, $duration)
	{
        Craft\craft()->cache->set($key, $value, $duration);
	}

	public static function get($key)
	{
        return Craft\craft()->cache->get($key);
	}

	public static function delete($key)
	{
        return Craft\craft()->cache->delete($key);
	}
}