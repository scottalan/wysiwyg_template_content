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
//      if ($route = $collection->get('entity.wysiwyg_template_content.add_page')) {
//        $route->setPath('/admin/config/content/wysiwyg-templates/add');
//      }
//      if ($route = $collection->get('entity.wysiwyg_template.collection')) {
//        $route->setPath('/admin/wysiwyg-template/config/libraries');
//      }
    }

    // Override the routes in wysiwyg_template.
//    $route = $collection->get('entity.wysiwyg_template.collection');
//    if ($route) {
//      $route->setPath('/admin/structure/wysiwyg_template_library/manage/{wysiwyg_template_library}/templates');
//    }
//    $route = $collection->get('entity.wysiwyg_template.add_form');
//    if ($route) {
//      $route->setPath('/admin/structure/wysiwyg_template_content/manage/{wysiwyg_template_library}/add');
//    }
//    $route = $collection->get('entity.wysiwyg_template.edit_form');
//    if ($route) {
//      $route->setPath('/admin/structure/wysiwyg_template_content/manage/{wysiwyg_template_library}/edit');
//    }
  }

}
