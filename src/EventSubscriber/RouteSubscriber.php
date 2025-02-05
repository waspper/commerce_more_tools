<?php

namespace Drupal\commerce_more_tools\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Altering some routes to set more granular permissions.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    $events = parent::getSubscribedEvents();
    // Ensure to run after the Views route subscriber.
    // @see \Drupal\views\EventSubscriber\RouteSubscriber.
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -250];

    return $events;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Alter access check to
    // "/admin/commerce/orders/{commerce_order}/payments".
    if ($route = $collection->get('entity.commerce_payment.collection')) {
      $route->setRequirements([
        '_custom_access' => 'Drupal\commerce_more_tools\Access\CommercePaymentAccessCheck::checkAccess',
      ]);
    }
    if ($route = $collection->get('view.commerce_order_payments.page_1')) {
      $route->setRequirements([
        '_custom_access' => 'Drupal\commerce_more_tools\Access\CommercePaymentAccessCheck::checkAccess',
      ]);
    }
    // Alter access check to
    // "/admin/commerce/orders/{commerce_order}/payments/add".
    if ($route = $collection->get('entity.commerce_payment.add_form')) {
      $route->setRequirements([
        '_custom_access' => 'Drupal\commerce_more_tools\Access\CommercePaymentAccessCheck::checkAccess',
      ]);
    }
    // Alter access check to
    // "/admin/commerce/orders/{commerce_order}".
    if ($route = $collection->get('entity.commerce_order.canonical')) {
      $route->setRequirements([
        '_custom_access' => 'Drupal\commerce_more_tools\Access\CommerceOrderAccessCheck::checkViewAccess',
      ]);
    }
    // Alter access check to
    // "/admin/commerce/orders/{commerce_order}/edit".
    if ($route = $collection->get('entity.commerce_order.edit_form')) {
      $route->setRequirements([
        '_custom_access' => 'Drupal\commerce_more_tools\Access\CommerceOrderAccessCheck::checkUpdateAccess',
      ]);
    }
    // Alter access check to
    // "/admin/commerce/orders/{commerce_order}/delete".
    if ($route = $collection->get('entity.commerce_order.delete_form')) {
      $route->setRequirements([
        '_custom_access' => 'Drupal\commerce_more_tools\Access\CommerceOrderAccessCheck::checkDeleteAccess',
      ]);
    }

  }

}
