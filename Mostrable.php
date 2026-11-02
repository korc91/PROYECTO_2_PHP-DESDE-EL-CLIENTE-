<?php
declare(strict_types=1);

/**
 * Interfaz Mostrable
 * Obliga a las clases que la implementan a saber "dibujarse" en HTML.
 */
interface Mostrable
{
    public function mostrarHtml(): string;
}
