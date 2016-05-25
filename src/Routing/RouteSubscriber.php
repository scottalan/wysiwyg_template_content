<?php

namespace Drupal\wysiwyg_template_content\Routing;

use Drupal\Core\Routing\RouteBuildEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Routing\RoutingEvents;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {

    if ($collection) {
      if ($route = $collection->get('wysiwyg_template.list_js')) {
        $route->setDefault('_controller', '\Drupal\wysiwyg_template_content\Controller\TemplateContentController::listJson');
      }
      if ($route = $collection->get('wysiwyg_template.list_js.type')) {
        $route->setDefault('_controller', '\Drupal\wysiwyg_template_content\Controller\TemplateContentController::listJson');
      }
    }

    // Override the routes in wysiwyg_template.
//    $route = $collection->get('entity.wysiwyg_template.collection');
//    if ($route) {
//      $route->setPath('/admin/structure/wysiwyg_template_category/manage/{wysiwyg_template_category}/templates');
//    }
//    $route = $collection->get('entity.wysiwyg_template.add_form');
//    if ($route) {
//      $route->setPath('/admin/structure/wysiwyg_template_content/manage/{wysiwyg_template_category}/add');
//    }
//    $route = $collection->get('entity.wysiwyg_template.edit_form');
//    if ($route) {
//      $route->setPath('/admin/structure/wysiwyg_template_content/manage/{wysiwyg_template_category}/edit');
//    }
  }

}
