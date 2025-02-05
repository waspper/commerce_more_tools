<?php

namespace Drupal\commerce_more_tools\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Defines a custom access checker for the payment collection routes.
 */
class CommerceOrderAccessCheck implements AccessInterface {

  /**
   * Checks access to view an order.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function checkViewAccess(RouteMatchInterface $route_match, AccountInterface $account) {
    return $this->checkOperationAccess($route_match, $account, 'view');
  }

  /**
   * Checks access to edit an order.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function checkUpdateAccess(RouteMatchInterface $route_match, AccountInterface $account) {
    return $this->checkOperationAccess($route_match, $account, 'update');
  }

  /**
   * Checks access to delete an order.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function checkDeleteAccess(RouteMatchInterface $route_match, AccountInterface $account) {
    return $this->checkOperationAccess($route_match, $account, 'delete');
  }

  /**
   * Build access to an order, given an operation.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param string $operation
   *   The operation to be checked.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   The access result.
   */
  public function checkOperationAccess(RouteMatchInterface $route_match, AccountInterface $account, string $operation) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $route_match->getParameter('commerce_order');
    /** @var \Drupal\commerce_store\Entity\StoreInterface $store */
    $store = $order ? $order->getStore() : NULL;
    if ($store) {
      if ($store->getOwner()->id() == $account->id()) {
        $result = AccessResult::allowedIfHasPermission($account, "$operation order type {$order->bundle()} in own {$store->bundle()} store type")->cachePerUser();

        return $result;
      }

      return AccessResult::allowedIfHasPermissions(
        $account,
        [
          "administer commerce_order",
          "$operation {$order->bundle()} commerce_order",
        ],
        'OR'
      )->cachePerUser();
    }

    return AccessResult::forbidden();
  }

}
