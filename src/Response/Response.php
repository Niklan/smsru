<?php

namespace Drupal\smsru\Response;

/**
 * Objects with response from the API.
 */
class Response implements ResponseInterface {

  /**
   * The response status.
   *
   * @var string
   */
  protected $status;

  /**
   * The response status code from the API.
   *
   * @var int
   */
  protected $statusCode;

  /**
   * The response data.
   *
   * @var array
   */
  protected $data;

  /**
   * Constructs a new Response object.
   *
   * @param string $status
   *   The response status.
   * @param int $status_code
   *   The response status code from the API.
   * @param array $data
   *   The response data.
   */
  public function __construct(string $status, int $status_code, array $data) {
    $this->status = $status;
    $this->statusCode = $status_code;
    $this->data = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus(): string {
    return $this->status;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatusCode(): int {
    return $this->statusCode;
  }

  /**
   * {@inheritdoc}
   */
  public function getData(): array {
    return $this->getData();
  }

}
