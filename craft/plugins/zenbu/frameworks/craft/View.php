<?php namespace Zenbu\frameworks\craft;

use Craft;

class View
{
	public static function includeJs($file)
	{
		if(is_array($file))
		{
			foreach($file as $f)
			{
				$output = Craft\craft()->templates->includeJsResource($f);		
			}

			return $output;
		}
		else
		{
			return Craft\craft()->templates->includeJsResource($file);		
		}
	}

	public static function includeCss($file)
	{
		if(is_array($file))
		{
			foreach($file as $f)
			{
				$output = Craft\craft()->templates->includeCssResource($f);		
			}

			return $output;
		}
		else
		{
			return Craft\craft()->templates->includeCssResource($file);		
		}
	}
}