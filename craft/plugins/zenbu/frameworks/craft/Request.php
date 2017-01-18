<?php namespace Zenbu\frameworks\craft;

use Craft;

class Request
{
	public static function get($key)
	{
        return Craft\craft()->request->get($key);
	}

	public static function post($key, $default = FALSE)
	{
		if(Craft\craft()->request->getPost($key))
		{
	        return Craft\craft()->request->getPost($key);
		}
		else
		{
			return $default;
		}
	}

	public static function param($key, $default = FALSE)
	{
		if(Craft\craft()->request->getParam($key))
		{
	        return Craft\craft()->request->getParam($key);		
		}
		else
		{
			return $default;
		}
	}

	public static function redirect($url)
	{
		return Craft\craft()->request->redirect($url);
	}
}