<?php
declare(strict_types=1);

/* =============================================================
   funciones.php · Constantes y funciones propias del proyecto
   ============================================================= */

// Zona horaria de Costa Rica para date()
date_default_timezone_set("America/Costa_Rica");

// ---------- Constantes: datos que no cambian ----------
const NOMBRE_CAFETERIA = "Cafetería Los Alpes";
const ESLOGAN = "Café de altura y cocina casera desde 2010";
const HORARIO = "Lunes a sábado de 7:00 a. m. a 7:00 p. m. · Domingo de 8:00 a. m. a 4:00 p. m.";
const DIRECCION = "100 m norte del parque central de Coronado, San José, Costa Rica";
const TELEFONO_WHATSAPP = "50688887777";   // número de ejemplo
const IVA = 0.13;                           // 13 %
const PLATOS_POR_PAGINA = 6;
const MAX_CANTIDAD = 20;
const DIAS_COOKIE = 30;
const MODO_PRUEBA = false;                  // true = muestra el mensaje de __destruct
const CATEGORIAS = ["desayunos", "almuerzos", "bebidas", "postres"];

/**
 * 1. Da formato de colones a un monto: 2500 → "₡2 500".
 */
function formatearColones(float $monto): string
{
    return "₡" . number_format($monto, 0, ",", " ");
}

/**
 * 2. Devuelve el nombre bonito de una categoría usando match.
 */
function nombreCategoria(string $categoria): string
{
    return match ($categoria) {
        "desayunos" => "Desayunos",
        "almuerzos" => "Almuerzos",
        "bebidas"   => "Bebidas",
        "postres"   => "Postres",
        default     => ucfirst($categoria),
    };
}

/**
 * 3. Parámetro POR REFERENCIA (&): limpia el texto original
 *    (quita espacios de más y etiquetas HTML) sin devolver nada.
 */
function limpiarTexto(string &$texto): void
{
    $texto = trim(strip_tags($texto));
    $texto = preg_replace('/\s+/', ' ', $texto) ?? "";
}

/**
 * 4. Parámetro con VALOR POR DEFECTO: corta el arreglo para mostrar
 *    solo una página. Si no se indica, usa PLATOS_POR_PAGINA.
 */
function paginar(array $elementos, int $pagina, int $porPagina = PLATOS_POR_PAGINA): array
{
    $inicio = ($pagina - 1) * $porPagina;
    return array_slice($elementos, $inicio, $porPagina);
}

/**
 * 5. Valida los datos del formulario de identificación.
 *    Devuelve una lista de errores (vacía si todo está bien).
 */
function validarCliente(string $nombre, string $correo): array
{
    $errores = [];
    if ($nombre === "") {
        $errores[] = "Escriba su nombre.";
    } elseif (mb_strlen($nombre) < 3) {
        $errores[] = "El nombre debe tener al menos 3 letras.";
    }
    if ($correo === "") {
        $errores[] = "Escriba su correo electrónico.";
    } elseif (filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
        $errores[] = "El correo no tiene un formato válido (ejemplo: ana@correo.com).";
    }
    return $errores;
}

/**
 * 6. Promoción automática según el día (RF-10).
 *    $dia es opcional (1 = lunes … 7 = domingo); si no se envía,
 *    se usa la fecha del servidor con date("N").
 */
function promocionDelDia(?int $dia = null): ?array
{
    $dia = $dia ?? (int) date("N");
    return match ($dia) {
        1 => ["categoria" => "bebidas",   "porcentaje" => 10, "texto" => "Lunes de café: 10 % de descuento en bebidas"],
        3 => ["categoria" => "desayunos", "porcentaje" => 10, "texto" => "Miércoles madrugador: 10 % de descuento en desayunos"],
        5 => ["categoria" => "postres",   "porcentaje" => 10, "texto" => "Viernes dulce: 10 % de descuento en postres"],
        default => null,
    };
}

/**
 * 7. Reconstruye el pedido guardado en la sesión como objeto Pedido.
 */
function obtenerPedido(array $menu): Pedido
{
    return new Pedido($menu, $_SESSION["pedido"] ?? []);
}

/** 8. Guarda el pedido en la sesión como arreglo simple. */
function guardarPedido(Pedido $pedido): void
{
    $_SESSION["pedido"] = $pedido->aArreglo();
}

/** 9. Devuelve el cliente identificado, o null si no se ha identificado. */
function obtenerCliente(): ?Cliente
{
    if (!isset($_SESSION["cliente"])) {
        return null;
    }
    return new Cliente($_SESSION["cliente"]["nombre"], $_SESSION["cliente"]["correo"]);
}

/** 10. Guarda un mensaje para mostrarlo en la siguiente página. */
function avisar(string $tipo, string $texto): void
{
    $_SESSION["mensajes"][] = ["tipo" => $tipo, "texto" => $texto];
}

/** 11. Devuelve los mensajes pendientes y los borra de la sesión. */
function tomarMensajes(): array
{
    $mensajes = $_SESSION["mensajes"] ?? [];
    unset($_SESSION["mensajes"]);
    return $mensajes;
}

/** 12. Redirecciona a otra página y detiene el script. */
function redireccionar(string $url): never
{
    header("Location: " . $url);
    exit;
}

/** 13. Enlace de WhatsApp con un mensaje ya escrito. */
function enlaceWhatsApp(string $mensaje = "Hola, quisiera información sobre el menú."): string
{
    return "https://wa.me/" . TELEFONO_WHATSAPP . "?text=" . rawurlencode($mensaje);
}

/** 14. Enlace de Google Maps a la dirección de la cafetería. */
function enlaceMaps(): string
{
    return "https://www.google.com/maps/search/?api=1&query=" . rawurlencode(DIRECCION);
}
