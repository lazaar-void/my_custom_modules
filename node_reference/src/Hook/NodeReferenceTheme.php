<?php

declare(strict_types=1);

namespace Drupal\node_reference\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hook definitions for the node_reference module.
 */
class NodeReferenceTheme {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'node_reference_block' => [
        'variables' => [
          'selected_title' => '',
          'related_nodes' => [],
        ],
      ],
    ];
  }

}
