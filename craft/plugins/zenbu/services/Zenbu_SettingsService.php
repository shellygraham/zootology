<?php
namespace Craft;

use Zenbu\librairies\Settings;

class Zenbu_SettingsService extends BaseApplicationComponent
{
    var $settings;

    public function __construct()
    {
        $this->settings = new Settings();
    }

    public function getDisplaySettings($what = '')
    {
        return $this->settings->getDisplaySettings();
    } // END getDisplaySettings

    // --------------------------------------------------------------------


    public function getDefaultDisplaySettings()
    {
        return $this->settings->getDefaultDisplaySettings();
    }

}