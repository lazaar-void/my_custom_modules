<?php

declare(strict_types=1);

namespace Drupal\anytown\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;




/**
 * Provides a hello world block.
 */
#[Block(
  id: 'anytown_hello_world',
  admin_label: new TranslatableMarkup('Hello World, from the anytime block'),
  category: new TranslatableMarkup('Custom')
)]
class HelloWorldBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Current user service
   *
   * @var AccountProxyInterface
   */
  private $current_user;
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $current_user,
  )
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->current_user = $current_user;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition){
  return new static(
    $configuration,
    $plugin_id,
    $plugin_definition,
    $container->get('current_user')
  );

  }
  /**
   * {@inheritdoc}
   */
  public function build(): array
  {
    $name = $this->current_user->getDisplayName();
    if ($this->current_user->isAuthenticated()) {
      $build['content'] = [
        '#markup' => $this->t('Hello, %name !', ['%name' => $name]),
      ];
    }
    else {
      $build['content'] = [
        '#markup' => $this->t('Hello, World')
      ];
    }
      return $build;


  }

}
