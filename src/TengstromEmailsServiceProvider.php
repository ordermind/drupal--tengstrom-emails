<?php

namespace Drupal\tengstrom_emails;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class TengstromEmailsServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    if ($container->hasDefinition('easy_email.handler')) {
      $definition = $container->getDefinition('easy_email.handler');
      $definition->setClass(EmailHandler::class);
    }
  }

}
