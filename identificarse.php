<?php
declare(strict_types=1);

// Autocarga: busca cada clase en la carpeta clases/
spl_autoload_register(function (string $clase): void {
    require __DIR__ . "/clases/" . $clase . ".php";
});

require __DIR__ . "/funciones.php";
require __DIR__ . "/datos.php";
session_start();


/* identificarse.php · RF-05 (formulario por POST) y RF-06 (cookie) */

$titulo = "Identificarse";
$paginaActual = "identificarse";

// ¿A dónde volver después de identificarse?
$volver = (($_GET["volver"] ?? $_POST["volver"] ?? "") === "confirmar") ? "confirmar" : "";

// Opción "No soy yo": borra la cookie con el nombre recordado
if (isset($_GET["olvidar"])) {
    setcookie("nombre_cliente", "", ["expires" => time() - 3600, "path" => "/"]);
    avisar("info", "Listo, ya no recordaremos su nombre en este navegador.");
    redireccionar("identificarse.php");
}

$errores = [];
$nombre = "";
$correo = "";

// --- Procesar el formulario (POST) ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = (string) ($_POST["nombre"] ?? "");
    $correo = (string) ($_POST["correo"] ?? "");

    // Limpieza por referencia: modifica las variables originales
    limpiarTexto($nombre);
    limpiarTexto($correo);
    $correo = strtolower($correo);

    $errores = validarCliente($nombre, $correo);

    if (count($errores) === 0) {
        session_regenerate_id(true);  // buena práctica al identificar al usuario
        $_SESSION["cliente"] = ["nombre" => $nombre, "correo" => $correo];

        // Cookie que recuerda el nombre durante 30 días
        setcookie("nombre_cliente", $nombre, [
            "expires"  => time() + DIAS_COOKIE * 24 * 60 * 60,
            "path"     => "/",
            "httponly" => true,
            "samesite" => "Lax",
        ]);

        $cliente = new Cliente($nombre, $correo);
        avisar("exito", "¡Bienvenido, " . $cliente->primerNombre() . "! Ya puede confirmar su pedido.");
        redireccionar($volver === "confirmar" ? "confirmar.php" : "menu.php");
    }
}

// Si no hay datos escritos, se llena el nombre con la cookie
$nombreRecordado = $_COOKIE["nombre_cliente"] ?? "";
if ($nombre === "" && $_SERVER["REQUEST_METHOD"] !== "POST") {
    $nombre = $nombreRecordado;
}

$clienteActual = obtenerCliente();

require __DIR__ . "/plantillas/encabezado.php";
?>

<section class="panel panel-angosto">
    <h1>Identificarse</h1>

    <?php if ($clienteActual !== null): ?>
        <div class="alerta alerta-info">
            Ya está identificado como <strong><?= htmlspecialchars($clienteActual->nombre) ?></strong>
            (<?= htmlspecialchars($clienteActual->correo) ?>).
            <a href="pedido.php">Ver mi pedido</a> o <a href="salir.php">cerrar sesión</a>.
        </div>
    <?php else: ?>

        <?php if ($nombreRecordado !== ""): ?>
            <p class="bienvenida">¡Hola de nuevo, <?= htmlspecialchars($nombreRecordado) ?>! 👋
                <a href="identificarse.php?olvidar=1" class="enlace-pequeno">¿No es usted?</a></p>
        <?php else: ?>
            <p>Para confirmar un pedido necesitamos su nombre y su correo.</p>
        <?php endif; ?>

        <?php if (count($errores) > 0): ?>
            <div class="alerta alerta-error">
                <strong>Revise los datos:</strong>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- novalidate: la validación se hace en el servidor con PHP -->
        <form action="identificarse.php" method="post" class="formulario" novalidate>
            <input type="hidden" name="volver" value="<?= $volver ?>">

            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" maxlength="60"
                   value="<?= htmlspecialchars($nombre) ?>" placeholder="Ej.: Ana Rodríguez">

            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" maxlength="80"
                   value="<?= htmlspecialchars($correo) ?>" placeholder="ana@correo.com">

            <button type="submit" class="boton boton-grande">Continuar</button>
        </form>
    <?php endif; ?>
</section>

<?php require __DIR__ . "/plantillas/pie.php"; ?>
