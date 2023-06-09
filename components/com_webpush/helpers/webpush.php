<?php

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/../vendor/autoload.php';

abstract class WebPushHelper
{
	/**
	 * @throws ErrorException
	 */
	public static function sendMessages(array $subscriptions, string $title, ?string $message = null, array $payload = null): array
	{
		$webPush       = self::getWebPush();
		foreach ($subscriptions as $subscription)
		{
			$webPush->sendNotification(self::createSubscription($subscription), json_encode([
				'body' => $message,
				'title'   => $title,
				'payload' => $payload,
				'icon'    => '/approved-icon.png',
			]));
		}

		return self::checkForResponse($webPush->flush());
	}

	/**
	 * @throws ErrorException
	 */
	protected static function getWebPush(): WebPush
	{
		$config = (new WebPushModel)->getConfig();

		return new WebPush([
			'VAPID' => [
				'subject'    => 'mailto:mweadennis2@gmail.com',
				'publicKey'  => $config['web_push_public_key']->value,
				'privateKey' => $config['web_push_private_key']->value,
			],
		], [
			'TTL'       => 1000,
			'batchSize' => 200,
			'urgency'   => 'high',
			'topic'     => 'newEvent',
		]);
	}

	/**
	 * @throws ErrorException
	 */
	protected static function createSubscription(stdClass $subscription): Subscription
	{
		return Subscription::create([
			'endpoint'  => $subscription->endpoint,
			'publicKey' => $subscription->public_key,
			'authToken' => $subscription->auth_token,
		]);
	}

	protected static function checkForResponse(iterable $reports): array
	{
		$messages = [];
		foreach ($reports as $report)
		{
			$messages[] = $report->isSuccess()
				? "[v] Message sent successfully for subscription {$report->getEndpoint()}."
				: "[x] Message failed to sent for subscription {$report->getEndpoint()}: {$report->getReason()}";
		}

		return $messages;
	}
}
