<?php

namespace Drupal\spotify\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for spotify routes.
 */
class SpotifyController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
