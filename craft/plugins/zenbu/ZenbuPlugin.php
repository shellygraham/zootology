<?php
/**
 * =======
 *  Zenbu
 * =======
 * See more data in your control panel entry listing
 * @copyright   Nicolas Bottari - Zenbu Studio 2011-2015
 * @author      Nicolas Bottari - Zenbu Studio
 * ------------------------------
 *
 * *** IMPORTANT NOTES ***
 * I (Nicolas Bottari and Zenbu Studio) am not responsible for any
 * damage, data loss, etc caused directly or indirectly by the use of this add-on.
 * @license     See the license documentation (text file) included with the add-on.
 *
 * Description
 * -----------
 * Zenbu is a powerful and customizable entry list manager.
 * Zenbu enables you to see, all on the same page:
 * Entry ID, Entry title, Entry date, Author name, all (or a portion of)
 * custom fields for the entry, etc.
 *
 * @link    http://zenbustudio.com/software/zenbu_craftcms/
 * @link    http://zenbustudio.com/software/docs/zenbu_craftcms/
 *
 */

namespace Craft;
require_once __DIR__.'/vendor/autoload.php';

class ZenbuPlugin extends BasePlugin
{
    public function getName()
    {
        return Craft::t('Zenbu');
    }

    public function getVersion()
    {
        return '0.11.1';
    }

    public function getDeveloper()
    {
        return 'Zenbu Studio';
    }

    public function getDeveloperUrl()
    {
        return 'https://zenbustudio.com/software/zenbu-craftcms';
    }
    
    public function hasCpSection()
    {
        return true;
    }

    protected function defineSettings()
    {
        $settings_array = array(
            'nav_label'         => AttributeType::String,
            'redirect_to_zenbu' => AttributeType::Bool,
            'hide_entries_tab' => AttributeType::Bool,
            );

        return $settings_array;
    }

    public function getSettingsHtml()
    {
        $settings = craft()->plugins->getPlugin('zenbu')->getSettings();

        $data['nav_label']         = isset($settings['nav_label']) && ! empty($settings['nav_label']) ? $settings['nav_label'] : 'Zenbu';
        $data['redirect_to_zenbu'] = isset($settings['redirect_to_zenbu']) && ! empty($settings['redirect_to_zenbu']) ? true : false;
        $data['hide_entries_tab']  = isset($settings['hide_entries_tab']) && ! empty($settings['hide_entries_tab']) ? true : false;

        return craft()->templates->render('zenbu/plugin_settings/index', array(
            'settings'                 => $data,
        ));
    }

    /**
     * Init function
     * Gets called on every request, before any events have had a chance to fire.
     * @return void
     */
    public function init()
    {
        if (craft()->request->isCpRequest()) {
            $settings          = craft()->plugins->getPlugin('zenbu')->getSettings();

            //    ----------------------------------------
            //    Change the Nav's "Zenbu" label to the user-set
            //    value, if present. Load JS to change Nav label if 
            //    modifyCpNav hook isn't available (before Craft 2.3.2640)
            //    ----------------------------------------
            if(craft()->request->isAjaxRequest() === FALSE && version_compare(CRAFT_VERSION.'.'.CRAFT_BUILD, '2.3.2640', '<'))
            {
                $data['nav_label']        = isset($settings['nav_label']) && ! empty($settings['nav_label']) ? $settings['nav_label'] : 'Zenbu';
                $data['hide_entries_tab'] = isset($settings['hide_entries_tab']) && ! empty($settings['hide_entries_tab']) ? true : false;
                $js                       = craft()->templates->render('zenbu/global/nav_js', $data);
                craft()->templates->includeJs($js);
            }

            //    ----------------------------------------
            //    Redirect to Zenbu
            //    ----------------------------------------
            if (isset($settings['redirect_to_zenbu']) && ! empty($settings['redirect_to_zenbu'])) {
                craft()->on('entries.saveEntry', function ($event) use ($settings) {

                    $redirect_uris = explode('/', craft()->request->getPost('redirect'));

                    // If the URI has 2 segments, that means "Save & Leave" was selected,
                    // therefore redirect to Zenbu. If there are more than 2 segments,
                    // "Save and continue" and similar options were selected, so don't
                    // redirect to Zenbu.
                    if (count($redirect_uris) <= 2) {
                        $entryId     = $event->params['entry']->id;
                        $sectionId   = '?sectionId='.$event->params['entry']->sectionId;
                        $entryTypeId = isset($event->params['entry']->typeId) ? '&entryTypeId='.$event->params['entry']->typeId : '';

                        craft()->request->redirect(UrlHelper::getCpUrl('zenbu/'.$sectionId.$entryTypeId));
                    }
                });
            }
        }
    } // END init()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to register their own CP routes.
     * @return array An array of CP routes
     */
    public function registerCpRoutes()
    {
        return array(
            'zenbu'                       => array('action' => 'zenbu/index'),
            'zenbu/settings'              => array('action' => 'zenbu/displaySettings/Index'),
            'zenbu/settings/save'         => array('action' => 'zenbu/displaySettings/Save'),
            'zenbu/searches'              => array('action' => 'zenbu/savedSearches/Manage'),
            'zenbu/searches/manage'       => array('action' => 'zenbu/savedSearches/Manage'),
            'zenbu/searches/update'       => array('action' => 'zenbu/savedSearches/Update'),
            'zenbu/searches/save'         => array('action' => 'zenbu/savedSearches/Save'),
            'zenbu/searches/fetchFilters' => array('action' => 'zenbu/savedSearches/FetchFilters'),
        );
    } // END registerCpRoutes()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to modify the Control Panel navigation.
     * @param  array &$nav The CP Nav array
     * @return void
     */
    public function modifyCpNav(&$nav)
    {
        $settings          = craft()->plugins->getPlugin('zenbu')->getSettings();
        
        //    ----------------------------------------
        //    Change the Nav's "Zenbu" label to
        //    the user-set value, if present
        //    ----------------------------------------
        $data['nav_label'] = isset($settings['nav_label']) && ! empty($settings['nav_label']) ? $settings['nav_label'] : 'Zenbu';
        $nav['zenbu']['label'] = $data['nav_label'];

        //    ----------------------------------------
        //    Hide Entries Tab
        //    ----------------------------------------
        if (isset($settings['hide_entries_tab']) && ! empty($settings['hide_entries_tab'])) {
            unset($nav['entries']);
        }
    } // END modifyCpNav()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to register new user permissions. 
     * @return array Additional user permissions
     */
    public function registerUserPermissions()
    {
        if (craft()->request->segments[0] == 'settings' && craft()->request->segments[1] == 'users' && craft()->request->segments[2] == 'groups') {
            return array(
                //'canAccessDisplaySettings' => array('label' => Craft::t('Can access display settings')),
                'canCopyZenbuDisplaySettingsToUsers' => array('label' => Craft::t('Can copy Display Settings to other users')),
            );
        }
    } // END registerUserPermissions()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to add a new Twig extension.
     * @return object New Twig Extension
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.zenbu.twigextensions.ZenbuTwigExtension');

        return new ZenbuTwigExtension();
    } // END addTwigExtension()

    // --------------------------------------------------------------------

}
