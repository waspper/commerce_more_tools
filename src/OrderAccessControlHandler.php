<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\commerce_order\OrderAccessControlHandler as OrderAccessControlHandlerBase;

/**
 * Defines a more granular access control handler for orders.
 */
class OrderAccessControlHandler extends OrderAccessControlHandlerBase {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\commerce_store\Entity\StoreInterface $store */
    $store = $entity->getStore();
    if (
      $store &&
      $store->getOwner()->id() == $account->id() &&
      in_array($operation, ['view', 'update', 'delete'])
    ) {
      $access = AccessResult::allowedIfHasPermission($account, "$operation order type {$entity->bundle()} in own {$store->bundle()} store type")->cachePerUser();
    }
    else {
      $access = parent::checkAccess($entity, $operation, $account);
    }

    return $access;
  }

}
