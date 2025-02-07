<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\commerce_product\ProductVariationAccessControlHandler as ProductVariationAccessControlHandlerBase;

/**
 * Provides an access control handler for product variations.
 */
class ProductVariationAccessControlHandler extends ProductVariationAccessControlHandlerBase {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // @var \Drupal\commerce_product\Entity\ProductInterface $product
    $product = $entity->getProduct();
    if (
      $product &&
      $product->getOwner()->id() == $account->id() &&
      !$account->hasPermission($this->entityType->getAdminPermission()) &&
      in_array($operation, ['update', 'delete', 'duplicate'])
    ) {
      return AccessResult::allowedIfHasPermission($account, "{$operation} product variation in own {$product->bundle()} product type")->cachePerUser();
    }
    return parent::checkAccess($entity, $operation, $account);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    // There is no safe way to get host product or even the product type,
    // so, need to deal with route parameters.
    // @TODO: Maybe is it good to provide a negotiator? If in the future,
    // we need to allow providing product from a different location than route.
    $params = self::getRouteParameters();
    if (isset($params['commerce_product']) && $params['commerce_product'] instanceof ProductInterface) {
      $product = $params['commerce_product'];
      if ($product->getOwner()->id() == $account->id()) {
        return AccessResult::allowedIfHasPermission($account, 'create product variation in own product')->cachePerUser();
      }
    }

    return parent::checkCreateAccess($account, $context, $entity_bundle);
  }

  /**
   * Get current route parameters.
   *
   * @return array
   *   The current route parameters.
   */
  public static function getRouteParameters() : array {
    return \Drupal::routeMatch()->getParameters()->all();
  }

}
