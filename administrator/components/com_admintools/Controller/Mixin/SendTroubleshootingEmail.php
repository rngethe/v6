<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller\Mixin;

use Akeeba\AdminTools\Admin\Model\ConfigureWAF;
use FOF30\Container\Container;
use JFactory;

defined('_JEXEC') or die;

trait SendTroubleshootingEmail
{
	/**
	 * Sends a preemptive troubleshooting email to the user before taking an action which might lock them out.
	 *
	 * @param   string  $controllerName
	 *
	 * @return  void
	 */
	protected function sendTroubelshootingEmail($controllerName)
	{
		// Is sending this email blocked in the WAF configuration?
		/** @var Container $container */
		$container = $this->container;
		/** @var ConfigureWAF $configModel */
		$configModel = $container->factory->model('ConfigureWAF')->tmpInstance();
		$wafConfig   = $configModel->getConfig();
		$sendEmail   = isset($wafConfig['sendTroubleshootingEmail']) ? $wafConfig['sendTroubleshootingEmail'] : 1;

		if (!$sendEmail)
		{
			return;
		}

		// Construct the email
		$user      = $container->platform->getUser();
		$config    = $container->platform->getConfig();
		$siteName  = $config->get('sitename');
		$actionKey = 'COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_ACTION_' . $controllerName;
		$action    = \JText::_($actionKey);
		$subject   = \JText::_('COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_SUBJECT');
		$body      = \JText::sprintf('COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_BODY_HELLO', $user->name) . "\n\n" .
			\JText::sprintf('COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_BODY_DESCRIPTION', $action, $siteName) . "\n\n" .
			"-  http://akee.ba/lockedout\n" .
			"-  http://akee.ba/500htaccess\n" .
			"-  http://akee.ba/adminpassword\n" .
			"-  http://akee.ba/403edituser\n\n" .
			\JText::_('COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_BODY_SUPPORT') . "\n\n" .
			\JText::_('COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_BODY_WHOSENTTHIS') . "\n" .
			str_repeat('=', 40) . "\n\n" .
			\JText::_('COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_BODY_WHOSENT_1') . "\n\n" .
			\JText::_('COM_ADMINTOOLS_TROUBLESHOOTEREMAIL_BODY_WHOSENT_2') . "\n";
		$body      = wordwrap($body);

		// Can't send email if I don't about this controller
		if ($action == $actionKey)
		{
			return;
		}

		// Is the Super User set up to not receive system emails?
		if (!$user->sendEmail)
		{
			return;
		}

		// Send the email
		try
		{
			$mailer    = JFactory::getMailer();
			$mailfrom  = $config->get('mailfrom');
			$fromname  = $config->get('fromname');
			$recipient = trim($user->email);

			// The priority is required because SpamAssassin rejects email without a priority (WTF, right?).
			$mailer->Priority = 3;
			$mailer->isHtml(false);
			$mailer->setSender([$mailfrom, $fromname]);
			$mailer->clearAllRecipients();

			if ($mailer->addRecipient($recipient) === false)
			{
				// Failed to add a recipient?
				return;
			}

			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->Send();
		}
		catch (\Exception $e)
		{
			// Joomla! 3.5 and later throw an exception when crap happens instead of suppressing it and returning false
		}
	}
}