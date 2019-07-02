<?php

namespace Drupal\smsru\Client;

use Drupal\smsru\Auth\AuthInterface;
use Drupal\smsru\Response\ResponseInterface;

/**
 * Client object which making requests to the API endpoints.
 */
interface ClientInterface {

  /**
   * Makes request to API.
   *
   * @param string $endpoint
   *   The API endpoint. I.e. "/sms/send".
   * @param array $params
   *   The API params send with request.
   *
   * @return mixed
   */
  public function request(string $endpoint, array $params = []): ResponseInterface;

  /**
   * Gets client authentication credits.
   *
   * @return \Drupal\smsru\Auth\AuthInterface
   *   The authentication credits.
   */
  public function getAuth(): AuthInterface;

}
