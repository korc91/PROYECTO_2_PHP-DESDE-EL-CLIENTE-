<?php
declare(strict_types=1);
/*
 * plantillas/encabezado.php
 * Se incluye con require al inicio de cada página.
 * Usa las variables $titulo y $paginaActual definidas en la página.
 */
$clienteMenu = obtenerCliente();
$articulosEnPedido = obtenerPedido(obtenerMenu())->cantidadArticulos();
$mensajes = tomarMensajes();
$promoHoy = promocionDelDia();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> | <?= NOMBRE_CAFETERIA ?></title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<?php if ($promoHoy !== null): ?>
    <div class="franja-promo">🎉 Promoción de hoy: <?= htmlspecialchars($promoHoy["texto"]) ?></div>
<?php endif; ?>

<header class="encabezado">
    <div class="contenedor encabezado-fila">
        <a href="index.php" class="marca">
            <img src="img/logo.svg" alt="Logo de la Cafetería Los Alpes: montañas y una taza de café" width="44" height="44">
            <span><?= NOMBRE_CAFETERIA ?></span>
        </a>

        <nav class="navegacion" aria-label="Menú principal">
            <a href="index.php" class="<?= $paginaActual === "inicio" ? "activo" : "" ?>">Inicio</a>
            <a href="menu.php" class="<?= $paginaActual === "menu" ? "activo" : "" ?>">Menú</a>
            <a href="pedido.php" class="<?= $paginaActual === "pedido" ? "activo" : "" ?>">
                Mi pedido<?php if ($articulosEnPedido > 0): ?> <span class="contador"><?= $articulosEnPedido ?></span><?php endif; ?>
            </a>
            <?php if ($clienteMenu === null): ?>
                <a href="identificarse.php" class="<?= $paginaActual === "identificarse" ? "activo" : "" ?>">Identificarse</a>
            <?php else: ?>
                <span class="saludo">👤 <?= htmlspecialchars($clienteMenu->primerNombre()) ?></span>
                <a href="salir.php" class="salir">Cerrar sesión</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="contenedor contenido">
    <?php foreach ($mensajes as $mensaje): ?>
        <div class="alerta alerta-<?= htmlspecialchars($mensaje["tipo"]) ?>"><?= htmlspecialchars($mensaje["texto"]) ?></div>
    <?php endforeach; ?>
