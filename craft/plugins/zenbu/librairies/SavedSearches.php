<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\librairies\Fields;

class SavedSearches
{
    var $fields;

    public function __construct()
    {
        $this->fields = new Fields();
    }

    /**
     * Retrieve saved searches
     * @return array
     */
    public function getSavedSearches()
    {
        if(Cache::get('Zenbu-SavedSearches-User' . Session::user()->id) !== FALSE)
        {
            return Cache::get('Zenbu-SavedSearches-User' . Session::user()->id);
        }

        $sql = 'SELECT * FROM zenbu_saved_searches
                WHERE userId = ' . Session::user()->id . '
                ORDER BY `order` ASC';

        $results = Db::rawQuery($sql);

        $output = array();

        if(count($results > 0))
        {
            foreach($results as $row)
            {
                $output[] = $row;
            }
        }

        Cache::set('Zenbu-SavedSearches-User' . Session::user()->id, $output, 10);

        return $output;

    } // END getSavedSearches()

    // --------------------------------------------------------------------


    public function getSavedSearchFilters()
    {
        $searchId = Request::param('searchId');

        if( ! empty($searchId) && ctype_digit($searchId) )
        {
            $sql = 'SELECT ssf.* 
                    FROM zenbu_saved_search_filters ssf
                    JOIN zenbu_saved_searches ss ON ss.id = ssf.searchId
                    WHERE ssf.searchId = ' . $searchId . '
                    AND ss.userId = ' . Session::user()->id . '
                    ORDER BY `order` ASC';

            $results = Db::rawQuery($sql);

            $output = array();

            if(count($results > 0))
            {
                foreach($results as $row)
                {
                    $output[] = $row;
                }
            }

            return $output;
        }
        else
        {
            return array();
        }
    } // END getSavedSearchFilters()

    // --------------------------------------------------------------------
}