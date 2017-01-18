<?php namespace Zenbu\frameworks\craft;

use Craft;

class Session
{
	public static function user()
	{
        return Craft\craft()->userSession->getUser();
	}

	public static function setFlash($key, $message = '')
	{
        return Craft\craft()->userSession->setFlash($key, $message);
	}

	public static function getFlash($key)
	{
        return Craft\craft()->userSession->getFlash($key);
	}
}