<?php
declare(strict_types=1);

/**
 * Cliente: nombre y correo de quien hace el pedido.
 * Usa los métodos mágicos __get y __destruct.
 */
class Cliente
{
    private string $nombre;
    private string $correo;

    public function __construct(string $nombre, string $correo)
    {
        $this->nombre = $nombre;
        $this->correo = $correo;
    }

    /**
     * __get se ejecuta al leer una propiedad privada desde fuera,
     * por ejemplo $cliente->nombre. Solo permite leer las que existen.
     */
    public function __get(string $propiedad): string
    {
        if (property_exists($this, $propiedad)) {
            return $this->$propiedad;
        }
        throw new InvalidArgumentException("La propiedad «{$propiedad}» no existe en Cliente.");
    }

    /** Primer nombre, para saludar de forma más cercana. */
    public function primerNombre(): string
    {
        $partes = explode(" ", $this->nombre);
        return ucfirst($partes[0]);
    }

    /**
     * __destruct se ejecuta cuando el objeto deja de usarse
     * (normalmente al terminar la página). Solo muestra el mensaje
     * si el modo de prueba está activo.
     */
    public function __destruct()
    {
        if (MODO_PRUEBA === true) {
            echo '<p class="modo-prueba">[Modo de prueba] Se destruyó el objeto Cliente de '
                . htmlspecialchars($this->nombre) . '.</p>';
        }
    }
}
