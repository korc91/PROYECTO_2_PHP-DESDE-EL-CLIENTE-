<?php
declare(strict_types=1);

/**
 * Bebida: hereda de Plato e implementa Mostrable.
 * Agrega si la bebida es caliente o fría.
 */
class Bebida extends Plato implements Mostrable
{
    private string $detalle;
    private bool $caliente;

    public function __construct(
        int $id,
        string $nombre,
        float $precio,
        string $detalle,
        bool $caliente,
        bool $disponible = true,
        string $icono = "☕"
    ) {
        // Todas las bebidas pertenecen a la categoría "bebidas"
        parent::__construct($id, $nombre, $precio, "bebidas", $disponible, $icono);
        $this->detalle = $detalle;
        $this->caliente = $caliente;
    }

    public function esCaliente(): bool
    {
        return $this->caliente;
    }

    // Implementación propia del método abstracto
    public function descripcion(): string
    {
        $temperatura = $this->caliente ? "Bebida caliente." : "Bebida fría.";
        return $this->detalle . " " . $temperatura;
    }

    // Implementación de la interfaz Mostrable
    public function mostrarHtml(string $volver = "menu.php"): string
    {
        $extra = $this->caliente
            ? '<span class="etiqueta etiqueta-caliente">🔥 Caliente</span>'
            : '<span class="etiqueta etiqueta-fria">❄️ Fría</span>';
        return $this->tarjetaHtml($extra, $volver);
    }
}
