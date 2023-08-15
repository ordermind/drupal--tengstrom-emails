<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\Plugin\EmailAdjuster;

use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AttachedAssets;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Plugin\EmailAdjuster\InlineCssEmailAdjuster as OverriddenAdjuster;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the Inline CSS Email Adjuster.
 *
 * @EmailAdjuster(
 *   id = "tengstrom_mailer_inline_css",
 *   label = @Translation("Inline CSS (Tengström)"),
 *   description = @Translation("Add inline CSS in an improved way."),
 *   weight = 900,
 * )
 */
class InlineCssEmailAdjuster extends OverriddenAdjuster implements ContainerFactoryPluginInterface {
  protected ThemeHandlerInterface $themeHandler;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AssetResolverInterface $assetResolver,
    ThemeHandlerInterface $themeHandler
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $assetResolver);

    $this->themeHandler = $themeHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('asset.resolver'),
      $container->get('theme_handler')
    );
  }

  /**
   * Adjustments:
   * - Hardcoded libraries to control which css is being used
   * - Disable optimization in the assetResolver->getCssAssets() call
   * since that does not work with the scss compiler.
   */
  public function postRender(EmailInterface $email) {
    $siteTheme = $this->themeHandler->getDefault();
    $parentTheme = $this->themeHandler->getTheme($siteTheme)->base_theme;

    // Inline CSS. Request optimization so that the CssOptimizer performs
    // essential processing such as @import.
    $assets = (new AttachedAssets())->setLibraries(
      ["{$parentTheme}/emails", "{$siteTheme}/emails"]
    );
    $css = '';
    foreach ($this->assetResolver->getCssAssets($assets, FALSE) as $file) {
      $css .= file_get_contents($file['data']);
    }

    if ($css) {
      $email->setHtmlBody($this->cssInliner->convert($email->getHtmlBody(), $css));
    }
  }

}
