<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\commerce_order\Entity\OrderType;
use Drupal\commerce_order\Entity\OrderTypeInterface;
use Drupal\commerce_store\Entity\StoreType;
use Drupal\commerce_store\Entity\StoreTypeInterface;

/**
 * Provides dynamic permissions for stores.
 */
class StoreTypePermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of store type permissions.
   */
  public function buildPermissions() {
    $permissions = [];
    // Getting needed entities to build permissions list.
    $store_types = StoreType::loadMultiple();
    $order_types = OrderType::loadMultiple();
    foreach ($store_types as $store_type) {
      $permissions += $this->buildPaymentPermissions($store_type);
      $permissions += $this->buildOrderTypePermissions($store_type, $order_types);
    }

    return $permissions;
  }

  /**
   * Prepares a list of payments permissions.
   *
   * @param \Drupal\commerce_store\Entity\StoreTypeInterface $store_type
   *   The commerce store type.
   */
  protected function buildPaymentPermissions(StoreTypeInterface $store_type) {
    $type_id = $store_type->id();
    $type_params = ['%type_name' => $store_type->label()];

    return [
      "manage payments in own $type_id store type" => [
        'title' => $this->t('[Store type %type_name] Manage payments in own store', $type_params),
      ],
    ];
  }

  /**
   * Prepares a list of order type permissions for a given store type.
   *
   * @param \Drupal\commerce_store\Entity\StoreTypeInterface $store_type
   *   The commerce store type.
   * @param array $order_types
   *   Array or order type entities.
   */
  protected function buildOrderTypePermissions(StoreTypeInterface $store_type, array $order_types) {
    $permissions = [];
    if (!empty($order_types)) {
      $store_type_params = ['%store_type_name' => $store_type->label()];
      foreach ($order_types as $order_type) {
        if ($order_type instanceof OrderTypeInterface) {
          $order_type_params = ['%order_type_name' => $order_type->label()];
          $permissions += [
            "view order type {$order_type->id()} in own {$store_type->id()} store type" => [
              'title' => $this->t('[Store type %store_type_name] View order type %order_type_name in own store', $store_type_params + $order_type_params),
            ],
            "update order type {$order_type->id()} in own {$store_type->id()} store type" => [
              'title' => $this->t('[Store type %store_type_name] Update order type %order_type_name in own store', $store_type_params + $order_type_params),
            ],
            "delete order type {$order_type->id()} in own {$store_type->id()} store type" => [
              'title' => $this->t('[Store type %store_type_name] Delete order type %order_type_name in own store', $store_type_params + $order_type_params),
            ],
          ];
        }
      }
    }

    return $permissions;
  }

}
