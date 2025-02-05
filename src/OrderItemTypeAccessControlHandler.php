<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\commerce\CommerceBundleAccessControlHandler;

/**
 * Defines the access control handler for order item types.
 */
class OrderItemTypeAccessControlHandler extends CommerceBundleAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if (in_array($operation, ['view label'])) {
      return AccessResult::allowedIfHasPermission($account, "manage order items in {$entity->getOrderTypeId()} order type")->cachePerUser();
    }
    else {
      return parent::checkAccess($entity, $operation, $account);
    }
  }

}
