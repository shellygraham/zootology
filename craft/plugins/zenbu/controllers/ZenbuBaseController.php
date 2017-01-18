<?php
namespace Zenbu\controllers;

use Craft;
use Craft\BaseController as BaseController;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Session;
use Zenbu\librairies\Settings;
use Zenbu\librairies\Fields;
use Zenbu\librairies\Filters;
use Zenbu\librairies\Sections;
use Zenbu\librairies\SavedSearches;

class ZenbuBaseController extends BaseController
{
	public $user;
	public $nav_label;
	public $sections;
	public $fields;
	public $filters;
	public $settings;
	public $base_settings;
	public $display_settings;
	public $saved_searches;

	public function init()
	{
		$message                = Session::getFlash('messageNotice');
		Craft\craft()->userSession->setNotice($message);

		$this->user             = Session::user();
		$this->sections         = new Sections();
		$this->fields           = new Fields();
		$this->filters          = new Filters();
		$this->settings         = new Settings();
		$this->saved_searches   = new SavedSearches();
		$this->base_settings    = $this->settings->getBaseSettings();
		$this->display_settings = $this->settings->getDisplaySettings();
		$this->nav_label        = isset($this->base_settings['nav_label']) && ! empty($this->base_settings['nav_label']) ? $this->base_settings['nav_label'] : 'Zenbu';
	}
}