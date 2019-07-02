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
    $api_id = $this->state->get('sms_smsru.api_id');

    $form['api_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('API settings'),
      '#open' => TRUE,
    ];

    if (isset($api_id)) {
      $api_id_pieces = explode('-', $api_id);
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
      ];
    }

    $form['api_settings']['api_id'] = [
      '#type' => 'password',
      '#title' => $this->t('API ID'),
      '#description' => $this->t('You can find your API ID on sms.ru <a href="@link">API page</a>.', [
        '@link' => 'https://sms.ru/?panel=api',
      ]),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $api_id = $this->state->get('sms_smsru.api_id');

    if (empty($api_id) && empty($form_state->getValue('api_id'))) {
      $message = $this->t('The API ID is required.');
      $form_state->setErrorByName('api_id', $message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    if (!empty($form_state->getValue('api_id'))) {
      $this->state->set('sms_smsru.api_id', $form_state->getValue('api_id'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function send(SmsMessageInterface $sms): SmsMessageResultInterface {
    $result = new SmsMessageResult();
    $reports = [];

    foreach ($sms->getRecipients() as $recipient) {
      // ...Send message with API
      $reports[] = (new SmsDeliveryReport())
        ->setRecipient($recipient)
        ->setStatus(SmsMessageReportStatus::DELIVERED);
    }

    $result->setReports($reports);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreditsBalance(): ?float {
    return 123;
  }

}
