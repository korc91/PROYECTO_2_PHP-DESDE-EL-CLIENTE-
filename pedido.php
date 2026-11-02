<?php
declare(strict_types=1);

// Autocarga: busca cada clase en la carpeta clases/
spl_autoload_register(function (string $clase): void {
    require __DIR__ . "/clases/" . $clase . ".php";
});

require __DIR__ . "/funciones.php";
require __DIR__ . "/datos.php";
session_start();


/* pedido.php · RF-07 (pedido con IVA) y RF-10 (promoción del día) */

$titulo = "Mi pedido";
$paginaActual = "pedido";

$menu = obtenerMenu();
$pedido = obtenerPedido($menu);

// --- Procesar acciones del formulario (POST) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $accion = (string) ($_POST["accion"] ?? "");
    $idPlato = (int) ($_POST["id"] ?? 0);
    $destino = "pedido.php";

    try {
        switch ($accion) {
            case "agregar":
                $cantidad = leerCantidad((string) ($_POST["cantidad"] ?? ""));
                $pedido->agregar($idPlato, $cantidad);
                avisar("exito", "Se agregó " . $cantidad . " × " . $menu[$idPlato]->getNombre() . " a su pedido.");
                // Regresa a la página del menú donde estaba el cliente
                $volver = (string) ($_POST["volver"] ?? "");
                if (str_starts_with($volver, "menu.php") || $volver === "index.php") {
                    $destino = $volver;
                }
                break;

            case "actualizar":
                $cantidad = leerCantidad((string) ($_POST["cantidad"] ?? ""));
                $pedido->cambiarCantidad($idPlato, $cantidad);
                avisar("exito", "Cantidad actualizada.");
                break;

            case "quitar":
                $pedido->quitar($idPlato);
                avisar("info", "Se quitó el plato del pedido.");
                break;

            case "vaciar":
                $pedido->vaciar();
                avisar("info", "Su pedido quedó vacío.");
                break;

            default:
                throw new InvalidArgumentException("Acción no reconocida.");
        }
        guardarPedido($pedido);
    } catch (InvalidArgumentException $e) {
        // Mensaje amable en lugar de un error de PHP
        avisar("error", $e->getMessage());
    }

    // Redirección después de procesar el formulario (evita reenvíos al recargar)
    redireccionar($destino);
}

/**
 * Convierte el texto de la cantidad en entero.
 * Lanza una excepción si no es un número entero.
 */
function leerCantidad(string $texto): int
{
    $cantidad = filter_var($texto, FILTER_VALIDATE_INT);
    if ($cantidad === false) {
        throw new InvalidArgumentException("La cantidad debe ser un número entero, por ejemplo 1 o 2.");
    }
    return $cantidad;
}

$promocion = promocionDelDia();
$lineas = $pedido->lineas();

require __DIR__ . "/plantillas/encabezado.php";
?>

<h1>Mi pedido</h1>

<?php if ($pedido->estaVacio()): ?>
    <div class="panel vacio">
        <p>🛒 Su pedido está vacío.</p>
        <a href="menu.php" class="boton">Ir al menú</a>
    </div>
<?php else: ?>

    <div class="tabla-contenedor">
        <table class="tabla-pedido">
            <thead>
                <tr>
                    <th>Plato</th>
                    <th class="num">Precio</th>
                    <th>Cantidad</th>
                    <th class="num">Importe</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($lineas as $linea): ?>
                <?php $plato = $linea["plato"]; ?>
                <tr>
                    <td>
                        <?= $plato->getIcono() ?> <strong><?= htmlspecialchars($plato->getNombre()) ?></strong><br>
                        <small><?= nombreCategoria($plato->getCategoria()) ?></small>
                    </td>
                    <td class="num"><?= formatearColones($plato->getPrecio()) ?></td>
                    <td>
                        <form action="pedido.php" method="post" class="form-linea">
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="hidden" name="id" value="<?= $plato->getId() ?>">
                            <input type="number" name="cantidad" value="<?= $linea["cantidad"] ?>" min="1" max="<?= MAX_CANTIDAD ?>" aria-label="Cantidad de <?= htmlspecialchars($plato->getNombre()) ?>">
                            <button type="submit" class="boton boton-pequeno">Cambiar</button>
                        </form>
                    </td>
                    <td class="num"><?= formatearColones($linea["importe"]) ?></td>
                    <td>
                        <form action="pedido.php" method="post">
                            <input type="hidden" name="accion" value="quitar">
                            <input type="hidden" name="id" value="<?= $plato->getId() ?>">
                            <button type="submit" class="boton boton-peligro boton-pequeno">Quitar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="resumen-fila">
        <form action="pedido.php" method="post">
            <input type="hidden" name="accion" value="vaciar">
            <button type="submit" class="boton boton-secundario">Vaciar pedido</button>
        </form>

        <div class="totales">
            <p><span>Subtotal</span><span><?= formatearColones($pedido->subtotal()) ?></span></p>
            <?php if ($pedido->descuento($promocion) > 0): ?>
                <p class="descuento"><span><?= htmlspecialchars($promocion["texto"]) ?></span>
                    <span>−<?= formatearColones($pedido->descuento($promocion)) ?></span></p>
            <?php endif; ?>
            <p><span>IVA (<?= IVA * 100 ?> %)</span><span><?= formatearColones($pedido->iva($promocion)) ?></span></p>
            <p class="total"><span>Total</span><span><?= formatearColones($pedido->total($promocion)) ?></span></p>
            <a href="confirmar.php" class="boton boton-grande">Confirmar pedido</a>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . "/plantillas/pie.php"; ?>
