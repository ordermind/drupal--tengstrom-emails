<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\HookHandlers\FormAlterHandlers;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Ordermind\DrupalTengstromShared\HookHandlers\FormAlterHandlerInterface;

class EasyEmailTypeEditFormAlterHandler implements FormAlterHandlerInterface {

  public function __construct(
    protected ConfigFactoryInterface $configFactory,
    protected AccountInterface $currentUser,
    protected TranslationInterface $translator
  ) {}

  public function alter(array &$form, FormStateInterface $formState, string $formId): void {
    $this->addTengstromFormClass($form);
    $this->setLabelDescription($form);
    $this->hideFields($form);
    $this->moveSubjectElement($form);
    $this->moveBodyHtmlElement($form);
    $this->moveTokensElement($form);
    $this->setAllowedFormats($form, $formState);
  }

  protected function addTengstromFormClass(array &$form): void {
    $form['#attributes']['class'][] = 'tengstrom-form';
    $form['#attributes']['class'][] = 'entity-form';
  }

  protected function setLabelDescription(array &$form): void {
    $form['label']['#description'] = $this->translator->translate('This label is only used for administrative purposes.');
  }

  protected function hideFields(array &$form): void {
    $hideFields = [
      'key',
      'to',
      'sender',
      'content',
      'body_plain',
      'body_html',
    ];
    foreach ($hideFields as $fieldName) {
      if (!empty($form[$fieldName])) {
        $form[$fieldName]['#access'] = FALSE;
      }
    }

    if (!empty($form['id'])) {
      $form['id']['#access'] = $this->currentUser->hasPermission('tengstrom_emails.see_email_template_machine_name');
    }

    if (!empty($form['tokens'])) {
      $form['tokens']['#access'] = $this->currentUser->hasPermission('administer email types');
    }

    if (!empty($form['email_storage'])) {
      $form['email_storage']['#access'] = $this->currentUser->hasPermission('administer email types');
    }
  }

  protected function moveSubjectElement(array &$form): void {
    if (empty($form['content']['subject'])) {
      return;
    }

    $form['subject'] = $form['content']['subject'];
    $form['subject']['#weight'] = 5;
    $form['subject']['#required'] = TRUE;
    unset($form['content']['subject']);
  }

  protected function moveBodyHtmlElement(array &$form): void {
    if (empty($form['body_html']['bodyHtml'])) {
      return;
    }

    $form['bodyHtml'] = $form['body_html']['bodyHtml'];
    $form['bodyHtml']['#title'] = $this->translator->translate('Content');
    $form['bodyHtml']['#weight'] = 10;
    $form['bodyHtml']['#rows'] = 5;
    $form['bodyHtml']['#required'] = TRUE;
    unset($form['body_html']['bodyHtml']);
  }

  protected function moveTokensElement(array &$form): void {
    if (empty($form['tokens'])) {
      return;
    }

    $form['tokens']['#weight'] = 15;
  }

  protected function setAllowedFormats(array &$form, FormStateInterface $formState): void {
    if (empty($form['bodyHtml'])) {
      return;
    }

    $emailTemplateId = $formState->getFormObject()->getEntity()->id();
    $configName = "field.field.easy_email.{$emailTemplateId}.body_html";
    $settings = (array) $this->configFactory->get($configName)->get('settings');
    $allowedFormats = $settings['allowed_formats'] ?? NULL;
    if (!$allowedFormats) {
      return;
    }

    $form['bodyHtml']['#allowed_formats'] = $allowedFormats;
  }

}
