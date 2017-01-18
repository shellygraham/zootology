<?php
namespace Craft;

use Zenbu\controllers\ZenbuBaseController as ZenbuBaseController;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\View;
use Zenbu\frameworks\craft\Request;

class ZenbuController extends ZenbuBaseController
{
	public function __construct()
	{
		parent::init();
	}

	/**
	 * Main page
	 * @return string Rendered template
	 */
	public function actionIndex()
	{
		$selectOptions                      = $this->sections->getSectionSelectOptions();
		$vars['section_dropdown_options']   = $selectOptions['sections'];
		$vars['entryType_dropdown_options'] = $selectOptions['entryTypes'];
		$vars['limit_options']              = $this->filters->getLimitSelectOptions();
		$vars['orderby_options']            = $this->filters->getOrderBySelectOptions();
		$vars['sort_options']               = $this->filters->getSortSelectOptions();
		$vars['firstFilterOptions']         = $this->filters->getFirstFilterOptions();
		$vars['secondFilterOptions']        = $this->filters->getSecondFilterOptions();
		$vars['fields']           = $fields = $this->fields->getFields();
		$vars['sections']                   = $this->sections->getSections();
		$vars['savedSearches']              = $this->saved_searches->getSavedSearches();
		$vars['savedSearch']                = $this->saved_searches->getSavedSearchFilters();
		$vars['storedFilterData']           = array();

		//	----------------------------------------
		//	If this is not a saved search request, or
		//	there is no saved search to retrieve,
		//	try getting filters from DB cache, if
		//	there's anything available
		//	----------------------------------------
		if( empty($vars['savedSearch']) )
		{
			// Comment out the following line if constant cache retrieval is being bothersome
			$vars['storedFilterData'] = Cache::get('zenbu_filter_cache_'.$this->user->id);
		}

		$vars['settings'] = $this->display_settings;

		//	----------------------------------------
		//	Order "show" fields first
		//	----------------------------------------
		$orderedFields = $this->fields->getOrderedFields();

		$vars['columns'] = empty($orderedFields) ? $vars['fields'] : $orderedFields;
		$vars['nav_label'] = $this->nav_label;

		if(craft()->request->getIsPostRequest() !== FALSE)
		{
			$this->filters->cacheFilters();
			$this->renderTemplate('zenbu/main/results', $vars);
		}
		else
		{
			View::includeCss(array('zenbu/css/font-awesome.min.css', 'zenbu/css/zenbu_main.css'));
			View::includeJs(array('zenbu/js/zenbu_main.js', 'zenbu/js/jquery-ui.min.js', 'zenbu/js/typewatch.js'));
			$this->renderTemplate('zenbu/main/index', $vars);
		}

	} // END actionIndex()

	// --------------------------------------------------------------------


    /**
     * Delete Entries
     * @return void Redirect
     */
    public function actionDeleteEntries()
    {
    	$elementIds        = craft()->request->getRequiredPost('elementIds');

    	if( $elementIds )
    	{
    		foreach($elementIds as $entryId)
    		{
    	 		$response = craft()->entries->deleteEntryById($entryId);
    		}
    	}

    	Session::setFlash('messageNotice', Lang::t("Entries Deleted"));

    	craft()->request->redirect(UrlHelper::getCpUrl('zenbu'));
    } // END actionDeleteEntries()

    // --------------------------------------------------------------------

}