<?php
declare(strict_types=1);

// Autocarga: busca cada clase en la carpeta clases/
spl_autoload_register(function (string $clase): void {
    require __DIR__ . "/clases/" . $clase . ".php";
});

require __DIR__ . "/funciones.php";
require __DIR__ . "/datos.php";
session_start();


/* salir.php · RF-09: cerrar sesión, borrar pedido e identificación */

// Vacía todas las variables de sesión y destruye la sesión
$_SESSION = [];
session_destroy();

// Nueva sesión con un id distinto, solo para mostrar la despedida.
// session_regenerate_id cambia el identificador, así la sesión anterior
// (con el pedido y el cliente) ya no se puede recuperar.
session_start();
session_regenerate_id(true);

avisar("info", "Sesión cerrada. Su pedido y sus datos fueron borrados. ¡Vuelva pronto!");
redireccionar("index.php");
