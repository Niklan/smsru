<?php

namespace Drupal\smsru\Auth;

/**
 * Stores authentication credentials.
 */
interface AuthInterface {

  /**
   * Gets authentication parameters for request.
   *
   * @return array
   *   The array with authentication parameters.
   */
  public function getRequestParams(): array;

}

