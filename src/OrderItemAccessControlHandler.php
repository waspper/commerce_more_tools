<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\commerce_order\Entity\OrderItemType;
use Drupal\commerce_order\OrderItemAccessControlHandler as OrderItemAccessControlHandlerBase;

/**
 * Provides an access control handler for order items.
 */
class OrderItemAccessControlHandler extends OrderItemAccessControlHandlerBase {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\commerce_order\Entity\OrderItemInterface $order */
    $order = $entity->getOrder();
    if ($order && $account->hasPermission("manage order items in {$order->bundle()} order type")) {
      return AccessResult::allowed()->addCacheableDependency($entity);
    }

    return parent::checkAccess($entity, $operation, $account);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    if ($entity_bundle) {
      $order_item_type = OrderItemType::load($entity_bundle);
      if ($order_item_type && $account->hasPermission("manage order items in {$order_item_type->getOrderTypeId()} order type")) {
        return AccessResult::allowed()->cachePerUser();
      }
    }

    return parent::checkCreateAccess($account, $context, $entity_bundle);
  }

}
