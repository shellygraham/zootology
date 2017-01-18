<?php
namespace Craft;

/**
 * Contact Form controller
 */
class ContactFormController extends BaseController
{
	/**
	 * @var Allows anonymous access to this controller's actions.
	 * @access protected
	 */
	protected $allowAnonymous = true;


	/**
	 * Sends an email based on the posted params.
	 *
	 * @throws Exception
	 */
	public function actionSendMessage()
	{
		$this->requirePostRequest();

		$settings = craft()->plugins->getPlugin('contactform')->getSettings();

		$message = new ContactFormModel();
		$savedBody = false;

		$message->fromEmail  = craft()->request->getPost('fromEmail');
		$message->fromName	 = craft()->request->getPost('fromName');
		$message->subject    = craft()->request->getPost('subject');
		$message->attachment = \CUploadedFile::getInstanceByName('attachment');

		// Set the message body
		$postedMessage = craft()->request->getPost('message');

		if ($postedMessage)
		{
			if (is_array($postedMessage))
			{
				$savedBody = false;

				if (isset($postedMessage['body']))
				{
					// Save the message body in case we need to reassign it in the event there's a validation error
					$savedBody = $postedMessage['body'];
				}

				// If it's false, then there was no messages[body] input submitted.  If it's '', then validation needs to fail.
				if ($savedBody === false || $savedBody !== '')
				{
					// Compile the message from each of the individual values
					$compiledMessage = '';

					$compiledMessage .= 'From: ' . $message->fromName . ' (' . $message->fromEmail . ')';

					foreach ($postedMessage as $key => $value)
					{
						if ($key != 'body')
						{
							if ($compiledMessage)
							{
								$compiledMessage .= "\n\n";
							}

							$compiledMessage .= $key.': ';

							if (is_array($value))
							{
								$compiledMessage .= implode(', ', $value);
							}
							else
							{
								$compiledMessage .= $value;
							}
						}
					}

					if (!empty($postedMessage['body']))
					{
						if ($compiledMessage)
						{
							$compiledMessage .= "\n\n";
						}

						$compiledMessage .= $postedMessage['body'];
					}

					$message->message = $compiledMessage;
				}
			}
			else
			{
				$message->message = $postedMessage;
			}
		}	

		if ($message->validate())
		{
			// Only actually send it if the honeypot test was valid
			if (!$this->validateHoneypot($settings->honeypotField) || craft()->contactForm->sendMessage($message))
			{
				// check for mail list subscribe
				if ($postedMessage && (is_array($postedMessage))) {
					if (array_key_exists('Email Opt In', $postedMessage) && $postedMessage['Email Opt In'] == 'yes') {
						require_once(craft()->path->getPluginsPath().'contactform/vendor/Drewm/MailChimp.php');
		 
					    $api_key = "415b73413d39e3173daf2e9e233e51cc-us8"; //replace with your API key
					    $list_id = "f1d5e2f377"; //replace with the list id you're adding the email to


						$MailChimp = new \Drewm\MailChimp($api_key);
						$result = $MailChimp->call('lists/subscribe', array(
					                'id'                => $list_id,
					                'email'             => array('email'=>$message->fromEmail),
					                'merge_vars'        => array(),
					                'double_optin'      => false,
					                'update_existing'   => true,
					                'replace_interests' => false,
					                'send_welcome'      => false,
					            ));
					}
				}

				if (craft()->request->isAjaxRequest())
				{
					$this->returnJson(array('success' => true));
				}
				else
				{
					// Deprecated. Use 'redirect' instead.
					$successRedirectUrl = craft()->request->getPost('successRedirectUrl');

					if ($successRedirectUrl)
					{
						$_POST['redirect'] = $successRedirectUrl;
					}

					craft()->userSession->setNotice('Your message has been sent.');
					$this->redirectToPostedUrl($message);
				}
			}
		}

		// Something has gone horribly wrong.
		if (craft()->request->isAjaxRequest())
		{
			return $this->returnErrorJson($message->getErrors());
		}
		else
		{
			craft()->userSession->setError('There was a problem with your submission, please check the form and try again!');

			if ($savedBody !== false)
			{
				$message->message = $savedBody;
			}

			craft()->urlManager->setRouteVariables(array(
				'message' => $message
			));
		}
	}

	/**
	 * Checks that the 'honeypot' field has not been filled out (assuming one has been set).
	 *
	 * @param string $fieldName The honeypot field name.
	 * @return bool
	 */
	protected function validateHoneypot($fieldName)
	{
		if (!$fieldName)
		{
			return true;
		}

		$honey = craft()->request->getPost($fieldName);
		return $honey == '';
	}
}
