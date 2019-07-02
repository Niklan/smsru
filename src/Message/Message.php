<?php

namespace Drupal\smsru\Message;

/**
 * The object represents the message send with SMS.
 *
 * @see https://sms.ru/api/send For more information about values.
 */
class Message {

  /**
   * The phone to send message to.
   *
   * @var string
   */
  protected $phone;

  /**
   * The message send in SMS.
   *
   * @var string
   */
  protected $message;

  /**
   * The sender name.
   *
   * @var string
   */
  protected $from;

  /**
   * The send time.
   *
   * @var int
   */
  protected $time;

  /**
   * The time to live for message.
   *
   * @var int
   */
  protected $ttl;

  /**
   * The mark to send message only in daytime.
   *
   * @var bool
   */
  protected $daytime;

  /**
   * The mark to transliterate message before sending.
   *
   * @var bool
   */
  protected $translit;

  /**
   * The mark that message is for testing purpose.
   *
   * @var bool
   */
  protected $test;

  /**
   * The partner ID for referral program.
   *
   * @var int
   */
  protected $partnerId;

  /**
   * Constructs a new Message object.
   *
   * @param string $phone
   *   The phone to send message to.
   * @param string $message
   *   The message send in SMS.
   */
  public function __construct(string $phone, string $message) {
    $this->phone = $phone;
    $this->message = $message;
  }

  /**
   * Sets message sender from.
   *
   * @param string $from
   *   The name of sender.
   *
   * @return $this
   */
  public function setFrom(string $from): Message {
    $this->from = $from;

    return $this;
  }

  /**
   * Sets time to deliver message.
   *
   * @param int $time
   *   The UNIX timestamp.
   *
   * @return $this
   */
  public function setTime(int $time): Message {
    $this->time = $time;

    return $this;
  }

  /**
   * Sets time to live for message.
   *
   * @param int $ttl
   *   The time to live in minutes.
   *
   * @return $this
   */
  public function setTtl(int $ttl): Message {
    $this->ttl = $ttl;

    return $this;
  }

  /**
   * Sets the daytime mark.
   *
   * @param bool $daytime
   *   The daytime status.
   *
   * @return $this
   */
  public function setDaytime(bool $daytime): Message {
    $this->daytime = $daytime;

    return $this;
  }

  /**
   * Sets transliteration status for message.
   *
   * @param bool $translit
   *   The transliteration status.
   *
   * @return $this
   */
  public function setTranslit(bool $translit): Message {
    $this->translit = $translit;

    return $this;
  }

  /**
   * Sets test status for message.
   *
   * @param bool $test
   *   The test status.
   *
   * @return $this
   */
  public function setTest(bool $test): Message {
    $this->test = $test;

    return $this;
  }

  /**
   * Sets the partner ID.
   *
   * @param int $partnerId
   *   The partner identifier.
   *
   * @return $this
   */
  public function setPartnerId(int $partnerId): Message {
    $this->partnerId = $partnerId;

    return $this;
  }

  /**
   * Gets params for an API request.
   *
   * @return array
   *   The options ready for using in request.
   */
  public function getRequestParams(): array {
    $params = [
      'to' => $this->phone,
      'msg' => $this->message,
    ];

    empty($this->from) ?: $params['from'] = $this->from;
    empty($this->time) ?: $params['time'] = $this->time;
    empty($this->ttl) ?: $params['ttl'] = $this->ttl;
    empty($this->partnerId) ?: $params['partnerId'] = $this->partnerId;
    !$this->daytime ?: $params['daytime'] = (int) $this->ttl;
    !$this->translit ?: $params['translit'] = (int) $this->translit;
    !$this->test ?: $params['test'] = (int) $this->test;

    return $params;
  }

}
