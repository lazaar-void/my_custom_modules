<?php

use Drupal\menu_link_content\Entity\MenuLinkContent;

/**
 * Create the initial editable menu links.
 */
function hello_world_post_update_create_menu_links() {
  $menu_link = MenuLinkContent::create([
    'title' => 'Editable Hello World',
    'link' => ['uri' => 'internal:/hello'],
    'menu_name' => 'main',
    'weight' => 10,
  ]);
  $menu_link->save();
}
