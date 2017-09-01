<?php

namespace Drupal\fcm\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\fcm\FcmMessage;

/**
 * Class FcmController.
 *
 * @package Drupal\fcm\Controller
 */
class FcmController extends ControllerBase {

  /**
   * The FCM message service.
   * @var \Drupal\fcm\FcmMessage
   */
  protected $fcmMessage;

  /**
   * The FCM settings
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $fcmSettings;

  /**
   * Logger object.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * FcmController constructor.
   *
   * @param \Drupal\fcm\FcmMessage $fcm_message
   *   The FCM message service.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger object.
   */
  public function __construct(
    FcmMessage $fcm_message,
    ConfigFactory $config_factory,
    LoggerChannelFactoryInterface $logger
  ) {
    $this->fcmMessage = $fcm_message;
    $this->fcmSettings = $config_factory->get('fcm.settings');
    $this->logger = $logger->get('fcm');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('fcm.message'),
      $container->get('config.factory'),
      $container->get('logger.factory')
    );
  }

  public function validateTokenRegisterRequest(AccountInterface $account) {
    // Server key is required to validate the key.
    if (!$this->fcmSettings->get('server_key')) {
      return AccessResult::forbidden('Serve key is not set.');
    }

    $request = \Drupal::request();
    $fcm_token = $request->getContent();

    // Token is missing, fail validation.
    if (!$fcm_token) {
      return AccessResult::forbidden('User token is null.');
    }

    // Send a fake request message with 'dry_run' parameter.
    $request_response = $this->fcmMessage->pushMessage($fcm_token, [], ['dry_run' => TRUE]);

    // TODO: Test the request. @seee https://stackoverflow.com/questions/41552610/how-to-verify-fcm-registration-token-on-server.

//    return AccessResult::allowedIf($key == $token);
  }

  /**
   * Register the device generated token.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Request object.
   *
   * @return array
   *   Render array.
   */
  public function registerToken(Request $request) {
    $fcm_token = $request->getContent();


  }


}
