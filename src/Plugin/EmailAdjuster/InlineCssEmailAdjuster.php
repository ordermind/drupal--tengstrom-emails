<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\Plugin\EmailAdjuster;

use Drupal\Core\Asset\AttachedAssets;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Plugin\EmailAdjuster\InlineCssEmailAdjuster as OverriddenAdjuster;

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
class InlineCssEmailAdjuster extends OverriddenAdjuster {

  /**
   * Adjustments:
   * - Hardcoded libraries to control which css is being used
   * - Disable optimization in the assetResolver->getCssAssets() call
   * since that does not work with the scss compiler.
   */
  public function postRender(EmailInterface $email) {
    // Inline CSS. Request optimization so that the CssOptimizer performs
    // essential processing such as @import.
    $assets = (new AttachedAssets())->setLibraries(['tengstrom_2022/emails']);
    $css = '';
    foreach ($this->assetResolver->getCssAssets($assets, FALSE) as $file) {
      $css .= file_get_contents($file['data']);
    }

    if ($css) {
      $email->setHtmlBody($this->cssInliner->convert($email->getHtmlBody(), $css));
    }
  }

}
