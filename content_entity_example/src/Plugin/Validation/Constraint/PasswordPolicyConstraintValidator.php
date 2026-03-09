<?php

declare(strict_types=1);

namespace Drupal\content_entity_example\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the PasswordPolicy constraint.
 */
class PasswordPolicyConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface
{

  /**
   * The password policy validator service.
   *
   * @var \Drupal\password_policy\PasswordPolicyValidator
   */
  protected $passwordPolicyValidator;

  /**
   * Constructs a new PasswordPolicyConstraintValidator.
   *
   * @param \Drupal\password_policy\PasswordPolicyValidator $password_policy_validator
   *   The password policy validator service.
   */
  public function __construct($password_policy_validator)
  {
    if ($password_policy_validator) {
      $this->passwordPolicyValidator = $password_policy_validator;
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    // We check if the service exists since password_policy might not be installed.
    // The prompt injects 'password_policy.validator'.
    $validator = NULL;
    if ($container->has('password_policy.validator')) {
      $validator = $container->get('password_policy.validator');
    }
    return new static ($validator);
  }

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint)
  {
    if (!$this->passwordPolicyValidator) {
      // If the service is not available, do not block validation.
      return;
    }

    $password = $items->value;

    // Typically, passwords could be empty when a user is just submitting a register request
    // We skip validation if it's empty.
    if (empty($password)) {
      return;
    }

    if (strlen($password) < 8) {
      $this->context->addViolation($constraint->message);
    }

    $user = \Drupal::currentUser();
    // In some contexts, currentUser might just be an AccountProxyInterface,
    // but validatePassword expects a UserInterface. However, password policies
    // often act on the user being edited. Since we're in an entity validation context,
    // we can get the entity being validated through the constraint's context.
    $entity = $this->context->getRoot()->getValue();
    if ($entity instanceof \Drupal\user\UserInterface) {
      $user = $entity;
    }
    else {
      $user = \Drupal\user\Entity\User::load($user->id());
    }

    $validation_report = $this->passwordPolicyValidator->validatePassword($password, $user);

    if ($validation_report->isInvalid()) {
      $this->context->addViolation($validation_report->getErrors()->render());
    }
  }

}
