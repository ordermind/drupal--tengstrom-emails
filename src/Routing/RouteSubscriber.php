<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\tengstrom_emails\Controller\TengstromEmailsController;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $routes = [
      'entity.easy_email_type.preview',
      'entity.easy_email_type.preview_html',
      'entity.easy_email_type.preview_plain',
    ];
    foreach ($routes as $routeName) {
      if ($route = $collection->get($routeName)) {
        $route->setRequirements(['_entity_access' => 'easy_email_type.preview']);

        if ($routeName === 'entity.easy_email_type.preview') {
          $route->setDefaults([
            '_controller' => TengstromEmailsController::class . '::' . 'previewEmailTemplate',
            '_title_callback' => $route->getDefault('_title_callback'),
          ]);
        }
      }
    }
  }

}
