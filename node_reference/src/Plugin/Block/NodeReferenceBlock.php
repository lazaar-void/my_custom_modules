<?php

declare(strict_types=1);

namespace Drupal\node_reference\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block displaying the selected node and related nodes.
 */
#[Block(
  id: 'node_reference_block',
  admin_label: new TranslatableMarkup('Node Reference Block'),
  category: new TranslatableMarkup('Custom')
)]
class NodeReferenceBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a NodeReferenceBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
    EntityTypeManagerInterface $entity_type_manager,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = $this->configFactory->get('node_reference.settings');
    $nid = $config->get('selected_node');

    // If no node is selected, show a message.
    if (!$nid) {
      return [
        '#markup' => $this->t('No node has been selected. Please configure the <a href=":url">Node Reference settings</a>.', [
          ':url' => '/admin/config/system/node-reference',
        ]),
      ];
    }

    // Load the selected node.
    $selected_node = Node::load($nid);
    if (!$selected_node) {
      return [
        '#markup' => $this->t('The selected node no longer exists.'),
      ];
    }

    $selected_title = $selected_node->getTitle();
    $bundle = $selected_node->bundle();

    // Entity query: find nodes of the same content type, excluding the
    // currently selected node.
    $node_storage = $this->entityTypeManager->getStorage('node');
    $query = $node_storage->getQuery()
      ->condition('type', $bundle)
      ->condition('nid', $nid, '<>')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->range(0, 10)
      ->sort('created', 'DESC');

    $result_nids = $query->execute();

    // Load the nodes and extract their titles.
    $related_titles = [];
    if (!empty($result_nids)) {
      $related_nodes = $node_storage->loadMultiple($result_nids);
      foreach ($related_nodes as $node) {
        $related_titles[] = $node->getTitle();
      }
    }

    return [
      '#theme' => 'node_reference_block',
      '#selected_title' => $selected_title,
      '#related_nodes' => $related_titles,
      '#cache' => [
        'tags' => [
          'node:' . $nid,
          'node_list',
          'config:node_reference.settings',
        ],
      ],
    ];
  }

}
