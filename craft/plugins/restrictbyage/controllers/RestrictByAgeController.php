<?php

namespace Craft;

class RestrictByAgeController extends BaseController
{   

	/**
	 * @var Allows anonymous access to this controller's actions.
	 * @access protected
	 */
	protected $allowAnonymous = true;

    public function actionOver21() 
    {
        $this->requirePostRequest();
        $this->requireAjaxRequest();
        
        $session = craft()->getSession();
        $session['over21'] = true;

        $this->returnJson(array('success' => true));
    }
}