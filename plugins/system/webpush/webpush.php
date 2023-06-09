<?php

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.environment.browser');

require_once JPATH_ROOT . "/components/com_webpush/models/webpush.php";

class plgSystemWebpush extends JPlugin
{
	/**
	 * On after route.
	 *
	 * @throws Exception
	 */
	public function onBeforeCompileHead(): void
	{
		$app = JFactory::getApplication();
		$browser = JBrowser::getInstance()->getBrowser();
		if (in_array($browser, ['chrome', 'mozilla', 'opera', 'firefox', 'safari']) && $app->isClient('site')) {
			$config = (new WebPushModel())->getConfig();
			if (isset($config['web_push_public_key'], $config['web_push_private_key'])) {
				$jsVars = "var webPushPublicKey = '{$config['web_push_public_key']->value}';";

				$document = JFactory::getDocument();
				$document->addScriptDeclaration($jsVars);
				$this->copyServiceWorker();
//				JHtml::script(JUri::root() . 'plugins/system/webpush/assets/webpush.js');
				JHtml::stylesheet(JUri::root() . 'plugins/system/webpush/assets/webpush.css');
			}
		}
	}

	private function copyServiceWorker(): void
	{
		$base = JPATH_ROOT;
		$serviceWorker = 'webpush-sw.js';
		JFile::copy("$base/plugins/system/webpush/assets/$serviceWorker", "$base/$serviceWorker");
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication();
		if ($app->isClient('site'))
		{
			$body = $app->getBody();
			$user = JFactory::getUser();
			if (!is_null($user) && $user->id != 0)
			{
				$body .= "
					<div id='permissionsModal' class='modal permissions-modal'>
						<div class='modal-content'>
							<div class='modal-header'>
								<span class='close'>&times;</span>
								<h2>Notification permissions request</h2>
							</div>
					
							<div class='modal-body'>
								<p>{$app->get('sitename')} would like to send you notifications. Click allow to accept.</p>
							</div>
					
							<div class='modal-footer'>
								<button class='btn btn-primary allow'>Allow</button>
							</div>
						</div>
					</div>
				";
				$app->setBody($body);
			}
		}
	}
}
