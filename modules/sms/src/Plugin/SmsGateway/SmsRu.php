<?php

namespace Drupal\smsru_sms\Plugin\SmsGateway;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\State\StateInterface;
use Drupal\sms\Message\SmsDeliveryReport;
use Drupal\sms\Message\SmsMessageInterface;
use Drupal\sms\Message\SmsMessageReportStatus;
use Drupal\sms\Message\SmsMessageResult;
use Drupal\sms\Message\SmsMessageResultInterface;
use Drupal\sms\Plugin\SmsGatewayPluginBase;
use Drupal\smsru\Auth\ApiIdAuth;
use Drupal\smsru\Auth\LoginPasswordAuth;
use Drupal\smsru\Client\HttpClient;
use Drupal\smsru\Message\Message;
use Drupal\smsru\SmsRu as SmsRuApi;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SMS.ru gateway plugin.
 *
 * @SmsGateway(
 *   id = "smsru_sms",
 *   label = @Translation("SMS.ru"),
 *   credit_balance_available = TRUE,
 * )
 */
class SmsRu extends SmsGatewayPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The authentication method using API ID.
   */
  const AUTH_API_ID = 'api_id';

  /**
   * The authentication method using login and password.
   */
  const AUTH_LOGIN_PASS = 'login_pass';

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a new SmsRu object.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $auth_settings = $this->state->get('sms_smsru.auth_settings');

    $form['api_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('API settings'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['api_settings']['auth_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Authentication method'),
      '#required' => TRUE,
      '#options' => [
        self::AUTH_API_ID => $this->t('API ID (recommended)'),
        self::AUTH_LOGIN_PASS => $this->t('Login and password'),
      ],
      '#default_value' => isset($auth_settings['auth_type']) ? $auth_settings['auth_type'] : self::AUTH_API_ID,
    ];

    // API ID.
    if (isset($auth_settings['api_id'])) {
      $api_id_pieces = explode('-', $auth_settings['api_id']);
      $api_id_last_piece = array_pop($api_id_pieces);
      $api_id_masked_pieces = [];

      foreach ($api_id_pieces as $api_id_part) {
        $api_id_masked_pieces[] = preg_replace("/[a-zA-Z0-9]/", '*', $api_id_part);
      }

      $api_id_masked_pieces[] = $api_id_last_piece;
      $api_id_masked = implode('-', $api_id_masked_pieces);

      $form['api_settings']['api_id_current'] = [
        '#type' => 'item',
        '#title' => $this->t('Current API ID'),
        '#plain_text' => $api_id_masked,
        '#states' => [
          'visible' => $this->isAuthTypeIsApiId(),
        ],
      ];
    }

    $form['api_settings']['api_id'] = [
      '#type' => 'password',
      '#title' => $this->t('API ID'),
      '#description' => $this->t('You can find your API ID on sms.ru <a href="@link">API page</a>.', [
        '@link' => 'https://sms.ru/?panel=api',
      ]),
      '#states' => [
        'visible' => $this->isAuthTypeIsApiId(),
      ],
    ];

    // Login and Password.
    $form['api_settings']['login'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Login'),
      '#states' => [
        'invisible' => $this->isAuthTypeIsApiId(),
      ],
      '#default_value' => isset($auth_settings['login']) ? $auth_settings['login'] : '',
    ];

    $form['api_settings']['pass'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#states' => [
        'invisible' => $this->isAuthTypeIsApiId(),
      ],
    ];

    $form['api_settings']['test_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Testing mode'),
      '#description' => $this->t('Check the box if you want the messages to be sent in test mode. You will be able to see the messages in your SMS.ru account.'),
      '#default_value' => isset($auth_settings['test_mode']) ? $auth_settings['test_mode'] : FALSE,
    ];

    $form['api_settings']['forget_credentials'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Forget credentials'),
      '#description' => $this->t('Check this to remove API ID and password from storage on submit.'),
    ];

    return $form;
  }

  /**
   * Gets state for selected auth_type api_id.
   *
   * @return array
   *   The array with state query.
   */
  protected function isAuthTypeIsApiId(): array {
    return [
      ':input[name="api_settings[auth_type]"]' => [
        'value' => 'api_id',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $auth_settings = $this->state->get('sms_smsru.auth_settings');
    $auth_settings_submitted = $form_state->getValue('api_settings');

    switch ($form_state->getValue('auth_type')) {
      case self::AUTH_API_ID:
        if (!$auth_settings_submitted['forget_credentials'] && (empty($auth_settings['api_id']) && empty($form_state->getValue('api_id')))) {
          $message = $this->t('The API ID is required.');
          $form_state->setErrorByName('api_id', $message);
        }
        break;

      case self::AUTH_LOGIN_PASS:
        if (empty($auth_settings['login']) && empty($form_state->getValue('login'))) {
          $message = $this->t('The login is required.');
          $form_state->setErrorByName('login', $message);
        }

        if (!$auth_settings_submitted['forget_credentials'] && (empty($auth_settings['pass']) && empty($form_state->getValue('pass')))) {
          $message = $this->t('The password is required.');
          $form_state->setErrorByName('pass', $message);
        }
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $auth_settings = $this->state->get('sms_smsru.auth_settings', [
      'pass' => '',
      'api_id' => '',
    ]);
    $auth_settings_submitted = $form_state->getValue('api_settings');

    if ($auth_settings_submitted['forget_credentials']) {
      $auth_settings_submitted['pass'] = '';
      $auth_settings_submitted['api_id'] = '';
    }
    else {
      if (empty($auth_settings_submitted['pass'])) {
        $auth_settings_submitted['pass'] = $auth_settings['pass'];
      }

      if (empty($auth_settings_submitted['api_id'])) {
        $auth_settings_submitted['api_id'] = $auth_settings['api_id'];
      }
    }

    // Remove this from being stored.
    unset($auth_settings_submitted['forget_credentials']);

    $this->state->set('sms_smsru.auth_settings', $auth_settings_submitted);
  }

  /**
   * {@inheritdoc}
   */
  public function send(SmsMessageInterface $sms): SmsMessageResultInterface {
    $smsru = $this->initSmsRuApi();
    $result = new SmsMessageResult();
    $reports = [];

    foreach ($sms->getRecipients() as $recipient) {
      $message = new Message($recipient, $sms->getMessage());
      if ($sender = $sms->getSender()) {
        $message->setFrom($sender);
      }

      if ($this->state->get('sms_smsru.auth_settings')['test_mode']) {
        $message->setTest(TRUE);
      }

      $response = $smsru->smsSend($message);

      if ($response->getStatusCode() == 100) {
        $sms_status = SmsMessageReportStatus::QUEUED;
      }
      else {
        $sms_status = SmsMessageReportStatus::ERROR;
      }

      $data = $response->getData();
      $sms_info = reset($data['sms']);

      $reports[] = (new SmsDeliveryReport())
        ->setMessageId($sms_info['sms_id'])
        ->setRecipient($recipient)
        ->setStatus($sms_status);
    }

    $result->setReports($reports);

    return $result;
  }

  /**
   * Initialize SmsRu API object.
   *
   * @return \Drupal\smsru\SmsRu
   *   The SMS.ru API object.
   */
  protected function initSmsRuApi(): SmsRuApi {
    $auth_settings = $this->state->get('sms_smsru.auth_settings');

    switch ($auth_settings['auth_type']) {
      case self::AUTH_API_ID:
        $smsru_auth = new ApiIdAuth($auth_settings['api_id']);
        break;

      case self::AUTH_LOGIN_PASS:
        $smsru_auth = new LoginPasswordAuth($auth_settings['login'], $auth_settings['pass']);
        break;
    }

    $http_client = new HttpClient($smsru_auth);

    return new SmsRuApi($http_client);
  }

  /**
   * {@inheritdoc}
   */
  public function getCreditsBalance(): ?float {
    $smsru = $this->initSmsRuApi();
    $response = $smsru->myBalance();
    if ($response->getStatusCode() == 100) {
      return $response->getData()['balance'];
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getDeliveryReports(array $message_ids = NULL): array {
    $smsru = $this->initSmsRuApi();
    $reports = [];

    foreach ($message_ids as $message_id) {
      $response = $smsru->smsStatus($message_id);

      if ($response->getStatus() != 'OK') {
        continue;
      }

      $data = $response->getData();
      $sms_info = reset($data['sms']);

      switch ($sms_info['status_code']) {
        case '100':
        case '101':
        case '102':
          $sms_status = SmsMessageReportStatus::QUEUED;
          break;

        case '103':
          $sms_status = SmsMessageReportStatus::DELIVERED;
          break;

        case '104':
          $sms_status = SmsMessageReportStatus::EXPIRED;
          break;

        case '105':
        case '106':
        case '107':
        case '108':
          $sms_status = SmsMessageReportStatus::REJECTED;
          break;

        case '150':
          $sms_status = SmsMessageReportStatus::INVALID_RECIPIENT;
          break;

        case '203':
          $sms_status = SmsMessageReportStatus::CONTENT_INVALID;
          break;

        default:
          $sms_status = SmsMessageReportStatus::ERROR;
          break;
      }

      $reports[] = (new SmsDeliveryReport())
        ->setStatus($sms_status);
    }

    return $reports;
  }

}
