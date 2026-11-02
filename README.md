# Cafetería Los Alpes · Menú en línea

**Proyecto 2 · Programación Web · Unidad 3: Programación de Aplicaciones en el Servidor**
**Estudiante:** Kenneth Rodríguez Cerdas

Aplicación web en **PHP 8 sin frameworks ni base de datos** donde los clientes de la Cafetería Los Alpes pueden:

- ver el menú,
- filtrarlo por categoría y pasar de página,
- identificarse,
- armar un pedido con IVA y confirmarlo.

---

## Requisitos

- **XAMPP** (Apache con PHP 8.1 o superior) o **Laragon**.
- Un navegador moderno, como Chrome, Edge o Firefox.

## Cómo abrir el proyecto en XAMPP

1. Abra el **XAMPP Control Panel** y presione **Start** en **Apache**. MySQL no es necesario.
2. Copie la carpeta **`Cafeteria-Los-Alpes`** completa dentro de `C:\xampp\htdocs\`.
   Debe quedar así: `C:\xampp\htdocs\Cafeteria-Los-Alpes\index.php`
3. Abra el navegador y entre a:
   **http://localhost/Cafeteria-Los-Alpes/**
4. Listo. Empiece por **Menú** para agregar platos.

> **Importante:** no abra los archivos con doble clic. Si la barra de direcciones muestra `file:///…`, el navegador no ejecuta PHP. La dirección siempre debe empezar con `http://localhost/`.

**Con Laragon:** copie la carpeta en `C:\laragon\www\`, presione **Start All** y entre a `http://localhost/Cafeteria-Los-Alpes/`.

**Sin XAMPP (opcional):** dentro de la carpeta del proyecto ejecute `php -S localhost:8000` y entre a `http://localhost:8000/`.

## Estructura de carpetas

```
Cafeteria-Los-Alpes/
├── index.php            RF-01  Inicio: horario, dirección, Google Maps y WhatsApp
├── menu.php             RF-02/03/04  Menú, filtro por categoría ($_GET) y paginación
├── identificarse.php    RF-05/06  Formulario ($_POST) y cookie con el nombre
├── pedido.php           RF-07/10  Mi pedido: cantidades, IVA, total y promoción del día
├── confirmar.php        RF-08  Confirmación (exige identificarse) con número, fecha y hora
├── salir.php            RF-09  Cerrar sesión
├── funciones.php        Constantes y funciones propias
├── datos.php            Menú: 16 platos en 4 categorías (lista de objetos)
├── clases/
│   ├── Mostrable.php    Interfaz con mostrarHtml()
│   ├── Plato.php        Clase abstracta (nombre, precio, categoría, disponible)
│   ├── Comida.php       Hereda de Plato e implementa Mostrable
│   ├── Bebida.php       Hereda de Plato e implementa Mostrable (fría/caliente)
│   ├── Pedido.php       agregar, quitar, subtotal, total + propiedad estática
│   └── Cliente.php      __get y __destruct (modo de prueba)
├── plantillas/
│   ├── encabezado.php   Encabezado con menú de navegación
│   └── pie.php          Pie de página
├── css/estilos.css
└── img/logo.svg
```

## Configuración rápida (`funciones.php`)

| Constante | Para qué sirve |
|---|---|
| `IVA` | Porcentaje de impuesto (0.13) |
| `PLATOS_POR_PAGINA` | Platos por página en el menú (6) |
| `TELEFONO_WHATSAPP` | Número del enlace de WhatsApp (es un número de ejemplo) |
| `MODO_PRUEBA` | Si se cambia a `true`, aparece el mensaje del `__destruct` de `Cliente` |

## Promociones automáticas (RF-10, extra)

La promoción se calcula con la fecha del servidor (`date("N")`):

| Día | Promoción |
|---|---|
| Lunes | 10 % en bebidas |
| Miércoles | 10 % en desayunos |
| Viernes | 10 % en postres |

## Documentación incluida en la entrega

En la carpeta **`Documentacion`** del ZIP, junto a esta carpeta del proyecto:


- `Manual_de_Usuario.pdf`: capturas de cada página y cómo usarla.
- `Documentacion_Tecnica.pdf`: plan, diseño de clases, requerimientos técnicos y pruebas.
- `Guion_Video.pdf`: guía para el video de defensa.
- **Video de defensa:** enlace o archivo agregado por el estudiante.
