<?php
namespace Craft;

use Zenbu\controllers\ZenbuBaseController as ZenbuBaseController;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\View;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Convert;

class Zenbu_DisplaySettingsController extends ZenbuBaseController
{
	public function __construct()
	{
		parent::init();
	}


	/**
	 * Display Settings
	 * @return string Rendered template
	 */
    public function actionIndex()
    {
		$fields             = $this->fields->getFields();
		$vars['settings']   = $this->settings->getDisplaySettings('all');
		$vars['nav_label']  = $this->nav_label;
		$vars['userGroups'] = ! empty(craft()->userGroups) ? craft()->userGroups->getAllGroups() : array();

		//	----------------------------------------
		//	Get rid of Zenbu filter cache
		//	This avoids sectionId/entryTypeId mismatch if
		//	returning to main Zenbu page
		//	----------------------------------------
		Cache::delete('zenbu_filter_cache_'.$this->user->id);

		//	----------------------------------------
		//	Order "show" fields first
		//	----------------------------------------
		$vars['rows'] = $this->fields->getOrderedFields(TRUE);

		$selectOptions = $this->sections->getSectionSelectOptions();
		$vars['section_dropdown_options']   = $selectOptions['sections'];
		$vars['entryType_dropdown_options'] = $selectOptions['entryTypes'];

		if(craft()->request->getIsPostRequest() !== FALSE)
		{
			$this->renderTemplate('zenbu/display_settings/settings', $vars);
		}
		else
		{
			View::includeCss(array('zenbu/css/font-awesome.min.css', 'zenbu/css/zenbu_main.css'));
			View::includeJs(array('zenbu/js/zenbu_main.js', 'zenbu/js/jquery-ui.min.js', 'zenbu/js/typewatch.js'));
			$this->renderTemplate('zenbu/display_settings/index', $vars);
		}
    } // END actionIndex()

    // --------------------------------------------------------------------


    public function actionSave()
    {
		$fields        = Request::post('field');
		$fieldsettings = Request::post('fieldsettings');
		$applyTo       = Request::post('applyTo');

		$sectionId     = Request::param('sectionId', 0);
		$entryTypeId   = Request::param('entryTypeId', 0);
		$sectionId     = empty($sectionId) ? 0 : $sectionId;
		$entryTypeId   = empty($entryTypeId) ? 0 : $entryTypeId;
		$c             = 1;

		$display_settings = new Zenbu_DisplaySettingsRecord;
    	$display_settings->deleteAll('(sectionId IS NULL OR sectionId = ?) AND (entryTypeId IS NULL OR entryTypeId = ?) AND userId = ?', array(
    			$sectionId,
    			$entryTypeId,
    			$this->user->id
			)
		);

		$users = array();

    	if(isset($applyTo['all']) && $applyTo['all'] != 1)
    	{
	    	foreach($applyTo as $groupId => $checked)
	    	{
	    		if($checked == 1 && $groupId != 'all')
	    		{
					$criteria          = craft()->elements->getCriteria(ElementType::User);
					$criteria->groupId = $groupId;
					$users[$groupId]             = $criteria->find();

	    			foreach($users as $groupId => $user_list)
	    			{
	    				foreach($user_list as $user)
	    				{
		    				if($user['id'] != $this->user->id)
	    					{
								$ds_delete = new Zenbu_DisplaySettingsRecord;
						    	$ds_delete->deleteAll('(sectionId IS NULL OR sectionId = ?) AND (entryTypeId IS NULL OR entryTypeId = ?) AND userId = ?', array(
						    			$sectionId,
						    			$entryTypeId,
						    			$user['id']
									)
								);
								unset($ds_delete);
				    		}
				    	}
	    			}
	    		}
	    	}
	    }
	    elseif(isset($applyTo['all']) && $applyTo['all'] == 1)
	    {
			$criteria = craft()->elements->getCriteria(ElementType::User);
			$users[]    = $criteria->find();

	        foreach($users as $groupId => $user_list)
    		{
    			foreach($user_list as $user)
    			{
	    			if($user['id'] != $this->user->id)
	    			{
						$ds_delete = new Zenbu_DisplaySettingsRecord;
				    	$ds_delete->deleteAll('(sectionId IS NULL OR sectionId = ?) AND (entryTypeId IS NULL OR entryTypeId = ?) AND userId = ?', array(
				    			$sectionId,
				    			$entryTypeId,
				    			$user['id']
							)
						);
						unset($ds_delete);
		    		}
		    	}
    		}
    	}

		if( ! $fields )
		{
			Request::redirect(UrlHelper::getCpUrl('zenbu/settings'));
		}

    	foreach($fields as $key => $setting)
    	{
    		foreach($setting as $handle => $show)
    		{
				$display_settings              = new Zenbu_DisplaySettingsRecord;
				$display_settings->userId      = $this->user->id;
				$display_settings->userGroupId = 0;
				$display_settings->sectionId   = $sectionId;
				$display_settings->entryTypeId = $entryTypeId;
				$display_settings->fieldType   = is_integer($handle) ? 'field' : $handle;
				$display_settings->fieldId     = is_integer($handle) ? $handle : 0;
				$display_settings->order       = $c;
				$display_settings->show        = empty($show) ? '0' : '1';
				$display_settings->settings    = isset($fieldsettings[$handle]) ? $fieldsettings[$handle] : NULL;
		    	$display_settings->save();
		    	unset($display_settings);

    			foreach($users as $groupId => $user_list)
    			{
    				foreach($user_list as $user)
    				{
	    				if($user['id'] != $this->user->id)
						{
		    				$display_settings              = new Zenbu_DisplaySettingsRecord;
							$display_settings->userId      = $user['id'];
							$display_settings->userGroupId = 0;
							$display_settings->sectionId   = $sectionId;
							$display_settings->entryTypeId = $entryTypeId;
							$display_settings->fieldType   = is_integer($handle) ? 'field' : $handle;
							$display_settings->fieldId     = is_integer($handle) ? $handle : 0;
							$display_settings->order       = $c;
							$display_settings->show        = empty($show) ? '0' : '1';
							$display_settings->settings    = isset($fieldsettings[$handle]) ? $fieldsettings[$handle] : NULL;
			    			$display_settings->save();
			    			unset($display_settings);
			    		}
			    	}
    			}

	    		$c++;
    		}
    	}

		Session::setFlash('messageNotice', Lang::t("Display Settings Saved"));

    	Request::redirect(UrlHelper::getCpUrl('zenbu/settings?sectionId='.Request::param('sectionId', 0).'&entryTypeId='.Request::param('entryTypeId', 0)));
    } // END actionSave()

    // --------------------------------------------------------------------

}