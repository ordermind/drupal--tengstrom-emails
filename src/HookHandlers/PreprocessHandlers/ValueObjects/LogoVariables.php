<?php

declare(strict_types=1);

namespace Drupal\tengstrom_emails\HookHandlers\PreprocessHandlers\ValueObjects;

class LogoVariables {

  public function __construct(
    protected string $url,
    protected int $width,
    protected int $height
  ) {}

  public function getUrl(): string {
    return $this->url;
  }

  public function getWidth(): int {
    return $this->width;
  }

  public function getHeight(): int {
    return $this->height;
  }

  public function toArray(): array {
    return [
      'url' => $this->getUrl(),
      'width' => $this->getWidth(),
      'height' => $this->getHeight(),
    ];
  }

}
