<?php
declare(strict_types=1);

/* =============================================================
   datos.php · Menú de la cafetería (16 platos en 4 categorías)
   El menú vive en el código como una lista de objetos.
   ============================================================= */

/**
 * Devuelve el menú completo indexado por el id de cada plato.
 * @return array<int, Plato>
 */
function obtenerMenu(): array
{
    $platos = [
        // ----- Desayunos -----
        new Comida(1, "Gallo pinto tradicional", 2800, "desayunos",
            "Gallo pinto con huevo al gusto, natilla, queso frito y tortilla.", false, true, "🍳"),
        new Comida(2, "Pinto vegetariano", 2700, "desayunos",
            "Gallo pinto con plátano maduro, aguacate y tortilla de maíz.", true, true, "🥑"),
        new Comida(3, "Panqueques de la montaña", 2500, "desayunos",
            "Tres panqueques con miel, fresas de la zona y mantequilla.", true, true, "🥞"),
        new Comida(4, "Empanadas de queso", 1500, "desayunos",
            "Dos empanadas de masa de maíz rellenas de queso Turrialba.", true, true, "🥟"),

        // ----- Almuerzos -----
        new Comida(5, "Casado con pollo", 4200, "almuerzos",
            "Arroz, frijoles, ensalada, plátano maduro y pechuga a la plancha.", false, true, "🍗"),
        new Comida(6, "Casado de bistec encebollado", 4600, "almuerzos",
            "Arroz, frijoles, picadillo de papa, ensalada y bistec encebollado.", false, true, "🥩"),
        new Comida(7, "Olla de carne", 4800, "almuerzos",
            "Sopa tradicional de res con yuca, chayote, elote y arroz aparte.", false, false, "🍲"),
        new Comida(8, "Lasaña de vegetales", 4300, "almuerzos",
            "Lasaña casera con espinaca, hongos, ricota y salsa de tomate.", true, true, "🥬"),

        // ----- Bebidas -----
        new Bebida(9, "Café chorreado", 1200,
            "Café de altura de Coronado, chorreado en bolsita de tela.", true, true, "☕"),
        new Bebida(10, "Chocolate caliente", 1600,
            "Chocolate espeso con leche y un toque de canela.", true, true, "🍫"),
        new Bebida(11, "Batido de fresa", 1800,
            "Fresas frescas de la zona licuadas en leche.", false, true, "🍓"),
        new Bebida(12, "Fresco de cas", 1300,
            "Fresco natural de cas en agua, con poca azúcar.", false, true, "🥤"),

        // ----- Postres -----
        new Comida(13, "Tres leches", 1900, "postres",
            "Bizcocho húmedo en tres leches con merengue y canela.", true, true, "🍰"),
        new Comida(14, "Arroz con leche", 1500, "postres",
            "Arroz con leche cremoso con pasas y canela.", true, true, "🍚"),
        new Comida(15, "Tartaleta de fresas", 2200, "postres",
            "Masa crujiente, crema pastelera y fresas frescas.", true, true, "🥧"),
        new Comida(16, "Brownie con helado", 2400, "postres",
            "Brownie tibio de chocolate con una bola de helado de vainilla.", true, true, "🍨"),
    ];

    // Indexa el arreglo por id para encontrar cada plato rápido
    $menu = [];
    foreach ($platos as $plato) {
        $menu[$plato->getId()] = $plato;
    }
    return $menu;
}
