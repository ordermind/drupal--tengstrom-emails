<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\HookHandlers\PreprocessHandlers;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\FileInterface;
use Drupal\image\ImageStyleInterface;
use Drupal\tengstrom_config_email_logo\EmailLogoFileLoader;
use Drupal\tengstrom_emails\HookHandlers\PreprocessHandlers\ValueObjects\LogoVariables;
use Ordermind\DrupalTengstromShared\HookHandlers\PreprocessHandlerInterface;

class PreprocessEmailWrapHandler implements PreprocessHandlerInterface {

  public function __construct(
    protected EmailLogoFileLoader $logoFileLoader,
    protected EntityTypeManagerInterface $entityTypeManager
  ) {}

  public function preprocess(array &$variables): void {
    $this->addLogoToEmails($variables);
    $this->addFooterPartialToEmails($variables);
  }

  protected function addLogoToEmails(array &$variables): void {
    $logoFile = $this->logoFileLoader->loadLogo();
    if (!($logoFile instanceof FileInterface)) {
      return;
    }

    $variables['logo'] = $this->createLogoVariables($logoFile)->toArray();
  }

  protected function addFooterPartialToEmails(array &$variables): void {
    $footerPartial = $this->entityTypeManager
      ->getStorage('tengstrom_text_partial')
      ->load('email_footer');

    if (!$footerPartial) {
      return;
    }

    $footerPartial = reset($footerPartial);
    $viewBuilder = $this->entityTypeManager->getViewBuilder('tengstrom_text_partial');
    $renderArray = $viewBuilder->view($footerPartial);
    $variables['footer'] = $renderArray;
  }

  protected function createLogoVariables(FileInterface $logoFile): LogoVariables {
    $imageStyle = $this->entityTypeManager->getStorage('image_style')->load('logo_in_email');
    if ($imageStyle instanceof ImageStyleInterface) {
      return $this->createLogoVariablesFromImageStyle($logoFile, $imageStyle);
    }

    return $this->createFallbackLogoVariables($logoFile);
  }

  protected function createLogoVariablesFromImageStyle(
    FileInterface $logoFile,
    ImageStyleInterface $imageStyle
  ): LogoVariables {
    $styledImageUri = $imageStyle->buildUri($logoFile->uri->value);
    $styledImageUrl = $imageStyle->buildUrl($logoFile->uri->value);
    $imageStyle->createDerivative($logoFile->uri->value, $styledImageUri);
    $styledImageSize = getimagesize($styledImageUri);

    return new LogoVariables($styledImageUrl, $styledImageSize[0], $styledImageSize[1]);
  }

  protected function createFallbackLogoVariables(FileInterface $logoFile): LogoVariables {
    $imageSize = getimagesize($logoFile->getFileUri());

    return new LogoVariables($logoFile->createFileUrl(FALSE), $imageSize[0], $imageSize[1]);
  }

}
