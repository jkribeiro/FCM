<?php

namespace Drupal\fcm;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Component\Serialization\Json;

/**
 * Class FcmMessage.
 *
 * @package Drupal\fcm
 */
class FcmMessage {

  const FCM_SEND_MESSAGE_ENDPOINT = 'https://fcm.googleapis.com/fcm/send';

  /**
   * The FCM settings
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $fcmSettings;

  /**
   * The HTTP client.
   *
   * @var \Drupal\Core\Http\ClientFactory
   */
  protected $httpClient;

  /**
   * Current user object.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Logger object.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * Server Key.
   *
   * @var string
   */
  protected $serverKey;

  /**
   * FcmMessage constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   * @param \Drupal\Core\Http\ClientFactory $http_client
   *   The client factory.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user object.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger object.
   */
  public function __construct(
    ConfigFactory $config_factory,
    ClientFactory $http_client,
    AccountProxyInterface $current_user,
    LoggerChannelFactoryInterface $logger
  ) {
    $this->fcmSettings = $config_factory->get('fcm.settings');
    $this->serverKey = $this->fcmSettings->get('server_key');
    $this->httpClient = $http_client->fromOptions();
    $this->currentUser = $current_user;
    $this->logger = $logger->get('fcm');
  }

  /**
   * Pushes a message to a device.
   *
   * This method makes a request to FCM to push a message to the device.
   *
   * @param string $token
   *   The FCM user valid token.
   * @param array $notification
   *   The notification array.
   * @param array $params
   *   An array of parameters (optional) to send to the request.
   *   @see https://firebase.google.com/docs/cloud-messaging/http-server-ref#send-downstream
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   The request response.
   */
  public function pushMessage($token, array $notification, array $params = []) {
    // @TODO: How pass the notification?

    if (!$this->serverKey) {
      $this->logger->error('Unable to push a message. The server key is no defined.');

      return NULL;
    }

    $payload = [
      'notification' => $notification,
      'to' => $token,
    ];

    $payload = $payload + $params;

    $response = $this->httpClient->post($this::FCM_SEND_MESSAGE_ENDPOINT, [
      'body' => Json::encode($payload),
      'headers' => [
        'Authorization' => "key=$this->serverKey",
        'Content-Type' => 'application/json',
      ],
    ]);

    return $response;
  }

}
