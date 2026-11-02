<?php
declare(strict_types=1);

// Autocarga: busca cada clase en la carpeta clases/
spl_autoload_register(function (string $clase): void {
    require __DIR__ . "/clases/" . $clase . ".php";
});

require __DIR__ . "/funciones.php";
require __DIR__ . "/datos.php";
session_start();


/* index.php · RF-01: página de inicio con datos de la cafetería */

$titulo = "Inicio";
$paginaActual = "inicio";

$menu = obtenerMenu();
// Tres platos destacados para la portada
$destacados = [$menu[1], $menu[9], $menu[13]];

// Si hay cookie, el cliente ya nos visitó antes (RF-06)
$nombreRecordado = $_COOKIE["nombre_cliente"] ?? "";

require __DIR__ . "/plantillas/encabezado.php";
?>

<section class="portada">
    <div class="portada-texto">
        <?php if ($nombreRecordado !== ""): ?>
            <p class="bienvenida">¡Hola de nuevo, <?= htmlspecialchars($nombreRecordado) ?>! 👋</p>
        <?php endif; ?>
        <h1><?= NOMBRE_CAFETERIA ?></h1>
        <p class="eslogan"><?= ESLOGAN ?></p>
        <p>Vea nuestro menú, arme su pedido desde aquí y recójalo listo al llegar. Sin llamadas ni filas.</p>
        <a href="menu.php" class="boton boton-grande">Ver el menú</a>
    </div>

    <div class="info-cafeteria">
        <h2>Visítenos</h2>
        <p><strong>🕒 Horario</strong><br><?= HORARIO ?></p>
        <p><strong>📍 Dirección</strong><br><?= DIRECCION ?></p>
        <div class="botones-externos">
            <!-- Enlaces externos que abren en otra pestaña -->
            <a href="<?= enlaceMaps() ?>" target="_blank" rel="noopener noreferrer" class="boton boton-mapa">Cómo llegar (Google Maps)</a>
            <a href="<?= enlaceWhatsApp() ?>" target="_blank" rel="noopener noreferrer" class="boton boton-whatsapp">Escribir por WhatsApp</a>
        </div>
    </div>
</section>

<section>
    <h2 class="titulo-seccion">Nuestras categorías</h2>
    <div class="categorias">
        <?php foreach (CATEGORIAS as $categoria): ?>
            <a href="menu.php?categoria=<?= $categoria ?>" class="categoria-enlace">
                <?= nombreCategoria($categoria) ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section>
    <h2 class="titulo-seccion">Los favoritos de la casa</h2>
    <div class="grilla">
        <?php foreach ($destacados as $plato): ?>
            <?= $plato->mostrarHtml("index.php") ?>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . "/plantillas/pie.php"; ?>
