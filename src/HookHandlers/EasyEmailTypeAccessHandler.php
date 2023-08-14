<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\HookHandlers;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class EasyEmailTypeAccessHandler {

  public function checkAccess(
    EntityInterface $entity,
    string $operation,
    AccountInterface $account
  ): AccessResultInterface {
    $operationPermissionMapping = [
      'preview' => 'preview email type entities',
      'update' => 'edit email type entities',
      'delete' => 'delete email type entities',
    ];

    foreach ($operationPermissionMapping as $operationName => $permission) {
      if ($operation === $operationName) {
        return $account->hasPermission($permission) ? AccessResult::allowed() : AccessResult::forbidden();
      }
    }

    return AccessResult::neutral();
  }

  public function checkCreateAccess(AccountInterface $account, array $context, ?string $entityBundle): AccessResultInterface {
    return $account->hasPermission('add email type entities') ? AccessResult::allowed() : AccessResult::forbidden();
  }

}
