<?php

namespace Drupal\tengstrom_emails\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\easy_email\Entity\EasyEmailTypeInterface;
use Drupal\symfony_mailer\EmailFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class TengstromEmailsController extends ControllerBase implements ContainerInjectionInterface {
  protected EmailFactoryInterface $emailFactory;
  protected RendererInterface $renderer;

  public function __construct(EmailFactoryInterface $emailFactory, RendererInterface $renderer) {
    $this->emailFactory = $emailFactory;
    $this->renderer = $renderer;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('email_factory'),
      $container->get('renderer')
    );
  }

  public function previewEmailTemplate(EasyEmailTypeInterface $easy_email_type) {
    return [
      '#type' => 'html_tag',
      '#tag' => 'iframe',
      '#attributes' => [
        'src' => Url::fromRoute('tengstrom_emails.render_easy_email', ['easy_email_type' => $easy_email_type->id()])->toString(),
        'loading' => 'eager',
        'width' => '100%',
        'height' => 800,
        'style' => 'border: none;',
      ],
    ];
  }

  /**
   * Renders an easy email as html, intended for use in preview iframe.
   */
  public function renderEasyEmail(EasyEmailTypeInterface $easy_email_type) {
    $email = $this->emailFactory->newEntityEmail($easy_email_type, $easy_email_type->id());
    $email->customize($this->languageManager()->getCurrentLanguage()->getId(), $this->currentUser());
    $bodyHtml = $easy_email_type->getBodyHtml();

    $email->setBody([
      '#type' => 'processed_text',
      '#text' => $bodyHtml['value'],
      '#format' => $bodyHtml['format'],
    ]);

    $email->render();
    $email->process();

    return new Response($email->getHtmlBody());
  }

}
