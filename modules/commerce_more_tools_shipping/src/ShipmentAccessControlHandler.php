<?php

namespace Drupal\commerce_more_tools_shipping;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_shipping\Entity\ShipmentInterface;
use Drupal\commerce_shipping\ShipmentAccessControlHandler as ShipmentAccessControlHandlerBase;

/**
 * Provides an access control handler for shipments.
 */
class ShipmentAccessControlHandler extends ShipmentAccessControlHandlerBase {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if (
      !$account->hasPermission($this->entityType->getAdminPermission()) &&
      !$account->hasPermission("manage {$entity->bundle()} commerce_shipment") &&
      $entity instanceof ShipmentInterface
    ) {
      $order = $entity->getOrder();
      $store = $order->getStore();
      if ($store->getOwner()->id() == $account->id()) {
        return AccessResult::allowedIfHasPermission($account, "manage shipments in {$order->bundle()} order type in own store")->cachePerUser();
      }
    }

    return parent::checkAccess($entity, $operation, $account);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $order = \Drupal::routeMatch()->getParameter('commerce_order');
    if (
      $order instanceof OrderInterface &&
      !$account->hasPermission($this->entityType->getAdminPermission()) &&
      !$account->hasPermission("manage {$entity_bundle} commerce_shipment")
    ) {
      $store = $order->getStore();
      if ($store->getOwner()->id() == $account->id()) {
        return AccessResult::allowedIfHasPermission($account, "manage shipments in {$order->bundle()} order type in own store")->cachePerUser();
      }
    }

    return parent::checkCreateAccess($account, $context, $entity_bundle);
  }

}
