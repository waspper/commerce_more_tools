<?php

namespace Drupal\commerce_more_tools\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Defines a custom access checker for the payment collection routes.
 */
class CommercePaymentAccessCheck implements AccessInterface {

  /**
   * Checks access to certain routes.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function checkAccess(RouteMatchInterface $route_match, AccountInterface $account) {
    /** @var \Drupal\commerce_store\Entity\StoreInterface $store */
    $store = $route_match->getParameter('commerce_order')->getStore();
    if ($store) {
      if ($store->getOwner()->id() == $account->id()) {
        $result = AccessResult::allowedIfHasPermission($account, "manage payments in own {$store->bundle()} store type")->cachePerUser();

        return $result;
      }

      return AccessResult::allowedIfHasPermission($account, 'administer commerce_payment');
    }

    return AccessResult::forbidden();
  }

}
