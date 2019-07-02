<?php

namespace Drupal\smsru;

use Drupal\smsru\Client\ClientInterface;
use Drupal\smsru\Message\Message;
use Drupal\smsru\Response\ResponseInterface;

/**
 * SMS.ru API.
 */
class SmsRu {

  /**
   * The API client.
   *
   * @var \Drupal\smsru\Client\ClientInterface
   */
  protected $client;

  /**
   * Constructs a new SmsRu object.
   *
   * @param \Drupal\smsru\Client\ClientInterface $client
   *   The API client.
   */
  public function __construct(ClientInterface $client) {
    $this->client = $client;
  }

  /**
   * Sends SMS.
   *
   * @param \Drupal\smsru\Message\Message $message
   *   The message to send.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/send
   */
  public function smsSend(Message $message): ResponseInterface {
    return $this->client->request('/sms/send', $message->getRequestParams());
  }

  /**
   * Gets SMS status.
   *
   * @param string $sms_id
   *   The SMS ID.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/status
   */
  public function smsStatus(string $sms_id): ResponseInterface {
    return $this->client->request('/sms/status', [
      'sms_id' => $sms_id,
    ]);
  }

  /**
   * Gets SMS cost.
   *
   * @param \Drupal\smsru\Message\Message $message
   *   The SMS to check cost.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/cost
   */
  public function smsCost(Message $message): ResponseInterface {
    return $this->client->request('/sms/cost', $message->getRequestParams());
  }

  /**
   * Gets account balance.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/balance
   */
  public function myBalance(): ResponseInterface {
    return $this->client->request('/my/balance');
  }

  /**
   * Gets daily sms limit and current usage.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/limit
   */
  public function myLimit(): ResponseInterface {
    return $this->client->request('/my/limit');
  }

  /**
   * Gets daily free sms limit and current usage.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/my_free
   */
  public function myFree(): ResponseInterface {
    return $this->client->request('/my/free');
  }

  /**
   * Gets list of approved senders.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/senders
   */
  public function mySenders(): ResponseInterface {
    return $this->client->request('/my/senders');
  }

  /**
   * Check authentication credentials.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/auth_check
   */
  public function authCheck(): ResponseInterface {
    return $this->client->request('/auth/check');
  }

  /**
   * Adds phone number to stop list.
   *
   * @param string $phone
   *   The phone number.
   * @param string $reason
   *   The reason.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/stoplist_add
   */
  public function stoplistAdd(string $phone, string $reason): ResponseInterface {
    return $this->client->request('/stoplist/add', [
      'stoplist_phone' => $phone,
      'stoplist_text' => $reason,
    ]);
  }

  /**
   * Delete phone number from stop list.
   *
   * @param string $phone
   *   The phone number.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/stoplist_del
   */
  public function stoplistDel(string $phone): ResponseInterface {
    return $this->client->request('/stoplist/del', [
      'stoplist_phone' => $phone,
    ]);
  }

  /**
   * Gets stop list.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/stoplist_get
   */
  public function stoplistGet(): ResponseInterface {
    return $this->client->request('/stoplist/get');
  }

  /**
   * Adds callback url.
   *
   * @param string $url
   *   The valid URL.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/callback_add
   */
  public function callbackAdd(string $url): ResponseInterface {
    return $this->client->request('/callback/add', [
      'url' => $url,
    ]);
  }

  /**
   * Delete callback url.
   *
   * @param string $url
   *   The valid URL.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/callback_del
   */
  public function callbackDel(string $url): ResponseInterface {
    return $this->client->request('/callback/del', [
      'url' => $url,
    ]);
  }

  /**
   * Gets callback list.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/callback_get
   */
  public function callbackGet(): ResponseInterface {
    return $this->client->request('/callback/get');
  }

  /**
   * Request phone number for user to call.
   *
   * @param string $phone
   *   The phone number.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/callcheck
   */
  public function callcheckAdd(string $phone): ResponseInterface {
    return $this->client->request('/callcheck/add', [
      'phone' => $phone,
    ]);
  }

  /**
   * Checks status for calling.
   *
   * @param string $check_id
   *   The ID assign to callcheck.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @see https://sms.ru/api/callcheck
   */
  public function callcheckStatus(string $check_id): ResponseInterface {
    return $this->client->request('/callcheck/status', [
      'check_id' => $check_id,
    ]);
  }

}
