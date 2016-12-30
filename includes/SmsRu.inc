<?php

/**
 * @file
 *   Class for SMS.ru API implementations. It's Drupal based, so it is will not
 *   work outside Drupal.
 */

/**
 * Class SmsRu
 */
class SmsRu {

  private $_apiKey;
  private $_smsRuApiUrl = 'http://sms.ru/';

  /**
   * SmsRu constructor.
   */
  public function __construct() {
    $this->_apiKey = _smsru_get_api_key();
  }

  /**
   * Implements sms/send.
   * You can pass additional parameters as third argument.
   *
   * @see https://sms.ru/?panel=api&subpanel=method&show=sms/send
   */
  public function send_sms($to, $text, array $options = array()) {
    $parameters = array(
      'api_id' => $this->_apiKey,
      'to' => $to,
      'text' => $text,
    );

    $parameters = array_merge($parameters, $options);
    // At current moment multi parameter is not implemented so it ignored.
    // @todo
    unset($parameters['multi']);

    if (!isset($parameters['from']) && $from = variable_get('smsru_default_from', '')) {
      $parameters['from'] = $from;
    }

    return $this->_request('sms/send', $parameters);
  }

  /**
   * Request builder.
   */
  private function _request($request, array $options = array(), $method = 'GET') {
    $data = drupal_http_build_query($options);
    $options = array(
      'method' => $method,
      'timeout' => 30,
      'max_redirects' => 10,
    );
    $request_url = $this->_smsRuApiUrl . $request;
    // @todo figure out why is not working with data in options.
    $result = drupal_http_request("$request_url?$data", $options);
    return $result;
  }

}