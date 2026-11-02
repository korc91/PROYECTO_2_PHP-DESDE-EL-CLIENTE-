<?php
declare(strict_types=1);
/* plantillas/pie.php · Se incluye con require al final de cada página. */
?>
</main>

<footer class="pie">
    <div class="contenedor pie-fila">
        <div>
            <strong><?= NOMBRE_CAFETERIA ?></strong><br>
            <?= DIRECCION ?>
        </div>
        <div>
            <a href="<?= enlaceMaps() ?>" target="_blank" rel="noopener noreferrer">📍 Google Maps</a> ·
            <a href="<?= enlaceWhatsApp() ?>" target="_blank" rel="noopener noreferrer">💬 WhatsApp</a>
        </div>
        <div class="pie-legal">
            &copy; <?= date("Y") ?> · Proyecto 2 de Programación Web · Kenneth Rodríguez Cerdas
        </div>
    </div>
</footer>

</body>
</html>
