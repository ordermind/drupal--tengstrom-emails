<?php

namespace Drupal\tengstrom_emails;

use Drupal\Core\Entity\EntityInterface;
use Drupal\easy_email\EasyEmailTypeListBuilder as OverriddenListBuilder;

class EasyEmailTypeListBuilder extends OverriddenListBuilder {

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if (
      !empty($operations['preview'])
      && !empty($operations['preview']['url'])
      && !$operations['preview']['url']->access()
    ) {
      unset($operations['preview']);
    }

    if (!empty($operations['edit'])) {
      $operations['edit']['title'] = $this->t('Edit');
    }

    return $operations;
  }

}
