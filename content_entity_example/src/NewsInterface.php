<?php

declare(strict_types=1);

namespace Drupal\content_entity_example;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a news entity type.
 */
interface NewsInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
