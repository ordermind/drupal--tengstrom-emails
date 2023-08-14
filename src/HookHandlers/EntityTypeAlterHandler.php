<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\HookHandlers;

use Drupal\tengstrom_emails\EasyEmailTypeListBuilder;

class EntityTypeAlterHandler {

  public function alter(array &$entityTypes): void {
    $this->setListBuilderClassForEasyEmailType($entityTypes);
  }

  protected function setListBuilderClassForEasyEmailType(array &$entityTypes): void {
    if (empty($entityTypes['easy_email_type'])) {
      return;
    }

    $entityTypes['easy_email_type']->setListBuilderClass(EasyEmailTypeListBuilder::class);
  }

}
