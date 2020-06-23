<?php

namespace Drupal\imuko\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class MauricioCommand.
 */
class CrearAjaxCommand implements CommandInterface {

  protected $username;
  protected $uid;

  /**
   * Constructor.
   */
  public function __construct($username,$uid) {
    $this->username = $username;
    $this->uid = $uid;
  }

  /**
   * Render custom ajax command.
   *
   * @return ajax
   *   Command function.
   */
  public function render() {
    return [
      'command' => 'guardar',
      'message' => [
        "username"  =>$this->username,
        "uid"       =>$this->uid,
      ],
    ];
  }

}
