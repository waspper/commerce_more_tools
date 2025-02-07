<?php

namespace Drupal\commerce_more_tools;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\commerce_product\Entity\ProductType;
use Drupal\commerce_product\Entity\ProductTypeInterface;

/**
 * Provides dynamic permissions for products.
 */
class ProductTypePermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of product type permissions.
   */
  public function buildPermissions() {
    $permissions = [
      "create product variation in own product" => [
        'title' => $this->t('Create product variations in own product'),
        'description' => $this->t('WARNING: Due to the nature of Drupal Commerce, there is no a definitive way to determine the parent product. So, this permission depends initially on the route parameter (%route_name_product). If not available, it will fallback to default validation made by Drupal Commerce.', ['%route_name_product' => 'commerce_product']),
      ],
    ];
    // Getting needed entities to build permissions list.
    $product_types = ProductType::loadMultiple();
    foreach ($product_types as $product_type) {
      $permissions += $this->buildProductTypePermissions($product_type);
    }

    return $permissions;
  }

  /**
   * Prepares a list of product type permissions.
   *
   * @param \Drupal\commerce_product\Entity\ProductTypeInterface $product_type
   *   The commerce product type.
   */
  protected function buildProductTypePermissions(ProductTypeInterface $product_type) {
    $type_id = $product_type->id();
    $type_params = ['%type_name' => $product_type->label()];

    return [
      "update product variation in own $type_id product type" => [
        'title' => $this->t('[Product type %type_name] Update product variation in own product', $type_params),
      ],
      "delete product variation in own $type_id product type" => [
        'title' => $this->t('[Product type %type_name] Delete product variation in own product', $type_params),
      ],
      "duplicate product variation in own $type_id product type" => [
        'title' => $this->t('[Product type %type_name] Duplicate product variation in own product', $type_params),
      ],
    ];
  }

}
