<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\commerce_payment\PaymentAccessControlHandler as PaymentAccessControlHandlerBase;

/**
 * Defines a more granular access control handler for payments.
 */
class PaymentAccessControlHandler extends PaymentAccessControlHandlerBase {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\commerce_store\Entity\StoreInterface $store */
    $store = $entity->getOrder()->getStore();
    if ($store && $store->getOwner()->id() == $account->id()) {
      $access = AccessResult::allowedIfHasPermission($account, "manage payments in own {$store->bundle()} store type")->cachePerUser();
    }
    else {
      $access = parent::checkAccess($entity, $operation, $account);
    }

    return $access;
  }

}
