<?php
declare(strict_types=1);

/**
 * Clase abstracta Plato
 * Reúne lo que tienen en común todos los productos del menú.
 * No se puede crear un "Plato" directamente: solo Comida o Bebida.
 */
abstract class Plato
{
    // protected: las clases hijas (Comida y Bebida) sí pueden usarlas
    protected int $id;
    protected string $nombre;
    protected float $precio;
    protected string $categoria;
    protected bool $disponible;
    protected string $icono;

    public function __construct(
        int $id,
        string $nombre,
        float $precio,
        string $categoria,
        bool $disponible = true,
        string $icono = "🍽️"
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->categoria = $categoria;
        $this->disponible = $disponible;
        $this->icono = $icono;
    }

    // ---------- Getters ----------
    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function estaDisponible(): bool
    {
        return $this->disponible;
    }

    public function getIcono(): string
    {
        return $this->icono;
    }

    /**
     * Método abstracto: cada clase hija decide cómo describirse.
     */
    abstract public function descripcion(): string;

    /**
     * Tarjeta HTML común para Comida y Bebida.
     * $volver indica a qué página regresar después de agregar el plato.
     */
    protected function tarjetaHtml(string $etiquetaExtra, string $volver): string
    {
        $nombre = htmlspecialchars($this->nombre);
        $descripcion = htmlspecialchars($this->descripcion());
        $precio = formatearColones($this->precio);
        $categoria = nombreCategoria($this->categoria);
        $volverSeguro = htmlspecialchars($volver);

        // Si el plato no está disponible, no se muestra el formulario
        if ($this->disponible === false) {
            $accion = '<p class="agotado">Agotado por hoy</p>';
        } else {
            $accion = <<<HTML
            <form action="pedido.php" method="post" class="form-agregar">
                <input type="hidden" name="accion" value="agregar">
                <input type="hidden" name="id" value="{$this->id}">
                <input type="hidden" name="volver" value="{$volverSeguro}">
                <label for="cant-{$this->id}">Cantidad</label>
                <input type="number" id="cant-{$this->id}" name="cantidad" value="1" min="1" max="20">
                <button type="submit" class="boton">Agregar</button>
            </form>
            HTML;
        }

        return <<<HTML
        <article class="tarjeta">
            <div class="tarjeta-icono">{$this->icono}</div>
            <div class="tarjeta-cuerpo">
                <p class="etiquetas"><span class="etiqueta">{$categoria}</span>{$etiquetaExtra}</p>
                <h3>{$nombre}</h3>
                <p class="descripcion">{$descripcion}</p>
                <p class="precio">{$precio}</p>
                {$accion}
            </div>
        </article>
        HTML;
    }
}
