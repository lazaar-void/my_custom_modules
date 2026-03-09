<?php

declare(strict_types=1);

namespace Drupal\node_reference\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Configuration form for selecting a node reference.
 */
final class NodeReferenceSettingsForm extends ConfigFormBase {

  /**
   * Name for module's configuration object.
   */
  const SETTINGS = 'node_reference.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'node_reference_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [self::SETTINGS];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(self::SETTINGS);
    $nid = $config->get('selected_node');

    // Load the node to use as default value for the autocomplete field.
    $default_node = NULL;
    if ($nid) {
      $default_node = Node::load($nid);
    }

    $form['selected_node'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Select a node'),
      '#description' => $this->t('Start typing to search for a node by title.'),
      '#target_type' => 'node',
      '#default_value' => $default_node,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(self::SETTINGS)
      ->set('selected_node', (int) $form_state->getValue('selected_node'))
      ->save();

    $this->messenger()->addMessage($this->t('Node reference configuration saved.'));

    parent::submitForm($form, $form_state);
  }

}
