<?php

declare(strict_types=1);

namespace ItalyStrap\Components\Footer\Events;

class Footer implements \Stringable
{
    private string $content = '';

    public function withContent(string $content): void
    {
        $this->content = $content;
    }

    public function render(): string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        return $this->render();
    }
}