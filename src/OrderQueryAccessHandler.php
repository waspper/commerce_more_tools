<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\Session\AccountInterface;
use Drupal\entity\QueryAccess\ConditionGroup;
use Drupal\commerce_order\OrderQueryAccessHandler as OrderQueryAccessHandlerBase;

/**
 * Controls query access based on the Order entity permissions.
 */
class OrderQueryAccessHandler extends OrderQueryAccessHandlerBase {

  /**
   * {@inheritdoc}
   */
  protected function buildEntityConditions($operation, AccountInterface $account) {
    if ($operation == 'view' && !$account->hasPermission('view commerce_order')) {
      $conditions = new ConditionGroup('OR');
      $conditions->addCacheContexts(['user.permissions']);
      // Checking permissions for each order type on each store type.
      $allowed_store_bundles = [];
      $allowed_order_bundles = [];
      $store_bundles = array_keys($this->bundleInfo->getBundleInfo('commerce_store'));
      $order_bundles = array_keys($this->bundleInfo->getBundleInfo('commerce_order'));
      foreach ($store_bundles as $store_bundle) {
        foreach ($order_bundles as $order_bundle) {
          if ($account->hasPermission("view order type {$order_bundle} in own {$store_bundle} store type")) {
            if (!in_array($order_bundle, $allowed_order_bundles)) {
              $allowed_order_bundles[] = $order_bundle;
            }
            if (!in_array($store_bundle, $allowed_store_bundles)) {
              $allowed_store_bundles[] = $store_bundle;
            }
          }
        }
      }
      if (!empty($allowed_store_bundles) && !empty($allowed_order_bundles)) {
        // Maybe there is a better way. Since "query_access" doesn't allow
        // to specify conditions as in Entity Query API, we need to strictly
        // pass the store IDs.
        $store_ids = \Drupal::entityTypeManager()->getStorage('commerce_store')
          ->getQuery()
          ->accessCheck(FALSE)
          ->condition('uid', $account->id())
          ->condition('type', $allowed_store_bundles, 'IN')
          ->execute();
        if (!empty($store_ids)) {
          $conditions->addCondition('type', $allowed_order_bundles);
          $conditions->addCondition('store_id', $store_ids);
        }
      }

      return $conditions->count() ? $conditions : NULL;
    }

    return parent::buildEntityConditions($operation, $account);
  }

}
