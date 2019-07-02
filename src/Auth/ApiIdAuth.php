<?php

namespace Drupal\smsru\Auth;

/**
 * Stores API ID for authentication.
 */
class ApiIdAuth extends AuthBase {

  /**
   * The API ID.
   *
   * @var string
   */
  protected $apiId;

  /**
   * Constructs a new ApiIdAuth object.
   *
   * @param string $api_id
   *   The API ID.
   */
  public function __construct(string $api_id) {
    $this->apiId = $api_id;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequestParams(): array {
    return [
      'api_id' => $this->apiId,
    ];
  }

}
