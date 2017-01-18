<?php
namespace Craft;

use Zenbu\controllers\ZenbuBaseController as ZenbuBaseController;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\View;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Request;
use Zenbu\libraries;

class Zenbu_SavedSearchesController extends ZenbuBaseController
{
    public function __construct()
    {
        parent::init();
    }
    
    /**
     * Manage Saved Searches
     * @return string Rendered section
     */
    public function actionManage()
    {
        $vars['savedSearches'] = $this->saved_searches->getSavedSearches();
        $vars['nav_label']     = $this->nav_label;
        View::includeCss(array('zenbu/css/font-awesome.min.css', 'zenbu/css/zenbu_main.css'));
        View::includeJs(array('zenbu/js/zenbu_main.js', 'zenbu/js/jquery-ui.min.js', 'zenbu/js/typewatch.js'));
        $this->renderTemplate('zenbu/saved_searches/index', $vars);
    } // END actionManageSavedSearches()

    // --------------------------------------------------------------------

    /**
     * Retrieve Search filters based on ID
     * @return string JSON output
     */
    public function actionFetchFilters()
    {
        $output = $this->saved_searches->getSavedSearchFilters();
        $this->returnJson($output);
    } // END actionFetchFilters()

    // --------------------------------------------------------------------


    /**
     * Save the Saved Search
     * @return string JSON output
     */
    public function actionSave()
    {

        //  ----------------------------------------
        //  Save the search name
        //  ----------------------------------------

        $data['label']        = Request::post('label');
        $data['userId']       = Session::user()->id;

        if (! Request::post('label')) {
            Request::redirect(UrlHelper::getCpUrl('zenbu/searches'));
        }

        $originalSearchList = $this->saved_searches->getSavedSearches();

        $search         = new Zenbu_SavedSearchesRecord;
        $search->label  = Request::post('label', 'Saved Search');
        $search->userId = Session::user()->id;
        $search->order  = count($originalSearchList);
        $search->save();
        $searchId       = $search->id;

        $sectionId      = Request::param('sectionId', 0);
        $entryTypeId    = Request::param(Convert::col('subSectionId'), 0);
        $sectionId      = empty($sectionId) ? 0 : $sectionId;
        $entryTypeId    = empty($entryTypeId) ? 0 : $entryTypeId;


        $c = 0;
        $searchfilter                   = new Zenbu_SavedSearchFiltersRecord;
        $searchfilter->searchId         = $search->id;
        $searchfilter->filterAttribute1 = 'sectionId';
        $searchfilter->filterAttribute2 = 'is';
        $searchfilter->filterAttribute3 = $sectionId;
        $searchfilter->order            = $c;
        $searchfilter->save();
        $c++;

        if ($entryTypeId != 0) {
            $searchfilter                   = new Zenbu_SavedSearchFiltersRecord;
            $searchfilter->searchId         = $search->id;
            $searchfilter->filterAttribute1 = 'entryTypeId';
            $searchfilter->filterAttribute2 = 'is';
            $searchfilter->filterAttribute3 = $entryTypeId;
            $searchfilter->order            = $c;
            $searchfilter->save();
            $c++;
        }

        //  ----------------------------------------
        //  Filters
        //  ----------------------------------------

        $filters = Request::param('filter');

        if (! empty($filters)) {
            foreach ($filters as $key => $val) {
                $searchfilter = new Zenbu_SavedSearchFiltersRecord;
                $searchfilter->searchId         = $search->id;
                $searchfilter->filterAttribute1 = $val['1st'];
                $searchfilter->filterAttribute2 = $val['2nd'];
                $searchfilter->filterAttribute3 = $val['3rd'];
                $searchfilter->order            = $c;
                $searchfilter->save();
                $c++;
            }
        }

        $searchfilter = new Zenbu_SavedSearchFiltersRecord;
        $searchfilter->searchId         = $search->id;
        $searchfilter->filterAttribute1 = 'limit';
        $searchfilter->filterAttribute2 = Request::param('limit', '10');
        $searchfilter->filterAttribute3 = '';
        $searchfilter->order            = $c;
        $searchfilter->save();
        $c++;

        $searchfilter = new Zenbu_SavedSearchFiltersRecord;
        $searchfilter->searchId         = $search->id;
        $searchfilter->filterAttribute1 = 'orderby';
        $searchfilter->filterAttribute2 = Request::param('orderby', 'title');
        $searchfilter->filterAttribute3 = Request::param('sort', 'ASC');
        $searchfilter->order            = $c;
        $searchfilter->save();

        $output['label'] = $data['label'];
        $output['searchId'] = $searchId;
        $output['link'] = HtmlHelper::link($data['label'], UrlHelper::getUrl('zenbu/searches/fetchFilters'), array('data-searchId' => $searchId));

        //  ----------------------------------------
        //  Clear Saved Searches cache
        //  ----------------------------------------

        Cache::delete('Zenbu-SavedSearches-User' . Session::user()->id);

        $this->returnJson($output);

    } // END actionSave()

    // --------------------------------------------------------------------


    /**
     * Update (or delete) Saved searches
     * @return void Redirect
     */
    public function actionUpdate()
    {
        $action   = Request::post('action');

        $search = new Zenbu_SavedSearchesRecord;

        if (! in_array($action, array('delete'))) {
            $searches = Request::post('search');

            if (! Request::post('search')) {
                Request::redirect(UrlHelper::getCpUrl('zenbu/searches'));
            }

            $c = 0;
            foreach ($searches as $searchId => $data) {
                $db_data['label'] = ! empty($data['label']) ? $data['label'] : 'Search';
                $db_data['order'] = $c;
                $search->updateAll($db_data, 'userId = ' . $this->user->id . ' AND id = ' . $searchId);
                $c++;
            }

            Session::setFlash('messageNotice', Lang::t("Saved Searches Updated"));
        } elseif ($action == 'delete') {
            $searchIds = Request::post('searchIds');

            if (! Request::post('searchIds')) {
                Request::redirect(UrlHelper::getCpUrl('zenbu/searches'));
            }

            foreach ($searchIds as $id) {
                $search->deleteAll('id = :id', array(':id' => $id));
            }

            Session::setFlash('messageNotice', Lang::t("Selected Searches have been deleted"));
        }

        //  ----------------------------------------
        //  Clear Saved Searches cache
        //  ----------------------------------------

        Cache::delete('Zenbu-SavedSearches-User' . Session::user()->id);

        Request::redirect(UrlHelper::getCpUrl('zenbu/searches'));

    } // END actionUpdateSavedSearches()

    // --------------------------------------------------------------------
}
