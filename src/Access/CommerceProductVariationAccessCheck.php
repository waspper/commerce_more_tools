<?php

namespace Drupal\commerce_more_tools\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\Access\AccessInterface;

/**
 * Defines a custom access checker for the product variation routes.
 */
class CommerceProductVariationAccessCheck implements AccessInterface {

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
    /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
    $product = $route_match->getParameter('commerce_product');
    if ($product) {
      /** @var \Drupal\commerce_product\Entity\ProductTypeInterface $product_type */
      $product_type = \Drupal::entityTypeManager()->getStorage('commerce_product_type')->load($product->bundle());
      // Base permissions for our comparison.
      $permissions = [
        'administer commerce_product',
        'access commerce_product overview',
      ];
      if ($product_type && $product_type->allowsMultipleVariations()) {
        foreach ($product_type->getVariationTypeIds() as $variation_type_id) {
          $permissions[] = "manage $variation_type_id commerce_product_variation";
        }
        if ($product->getOwner()->id() == $account->id()) {
          $permissions = array_merge($permissions, [
            'create product variation in own product',
            "update product variation in own {$product->bundle()} product type",
            "delete product variation in own {$product->bundle()} product type",
            "duplicate product variation in own {$product->bundle()} product type",
          ]);
        }

        return AccessResult::allowedIfHasPermissions($account, $permissions, 'OR');
      }
    }

    return AccessResult::forbidden();
  }

}
