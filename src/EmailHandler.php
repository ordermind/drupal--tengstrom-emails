<?php

namespace Drupal\tengstrom_emails;

use Drupal\easy_email\Entity\EasyEmailInterface;
use Drupal\easy_email\Service\EmailHandler as OverriddenHandler;
use Drupal\html_filter_token_workaround\Plugin\Filter\RestoreTokenSeparator;

class EmailHandler extends OverriddenHandler {

  /**
   * @inheritDoc
   */
  public function sendEmail(EasyEmailInterface $email, $params = [], $send_duplicate = FALSE) {
    $this->restoreTokenSeparator($email);

    return parent::sendEmail($email, $params, $send_duplicate);
  }

  private function restoreTokenSeparator(EasyEmailInterface $email) {
    if ($email->hasField('body_html')) {
      $html_body = $email->getHtmlBody();

      $email->setHtmlBody(
        RestoreTokenSeparator::run($html_body['value']),
        $html_body['format']
      );
    }
  }

}
