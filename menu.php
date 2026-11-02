<?php
declare(strict_types=1);

// Autocarga: busca cada clase en la carpeta clases/
spl_autoload_register(function (string $clase): void {
    require __DIR__ . "/clases/" . $clase . ".php";
});

require __DIR__ . "/funciones.php";
require __DIR__ . "/datos.php";
session_start();


/* menu.php · RF-02 (menú), RF-03 (filtro por $_GET) y RF-04 (paginación) */

$titulo = "Menú";
$paginaActual = "menu";

$menu = obtenerMenu();

// --- Filtro por categoría recibido por GET ---
$categoria = (string) ($_GET["categoria"] ?? "");
if (!in_array($categoria, CATEGORIAS, true)) {
    $categoria = "";   // valor desconocido = mostrar todo
}

$platos = array_values($menu);
if ($categoria !== "") {
    $platos = array_values(array_filter(
        $platos,
        fn (Plato $plato): bool => $plato->getCategoria() === $categoria
    ));
}

// --- Paginación: 6 platos por página ---
$totalPaginas = max(1, (int) ceil(count($platos) / PLATOS_POR_PAGINA));
$pagina = filter_var($_GET["pagina"] ?? 1, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if ($pagina === false) {
    $pagina = 1;
}
$pagina = min($pagina, $totalPaginas);
$platosPagina = paginar($platos, $pagina);

// Arma la URL de una página conservando la categoría elegida
function urlMenu(string $categoria, int $pagina): string
{
    $parametros = ["pagina" => $pagina];
    if ($categoria !== "") {
        $parametros = ["categoria" => $categoria] + $parametros;
    }
    return "menu.php?" . http_build_query($parametros);
}

$urlActual = urlMenu($categoria, $pagina);

require __DIR__ . "/plantillas/encabezado.php";
?>

<h1>Nuestro menú</h1>
<p class="subtitulo">
    <?= $categoria === "" ? "Todos los platos" : nombreCategoria($categoria) ?>
    · <?= count($platos) ?> platos · Precios con IVA no incluido
</p>

<!-- Filtro: cada enlace envía la categoría por GET -->
<nav class="filtros" aria-label="Filtrar por categoría">
    <a href="menu.php" class="<?= $categoria === "" ? "activo" : "" ?>">Todos</a>
    <?php foreach (CATEGORIAS as $cat): ?>
        <a href="menu.php?categoria=<?= $cat ?>" class="<?= $categoria === $cat ? "activo" : "" ?>">
            <?= nombreCategoria($cat) ?>
        </a>
    <?php endforeach; ?>
</nav>

<div class="grilla">
    <?php foreach ($platosPagina as $plato): ?>
        <?= $plato->mostrarHtml($urlActual) ?>
    <?php endforeach; ?>
</div>

<!-- Paginación -->
<nav class="paginacion" aria-label="Páginas del menú">
    <?php if ($pagina > 1): ?>
        <a href="<?= urlMenu($categoria, $pagina - 1) ?>" class="boton boton-secundario">« Anterior</a>
    <?php else: ?>
        <span class="boton boton-secundario deshabilitado">« Anterior</span>
    <?php endif; ?>

    <span>Página <?= $pagina ?> de <?= $totalPaginas ?></span>

    <?php if ($pagina < $totalPaginas): ?>
        <a href="<?= urlMenu($categoria, $pagina + 1) ?>" class="boton boton-secundario">Siguiente »</a>
    <?php else: ?>
        <span class="boton boton-secundario deshabilitado">Siguiente »</span>
    <?php endif; ?>
</nav>

<?php require __DIR__ . "/plantillas/pie.php"; ?>
