<?php
namespace Craft;

class RestrictByAgeVariable
{
    public function canView()
    {
    	$session = craft()->getSession();
        // return false;
        return $session['over21'];
    }
}