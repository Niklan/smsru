<?php

namespace Drupal\smsru\Auth;

/**
 * Stores login and password for authentication.
 */
class LoginPasswordAuth extends AuthBase {

  /**
   * The login.
   *
   * @var string
   */
  protected $login;

  /**
   * The password.
   *
   * @var string
   */
  protected $password;

  /**
   * Constructs a new LoginPasswordAuth object.
   *
   * @param string $login
   *   The login.
   * @param string $password
   *   The password.
   */
  public function __construct(string $login, string $password) {
    $this->login = $login;
    $this->password = $password;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequestParams(): array {
    return [
      'login' => $this->login,
      'password' => $this->password,
    ];
  }

}
