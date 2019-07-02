<?php

namespace Drupal\smsru\Response;

/**
 * Object with response data from API request.
 */
interface ResponseInterface {

  /**
   * Gets status from API.
   *
   * @return string
   *   The response status.
   */
  public function getStatus(): string;

  /**
   * Gets the response status code.
   *
   * @return int
   *   The response status code.
   */
  public function getStatusCode(): int;

  /**
   * Gets the response data.
   *
   * @return array
   *   The response data.
   */
  public function getData(): array;

}
