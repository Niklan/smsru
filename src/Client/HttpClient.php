<?php

namespace Drupal\smsru\Client;

use Drupal\smsru\Auth\AuthInterface;
use Drupal\smsru\Response\Response;
use Drupal\smsru\Response\ResponseInterface;
use GuzzleHttp\Client;

/**
 * The client objects making requests to API with HTTP requests.
 */
class HttpClient extends ClientBase {

  /**
   * The base uri.
   *
   * @var string
   */
  const BASE_URI = 'https://sms.ru';

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * {@inheritdoc}
   */
  public function __construct(AuthInterface $auth) {
    parent::__construct($auth);

    $this->client = new Client();
  }

  /**
   * Makes request to API.
   *
   * @param string $endpoint
   *   The API endpoint. I.e. "/sms/send".
   * @param array $params
   *   The API params send with request.
   *
   * @return \Drupal\smsru\Response\ResponseInterface
   *   The response result.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function request(string $endpoint, array $params = []): ResponseInterface {
    $auth_params = $this->getAuth()->getRequestParams();
    $params = array_merge($params, $auth_params);
    $params['json'] = 1;

    // Use POST instead of GET for more reliable requests with a lot of data.
    $response = $this->client->request('POST', self::BASE_URI . $endpoint, ['query' => $params]);
    $data = json_decode($response->getBody()->getContents(), TRUE);

    $result = new Response($data['status'], $data['status_code'], $data);

    return $result;
  }

}
