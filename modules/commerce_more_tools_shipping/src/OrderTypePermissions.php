<?php

namespace Drupal\commerce_more_tools_shipping;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\commerce_order\Entity\OrderType;
use Drupal\commerce_order\Entity\OrderTypeInterface;

/**
 * Provides dynamic permissions for orders.
 */
class OrderTypePermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of order type permissions.
   */
  public function buildPermissions() {
    $permissions = [];
    // Getting needed entities to build permissions list.
    $order_types = OrderType::loadMultiple();
    foreach ($order_types as $order_type) {
      $permissions += $this->buildOrderTypePermissions($order_type);
    }

    return $permissions;
  }

  /**
   * Prepares a list of order type permissions.
   *
   * @param \Drupal\commerce_order\Entity\OrderTypeInterface $order_type
   *   The commerce order type.
   */
  protected function buildOrderTypePermissions(OrderTypeInterface $order_type) {
    return [
      "manage shipments in {$order_type->id()} order type in own store" => [
        'title' => $this->t('[Order type %type_name] Manage shipments in own store', ['%type_name' => $order_type->label()]),
      ],
    ];
  }

}
