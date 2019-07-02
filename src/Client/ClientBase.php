<?php

namespace Drupal\smsru\Client;

use Drupal\smsru\Auth\AuthInterface;

/**
 * Base object for all clients.
 */
abstract class ClientBase implements ClientInterface {

  /**
   * The client authentication credits.
   *
   * @var \Drupal\smsru\Auth\AuthInterface
   */
  protected $auth;

  /**
   * Constructs a new ClientBase object.
   *
   * @param \Drupal\smsru\Auth\AuthInterface $auth
   *   The authentication credits.
   */
  public function __construct(AuthInterface $auth) {
    $this->auth = $auth;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuth(): AuthInterface {
    return $this->auth;
  }

}
