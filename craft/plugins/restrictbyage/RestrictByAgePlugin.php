<?php

namespace Craft;

class RestrictByAgePlugin extends BasePlugin
{
    function getName()
    {
         return Craft::t('Restrict By Age');
    }

    function getVersion()
    {
        return '1.0';
    }

    function getDeveloper()
    {
        return 'Jux Collective';
    }

    function getDeveloperUrl()
    {
        return 'http://juxcollective.com';
    }
}