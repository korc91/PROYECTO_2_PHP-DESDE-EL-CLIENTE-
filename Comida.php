<?php
declare(strict_types=1);

/**
 * Comida: hereda de Plato e implementa Mostrable.
 * Agrega si el plato es vegetariano.
 */
class Comida extends Plato implements Mostrable
{
    private string $detalle;
    private bool $vegetariano;

    public function __construct(
        int $id,
        string $nombre,
        float $precio,
        string $categoria,
        string $detalle,
        bool $vegetariano = false,
        bool $disponible = true,
        string $icono = "🍽️"
    ) {
        // Llama al constructor de la clase padre (Plato)
        parent::__construct($id, $nombre, $precio, $categoria, $disponible, $icono);
        $this->detalle = $detalle;
        $this->vegetariano = $vegetariano;
    }

    public function esVegetariano(): bool
    {
        return $this->vegetariano;
    }

    // Implementación propia del método abstracto
    public function descripcion(): string
    {
        $texto = $this->detalle;
        if ($this->vegetariano === true) {
            $texto .= " Opción vegetariana.";
        }
        return $texto;
    }

    // Implementación de la interfaz Mostrable
    public function mostrarHtml(string $volver = "menu.php"): string
    {
        $extra = $this->vegetariano ? '<span class="etiqueta etiqueta-verde">🌱 Vegetariano</span>' : '';
        return $this->tarjetaHtml($extra, $volver);
    }
}
