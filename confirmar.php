<?php
declare(strict_types=1);

// Autocarga: busca cada clase en la carpeta clases/
spl_autoload_register(function (string $clase): void {
    require __DIR__ . "/clases/" . $clase . ".php";
});

require __DIR__ . "/funciones.php";
require __DIR__ . "/datos.php";
session_start();


/* confirmar.php · RF-08: confirmar solo si el cliente se identificó */

$titulo = "Confirmar pedido";
$paginaActual = "pedido";

$menu = obtenerMenu();
$cliente = obtenerCliente();

// Sin identificación no se puede confirmar: redirecciona
if ($cliente === null) {
    avisar("info", "Para confirmar su pedido, primero identifíquese.");
    redireccionar("identificarse.php?volver=confirmar");
}

$pedido = obtenerPedido($menu);
$promocion = promocionDelDia();

// --- El cliente presionó "Confirmar" (POST) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($pedido->estaVacio()) {
        avisar("error", "No hay platos en su pedido.");
        redireccionar("menu.php");
    }

    // La propiedad estática continúa la cuenta guardada en la sesión
    Pedido::$pedidosConfirmados = (int) ($_SESSION["pedidos_confirmados"] ?? 0);
    $numero = $pedido->confirmar();
    $_SESSION["pedidos_confirmados"] = Pedido::$pedidosConfirmados;

    // Resumen que se mostrará después de redireccionar
    $resumenLineas = [];
    foreach ($pedido->lineas() as $linea) {
        $resumenLineas[] = [
            "nombre"   => $linea["plato"]->getNombre(),
            "cantidad" => $linea["cantidad"],
            "importe"  => $linea["importe"],
        ];
    }
    $_SESSION["ultimo_pedido"] = [
        "numero"    => $numero,
        "fecha"     => date("d/m/Y"),
        "hora"      => date("g:i a"),
        "cliente"   => $cliente->nombre,
        "correo"    => $cliente->correo,
        "lineas"    => $resumenLineas,
        "subtotal"  => $pedido->subtotal(),
        "descuento" => $pedido->descuento($promocion),
        "iva"       => $pedido->iva($promocion),
        "total"     => $pedido->total($promocion),
    ];

    $pedido->vaciar();
    guardarPedido($pedido);
    redireccionar("confirmar.php?listo=1");
}

$listo = isset($_GET["listo"]) && isset($_SESSION["ultimo_pedido"]);

// Si no hay nada que confirmar ni resumen que mostrar, vuelve al menú
if (!$listo && $pedido->estaVacio()) {
    avisar("info", "Su pedido está vacío. Agregue platos desde el menú.");
    redireccionar("menu.php");
}

require __DIR__ . "/plantillas/encabezado.php";
?>

<?php if ($listo): ?>
    <?php $resumen = $_SESSION["ultimo_pedido"]; ?>
    <section class="panel recibo">
        <p class="recibo-icono">✅</p>
        <h1>¡Pedido confirmado!</h1>
        <p class="numero-pedido">Pedido N.º <strong><?= htmlspecialchars($resumen["numero"]) ?></strong></p>
        <p>Fecha: <strong><?= $resumen["fecha"] ?></strong> · Hora: <strong><?= $resumen["hora"] ?></strong></p>
        <p>Cliente: <strong><?= htmlspecialchars($resumen["cliente"]) ?></strong> (<?= htmlspecialchars($resumen["correo"]) ?>)</p>

        <table class="tabla-pedido">
            <thead><tr><th>Plato</th><th class="num">Cant.</th><th class="num">Importe</th></tr></thead>
            <tbody>
            <?php foreach ($resumen["lineas"] as $linea): ?>
                <tr>
                    <td><?= htmlspecialchars($linea["nombre"]) ?></td>
                    <td class="num"><?= $linea["cantidad"] ?></td>
                    <td class="num"><?= formatearColones($linea["importe"]) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totales totales-recibo">
            <p><span>Subtotal</span><span><?= formatearColones($resumen["subtotal"]) ?></span></p>
            <?php if ($resumen["descuento"] > 0): ?>
                <p class="descuento"><span>Descuento del día</span><span>−<?= formatearColones($resumen["descuento"]) ?></span></p>
            <?php endif; ?>
            <p><span>IVA (<?= IVA * 100 ?> %)</span><span><?= formatearColones($resumen["iva"]) ?></span></p>
            <p class="total"><span>Total</span><span><?= formatearColones($resumen["total"]) ?></span></p>
        </div>

        <p>Le esperamos en <?= NOMBRE_CAFETERIA ?>. Presente este número al retirar su pedido.</p>
        <a href="menu.php" class="boton">Hacer otro pedido</a>
    </section>
<?php else: ?>
    <section class="panel">
        <h1>Revisar y confirmar</h1>
        <p>Pedido a nombre de <strong><?= htmlspecialchars($cliente->nombre) ?></strong> (<?= htmlspecialchars($cliente->correo) ?>).</p>

        <table class="tabla-pedido">
            <thead><tr><th>Plato</th><th class="num">Cant.</th><th class="num">Importe</th></tr></thead>
            <tbody>
            <?php foreach ($pedido->lineas() as $linea): ?>
                <tr>
                    <td><?= $linea["plato"]->getIcono() ?> <?= htmlspecialchars($linea["plato"]->getNombre()) ?></td>
                    <td class="num"><?= $linea["cantidad"] ?></td>
                    <td class="num"><?= formatearColones($linea["importe"]) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totales totales-recibo">
            <p><span>Subtotal</span><span><?= formatearColones($pedido->subtotal()) ?></span></p>
            <?php if ($pedido->descuento($promocion) > 0): ?>
                <p class="descuento"><span><?= htmlspecialchars($promocion["texto"]) ?></span><span>−<?= formatearColones($pedido->descuento($promocion)) ?></span></p>
            <?php endif; ?>
            <p><span>IVA (<?= IVA * 100 ?> %)</span><span><?= formatearColones($pedido->iva($promocion)) ?></span></p>
            <p class="total"><span>Total</span><span><?= formatearColones($pedido->total($promocion)) ?></span></p>
        </div>

        <div class="acciones">
            <a href="pedido.php" class="boton boton-secundario">« Modificar pedido</a>
            <form action="confirmar.php" method="post">
                <button type="submit" class="boton boton-grande">Confirmar pedido</button>
            </form>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . "/plantillas/pie.php"; ?>
