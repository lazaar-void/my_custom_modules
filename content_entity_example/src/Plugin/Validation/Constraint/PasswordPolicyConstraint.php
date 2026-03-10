<?php

declare(strict_types=1);

namespace Drupal\content_entity_example\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted password matches the password policy.
 *
 * @Constraint(
 *   id = "PasswordPolicy",
 *   label = @Translation("Password policy constraint", context = "Validation"),
 * )
 */
class PasswordPolicyConstraint extends Constraint {

  /**
   * The message that will be shown if the password validation fails.
   *
   * @var string
   */
  public $message = 'The password does not SATISFYY the current password policy.';

}
