<?php
declare(strict_types=1);

/**
 * Pedido: guarda los platos elegidos y sus cantidades.
 * En $_SESSION se guarda solo un arreglo [idPlato => cantidad];
 * con ese arreglo se vuelve a construir el objeto en cada página.
 */
class Pedido
{
    // Propiedad estática: es de la clase, no de cada objeto.
    // Cuenta cuántos pedidos se han confirmado en esta sesión.
    public static int $pedidosConfirmados = 0;

    /** @var array<int, int> idPlato => cantidad */
    private array $lineas;

    /** @var array<int, Plato> menú completo, indexado por id */
    private array $menu;

    public function __construct(array $menu, array $lineas = [])
    {
        $this->menu = $menu;
        $this->lineas = $lineas;
    }

    /**
     * Agrega un plato. Si ya estaba, suma la cantidad.
     * Lanza una excepción si la cantidad o el plato no son válidos.
     */
    public function agregar(int $idPlato, int $cantidad): void
    {
        $this->validarCantidad($cantidad);
        $plato = $this->buscarPlato($idPlato);

        if ($plato->estaDisponible() === false) {
            throw new InvalidArgumentException("Lo sentimos, «{$plato->getNombre()}» está agotado.");
        }

        $nuevaCantidad = ($this->lineas[$idPlato] ?? 0) + $cantidad;
        $this->validarCantidad($nuevaCantidad);
        $this->lineas[$idPlato] = $nuevaCantidad;
    }

    /** Cambia la cantidad de un plato que ya está en el pedido. */
    public function cambiarCantidad(int $idPlato, int $cantidad): void
    {
        $this->buscarPlato($idPlato);
        $this->validarCantidad($cantidad);
        $this->lineas[$idPlato] = $cantidad;
    }

    /** Quita un plato del pedido. */
    public function quitar(int $idPlato): void
    {
        unset($this->lineas[$idPlato]);
    }

    /** Vacía el pedido. */
    public function vaciar(): void
    {
        $this->lineas = [];
    }

    public function estaVacio(): bool
    {
        return count($this->lineas) === 0;
    }

    /** Cantidad total de unidades (para el contador del menú). */
    public function cantidadArticulos(): int
    {
        return array_sum($this->lineas);
    }

    /**
     * Devuelve las líneas con el objeto Plato, la cantidad y el importe.
     * @return array<int, array{plato: Plato, cantidad: int, importe: float}>
     */
    public function lineas(): array
    {
        $resultado = [];
        foreach ($this->lineas as $id => $cantidad) {
            if (!isset($this->menu[$id])) {
                continue; // por si un plato dejó de existir
            }
            $plato = $this->menu[$id];
            $resultado[] = [
                "plato" => $plato,
                "cantidad" => $cantidad,
                "importe" => $plato->getPrecio() * $cantidad,
            ];
        }
        return $resultado;
    }

    /** Suma de precio × cantidad de todas las líneas. */
    public function subtotal(): float
    {
        $suma = 0.0;
        foreach ($this->lineas() as $linea) {
            $suma += $linea["importe"];
        }
        return $suma;
    }

    /**
     * Descuento de la promoción del día (RF-10).
     * $promocion es ["categoria" => ..., "porcentaje" => ...] o null.
     */
    public function descuento(?array $promocion): float
    {
        if ($promocion === null) {
            return 0.0;
        }
        $base = 0.0;
        foreach ($this->lineas() as $linea) {
            if ($linea["plato"]->getCategoria() === $promocion["categoria"]) {
                $base += $linea["importe"];
            }
        }
        return round($base * $promocion["porcentaje"] / 100, 2);
    }

    /** IVA sobre el subtotal menos el descuento. */
    public function iva(?array $promocion = null): float
    {
        return round(($this->subtotal() - $this->descuento($promocion)) * IVA, 2);
    }

    /** Total a pagar = subtotal − descuento + IVA. */
    public function total(?array $promocion = null): float
    {
        return $this->subtotal() - $this->descuento($promocion) + $this->iva($promocion);
    }

    /** Arreglo simple para guardar en $_SESSION. */
    public function aArreglo(): array
    {
        return $this->lineas;
    }

    /** Genera el número del pedido confirmado usando la propiedad estática. */
    public function confirmar(): string
    {
        self::$pedidosConfirmados++;
        return "LA-" . date("Ymd") . "-" . str_pad((string) self::$pedidosConfirmados, 3, "0", STR_PAD_LEFT);
    }

    // ---------- Métodos privados de apoyo ----------

    private function validarCantidad(int $cantidad): void
    {
        if ($cantidad < 1 || $cantidad > MAX_CANTIDAD) {
            throw new InvalidArgumentException("La cantidad debe ser un número entre 1 y " . MAX_CANTIDAD . ".");
        }
    }

    private function buscarPlato(int $idPlato): Plato
    {
        if (!isset($this->menu[$idPlato])) {
            throw new InvalidArgumentException("El plato solicitado no existe en el menú.");
        }
        return $this->menu[$idPlato];
    }
}
