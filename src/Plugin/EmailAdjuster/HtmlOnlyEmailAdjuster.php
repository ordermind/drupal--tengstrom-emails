<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\Plugin\EmailAdjuster;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\Processor\EmailAdjusterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the HTML Only Email Adjuster.
 *
 * @EmailAdjuster(
 *   id = "tengstrom_email_html_only",
 *   label = @Translation("HTML only (Tengström)"),
 *   description = @Translation("Renders the e-mail as HTML only."),
 *   weight = 800,
 * )
 */
class HtmlOnlyEmailAdjuster extends EmailAdjusterBase implements ContainerFactoryPluginInterface {

  protected RendererInterface $renderer;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function postRender(EmailInterface $email) {
    $html = $this->render($email, $email->getHtmlBody(), TRUE);
    $email->setHtmlBody($html->__toString());
  }

  protected function render(EmailInterface $email, string $body) {
    $render = [
      '#theme' => 'email_wrap',
      '#email' => $email,
      '#body' => Markup::create($body),
      '#is_html' => TRUE,
    ];

    return $this->renderer->renderPlain($render);
  }

}
