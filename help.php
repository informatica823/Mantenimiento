<?php
/**
 * Ayuda de Nube InSSA
 * -------------------
 * Manual de usuario de Nube InSSA en un solo archivo.
 *
 * Qué hace este archivo:
 *   - Detecta qué aplicaciones están instaladas mirando las carpetas de la
 *     instalación, en modo SOLO LECTURA (file_exists / is_dir). No escribe
 *     nada, no toca la base de datos, no ejecuta comandos y no muestra rutas
 *     internas del servidor.
 *   - Muestra sólo las secciones de ayuda de las aplicaciones que encontró.
 *   - Usa los íconos SVG reales de cada aplicación (img/app.svg). Si una
 *     aplicación no trae ícono, usa un dibujo propio incluido en este archivo.
 *
 * Todo lo que se puede ajustar está en el bloque CONFIGURACIÓN de abajo.
 */

declare(strict_types=1);

/* =========================================================================
 * CONFIGURACIÓN
 * ========================================================================= */

// Dirección pública de Nube InSSA. Se usa para los íconos y para los botones
// "Abrir" que llevan a cada aplicación.
const NUBE_URL = 'https://nube.inssa.net.ar';

// Carpeta de la instalación en el servidor. Dejala vacía para que se busque
// sola (se prueba la carpeta de este archivo, sus carpetas superiores y las
// ubicaciones habituales de /var/www y /srv). Si la búsqueda automática no la
// encuentra, escribí acá la ruta completa. Nunca se muestra en pantalla.
const NUBE_DIR = '';

// Si la detección automática no es posible (por ejemplo, porque help.php está
// en otro servidor), listá acá los identificadores de las aplicaciones que
// tiene Nube InSSA. Ejemplo: ['contacts', 'calendar', 'mail', 'onlyoffice'].
// Las aplicaciones básicas (archivos, actividad, notificaciones, etc.) se
// muestran siempre.
$APPS_MANUALES = [];

// Aplicaciones que están en el servidor pero que la institución desactivó.
// Las que pongas acá no aparecen en la ayuda. Ejemplo: ['deck', 'polls'].
$APPS_OCULTAS = [];

// Módulos propios de InSSA. Poné 'activo' => false para ocultar uno.
// 'url'   : dirección del módulo dentro de Nube InSSA (vacío = sin botón).
//           Ejemplo: '/index.php/apps/external/1'
// 'icono' : ruta de un SVG propio (vacío = dibujo incluido en este archivo).
$MODULOS = [
    'cursos'       => ['activo' => true, 'url' => '', 'icono' => ''],
    'editor'       => ['activo' => true, 'url' => '', 'icono' => ''],
    'publicacion'  => ['activo' => true, 'url' => '', 'icono' => ''],
    'estadisticas' => ['activo' => true, 'url' => '', 'icono' => ''],
];

/* =========================================================================
 * CATÁLOGO DE APLICACIONES
 * Cada entrada dice cómo se llama la aplicación en la ayuda, a qué dirección
 * lleva el botón "Abrir" y qué archivos SVG probar como ícono (en orden).
 * 'core' es un ícono de respaldo que viene con la plataforma misma.
 * 'basica' => true significa que forma parte de la plataforma y se asume
 * presente aunque no se pueda detectar.
 * ========================================================================= */

$CATALOGO = [
    'files'                 => ['nombre' => 'Archivos',            'ruta' => '/index.php/apps/files/',     'iconos' => ['img/app.svg'], 'basica' => true],
    'files_sharing'         => ['nombre' => 'Compartir',           'ruta' => '',                           'iconos' => ['img/app.svg'], 'core' => 'img/actions/share.svg', 'basica' => true],
    'files_trashbin'        => ['nombre' => 'Papelera',            'ruta' => '',                           'iconos' => ['img/app.svg'], 'core' => 'img/actions/delete.svg', 'basica' => true],
    'files_versions'        => ['nombre' => 'Versiones',           'ruta' => '',                           'iconos' => ['img/app.svg'], 'core' => 'img/actions/history.svg', 'basica' => true],
    'comments'              => ['nombre' => 'Comentarios',         'ruta' => '',                           'iconos' => ['img/app.svg', 'img/comments.svg'], 'basica' => true],
    'systemtags'            => ['nombre' => 'Etiquetas',           'ruta' => '',                           'iconos' => ['img/app.svg'], 'core' => 'img/actions/tag.svg', 'basica' => true],
    'activity'              => ['nombre' => 'Actividad',           'ruta' => '/index.php/apps/activity/',  'iconos' => ['img/app.svg', 'img/activity.svg'], 'basica' => true],
    'notifications'         => ['nombre' => 'Notificaciones',      'ruta' => '',                           'iconos' => ['img/app.svg', 'img/notifications.svg'], 'basica' => true],
    'dashboard'             => ['nombre' => 'Panel',               'ruta' => '/index.php/apps/dashboard/', 'iconos' => ['img/app.svg', 'img/dashboard.svg']],
    'settings'              => ['nombre' => 'Configuración',       'ruta' => '/index.php/settings/user',   'iconos' => ['img/personal.svg', 'img/app.svg'], 'core' => 'img/actions/settings-dark.svg', 'basica' => true],
    'dav'                   => ['nombre' => 'Sincronización',      'ruta' => '',                           'iconos' => ['img/app.svg'], 'basica' => true],
    'onlyoffice'            => ['nombre' => 'Documentos',          'ruta' => '',                           'iconos' => ['img/app.svg', 'img/app-dark.svg']],
    'text'                  => ['nombre' => 'Texto',               'ruta' => '',                           'iconos' => ['img/app.svg']],
    'photos'                => ['nombre' => 'Fotos',               'ruta' => '/index.php/apps/photos/',    'iconos' => ['img/app.svg', 'img/photos.svg']],
    'contacts'              => ['nombre' => 'Contactos',           'ruta' => '/index.php/apps/contacts/',  'iconos' => ['img/app.svg']],
    'calendar'              => ['nombre' => 'Calendario',          'ruta' => '/index.php/apps/calendar/',  'iconos' => ['img/app.svg']],
    'mail'                  => ['nombre' => 'Correo',              'ruta' => '/index.php/apps/mail/',      'iconos' => ['img/app.svg', 'img/mail.svg']],
    'forms'                 => ['nombre' => 'Formularios',         'ruta' => '/index.php/apps/forms/',     'iconos' => ['img/app.svg', 'img/forms.svg']],
    'deck'                  => ['nombre' => 'Tableros',            'ruta' => '/index.php/apps/deck/',      'iconos' => ['img/app.svg', 'img/deck.svg']],
    'tasks'                 => ['nombre' => 'Tareas',              'ruta' => '/index.php/apps/tasks/',     'iconos' => ['img/app.svg', 'img/tasks.svg']],
    'notes'                 => ['nombre' => 'Notas',               'ruta' => '/index.php/apps/notes/',     'iconos' => ['img/app.svg', 'img/notes.svg']],
    'spreed'                => ['nombre' => 'Conversaciones',      'ruta' => '/index.php/apps/spreed/',    'iconos' => ['img/app.svg']],
    'polls'                 => ['nombre' => 'Encuestas',           'ruta' => '/index.php/apps/polls/',     'iconos' => ['img/app.svg', 'img/polls.svg']],
    'bookmarks'             => ['nombre' => 'Marcadores',          'ruta' => '/index.php/apps/bookmarks/', 'iconos' => ['img/app.svg', 'img/bookmarks.svg']],
    'groupfolders'          => ['nombre' => 'Carpetas de grupo',   'ruta' => '',                           'iconos' => ['img/app.svg', 'img/folder-group.svg']],
    'external'              => ['nombre' => 'Sitios externos',     'ruta' => '',                           'iconos' => ['img/app.svg', 'img/external.svg']],
    'analytics'             => ['nombre' => 'Análisis',            'ruta' => '/index.php/apps/analytics/', 'iconos' => ['img/app.svg']],
    'serverinfo'            => ['nombre' => 'Información del sistema', 'ruta' => '/index.php/settings/admin/serverinfo', 'iconos' => ['img/app.svg']],
    'theming'               => ['nombre' => 'Apariencia',          'ruta' => '',                           'iconos' => ['img/app.svg', 'img/theming.svg']],
    'twofactor_totp'        => ['nombre' => 'Verificación en dos pasos', 'ruta' => '',                     'iconos' => ['img/app.svg']],
    'twofactor_backupcodes' => ['nombre' => 'Códigos de respaldo', 'ruta' => '',                           'iconos' => ['img/app.svg']],
    'password_policy'       => ['nombre' => 'Política de contraseñas', 'ruta' => '',                       'iconos' => ['img/app.svg']],
    'encryption'            => ['nombre' => 'Cifrado',             'ruta' => '',                           'iconos' => ['img/app.svg']],
    'end_to_end_encryption' => ['nombre' => 'Cifrado de extremo a extremo', 'ruta' => '',                  'iconos' => ['img/app.svg']],
    'privacy'               => ['nombre' => 'Privacidad',          'ruta' => '',                           'iconos' => ['img/app.svg']],
    'user_status'           => ['nombre' => 'Estado',              'ruta' => '',                           'iconos' => ['img/app.svg']],
];

/* =========================================================================
 * FUNCIONES AUXILIARES
 * ========================================================================= */

/** Escapa texto para mostrarlo en HTML. Toda salida dinámica pasa por acá. */
function h(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** ¿La carpeta parece ser la instalación de la plataforma? */
function es_instalacion(string $dir): bool
{
    return @is_file($dir . '/occ') && @is_file($dir . '/apps/files/appinfo/info.xml');
}

/** Busca la carpeta de la instalación. Sólo lee; devuelve null si no la halla. */
function buscar_instalacion(): ?string
{
    $candidatas = [];
    if (NUBE_DIR !== '') {
        $candidatas[] = NUBE_DIR;
    }
    $dir = __DIR__;
    for ($i = 0; $i < 4; $i++) {
        $candidatas[] = $dir;
        $dir = dirname($dir);
    }
    foreach (['/var/www', '/srv', '/srv/www', '/usr/share'] as $base) {
        $candidatas[] = $base;
        $candidatas[] = $base . '/html';
        $hijas = @glob($base . '/*', GLOB_ONLYDIR);
        if (is_array($hijas)) {
            foreach (array_slice($hijas, 0, 40) as $hija) {
                $candidatas[] = $hija;
            }
        }
    }
    foreach (array_unique($candidatas) as $c) {
        $c = rtrim($c, '/');
        if ($c !== '' && es_instalacion($c)) {
            return $c;
        }
    }
    return null;
}

/** Carpetas de aplicaciones de la instalación: apps, custom_apps, etc. */
function carpetas_de_apps(string $raiz): array
{
    $carpetas = [];
    $hijas = @glob($raiz . '/*', GLOB_ONLYDIR);
    if (is_array($hijas)) {
        foreach ($hijas as $hija) {
            $nombre = basename($hija);
            if (preg_match('/^(apps[\w-]*|[\w-]*_apps)$/', $nombre)) {
                $carpetas[] = $nombre;
            }
        }
    }
    // Las instaladas por la institución primero, las de fábrica al final.
    usort($carpetas, fn($a, $b) => ($a === 'apps') <=> ($b === 'apps'));
    return $carpetas;
}

/** Versión principal de una aplicación, leída de su appinfo/info.xml. */
function version_principal(string $archivo): int
{
    $xml = @file_get_contents($archivo, false, null, 0, 20000);
    if (is_string($xml) && preg_match('#<version>\s*(\d+)#', $xml, $m)) {
        return (int) $m[1];
    }
    return 0;
}

/**
 * Detecta las aplicaciones. Devuelve, por cada id del catálogo:
 *   ['instalada' => bool, 'icono' => URL del SVG o '', 'version' => int]
 */
function detectar_apps(?string $raiz, array $catalogo, array $manuales, array $ocultas): array
{
    $res = [];
    $carpetas = $raiz !== null ? carpetas_de_apps($raiz) : [];

    foreach ($catalogo as $id => $info) {
        $dato = ['instalada' => false, 'icono' => '', 'version' => 0];

        if ($raiz !== null) {
            foreach ($carpetas as $carpeta) {
                $base = $raiz . '/' . $carpeta . '/' . $id;
                if (!@is_file($base . '/appinfo/info.xml')) {
                    continue;
                }
                $dato['instalada'] = true;
                $dato['version'] = version_principal($base . '/appinfo/info.xml');
                foreach ($info['iconos'] as $svg) {
                    if (@is_file($base . '/' . $svg)) {
                        $dato['icono'] = NUBE_URL . '/' . $carpeta . '/' . $id . '/' . $svg;
                        break;
                    }
                }
                break;
            }
            if ($dato['icono'] === '' && !empty($info['core']) && @is_file($raiz . '/core/' . $info['core'])) {
                $dato['icono'] = NUBE_URL . '/core/' . $info['core'];
            }
        } else {
            // Sin acceso a la instalación: sólo lo básico y lo declarado a mano.
            // Para esas se usa la ubicación estándar del ícono de fábrica.
            $dato['instalada'] = !empty($info['basica']) || in_array($id, $manuales, true);
            if (!empty($info['basica']) && $info['iconos'][0] === 'img/app.svg' && $id === 'files') {
                $dato['icono'] = NUBE_URL . '/apps/files/img/app.svg';
            }
        }

        if (in_array($id, $ocultas, true)) {
            $dato['instalada'] = false;
        }
        $res[$id] = $dato;
    }
    return $res;
}

/* ---- Íconos de respaldo (dibujos propios, sin archivos externos) ---- */

const ICONOS_PROPIOS = [
    'inicio'     => 'M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z',
    'pasos'      => 'M14.4,6L14,4H5V21H7V14H12.6L13,16H20V6H14.4Z',
    'buscar'     => 'M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z',
    'compartir'  => 'M18,16.08C17.24,16.08 16.56,16.38 16.04,16.85L8.91,12.7C8.96,12.47 9,12.24 9,12C9,11.76 8.96,11.53 8.91,11.3L15.96,7.19C16.5,7.69 17.21,8 18,8A3,3 0 0,0 21,5A3,3 0 0,0 18,2A3,3 0 0,0 15,5C15,5.24 15.04,5.47 15.09,5.7L8.04,9.81C7.5,9.31 6.79,9 6,9A3,3 0 0,0 3,12A3,3 0 0,0 6,15C6.79,15 7.5,14.69 8.04,14.19L15.16,18.34C15.11,18.55 15.08,18.77 15.08,19C15.08,20.61 16.39,21.91 18,21.91C19.61,21.91 20.92,20.61 20.92,19A2.92,2.92 0 0,0 18,16.08Z',
    'papelera'   => 'M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z',
    'campana'    => 'M21,19V20H3V19L5,17V11C5,7.9 7.03,5.17 10,4.29C10,4.19 10,4.1 10,4A2,2 0 0,1 12,2A2,2 0 0,1 14,4C14,4.1 14,4.19 14,4.29C16.97,5.17 19,7.9 19,11V17L21,19M14,21A2,2 0 0,1 12,23A2,2 0 0,1 10,21',
    'engranaje'  => 'M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.05,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z',
    'escudo'     => 'M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z',
    'movil'      => 'M17,19H7V5H17M17,1H7C5.89,1 5,1.89 5,3V21A2,2 0 0,0 7,23H17A2,2 0 0,0 19,21V3C19,1.89 18.1,1 17,1Z',
    'grupo'      => 'M12,5.5A3.5,3.5 0 0,1 15.5,9A3.5,3.5 0 0,1 12,12.5A3.5,3.5 0 0,1 8.5,9A3.5,3.5 0 0,1 12,5.5M5,8C5.56,8 6.08,8.15 6.53,8.42C6.38,9.85 6.8,11.27 7.66,12.38C7.16,13.34 6.16,14 5,14A3,3 0 0,1 2,11A3,3 0 0,1 5,8M19,8A3,3 0 0,1 22,11A3,3 0 0,1 19,14C17.84,14 16.84,13.34 16.34,12.38C17.2,11.27 17.62,9.85 17.47,8.42C17.92,8.15 18.44,8 19,8M5.5,18.25C5.5,16.18 8.41,14.5 12,14.5C15.59,14.5 18.5,16.18 18.5,18.25V20H5.5V18.25M0,20V18.5C0,17.11 1.89,15.94 4.45,15.6C3.86,16.28 3.5,17.22 3.5,18.25V20H0M24,20H20.5V18.25C20.5,17.22 20.14,16.28 19.55,15.6C22.11,15.94 24,17.11 24,18.5V20Z',
    'llave'      => 'M22.7,19L13.6,9.9C14.5,7.6 14,4.9 12.1,3C10.1,1 7.1,0.6 4.7,1.7L9,6L6,9L1.6,4.7C0.4,7.1 0.9,10.1 2.9,12.1C4.8,14 7.5,14.5 9.8,13.6L18.9,22.7C19.3,23.1 19.9,23.1 20.3,22.7L22.6,20.4C23.1,20 23.1,19.3 22.7,19Z',
    'persona'    => 'M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z',
    'curso'      => 'M12,3L1,9L12,15L21,10.09V17H23V9M5,13.18V17.18L12,21L19,17.18V13.18L12,17L5,13.18Z',
    'editor'     => 'M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M16.7,9.35L15.7,10.35L13.65,8.3L14.65,7.3C14.86,7.08 15.21,7.08 15.42,7.3L16.7,8.58C16.92,8.79 16.92,9.14 16.7,9.35M7,14.94L13.06,8.88L15.12,10.94L9.06,17H7V14.94Z',
    'publicar'   => 'M5,4V6H19V4H5M5,14H9V20H15V14H19L12,7L5,14Z',
    'grafico'    => 'M22,21H2V3H4V19H6V10H10V19H12V6H16V19H18V14H22V21Z',
    'documento'  => 'M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z',
    'carpeta'    => 'M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z',
    'ayuda'      => 'M15.07,11.25L14.17,12.17C13.45,12.89 13,13.5 13,15H11V14.5C11,13.39 11.45,12.39 12.17,11.67L13.41,10.41C13.78,10.05 14,9.55 14,9C14,7.89 13.1,7 12,7A2,2 0 0,0 10,9H8A4,4 0 0,1 12,5A4,4 0 0,1 16,9C16,9.88 15.64,10.67 15.07,11.25M13,19H11V17H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12C22,6.47 17.5,2 12,2Z',
    'historial'  => 'M13.5,8H12V13L16.28,15.54L17,14.33L13.5,12.25V8M13,3A9,9 0 0,0 4,12H1L4.96,16.03L9,12H6A7,7 0 0,1 13,5A7,7 0 0,1 20,12A7,7 0 0,1 13,19C11.07,19 9.32,18.21 8.06,16.94L6.64,18.36C8.27,20 10.5,21 13,21A9,9 0 0,0 22,12A9,9 0 0,0 13,3',
    'candado'    => 'M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z',
    'abrir'      => 'M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z',
    'arriba'     => 'M13,20H11V8L5.5,13.5L4.08,12.08L12,4.16L19.92,12.08L18.5,13.5L13,8V20Z',
    'idea'       => 'M12,2A7,7 0 0,0 5,9C5,11.38 6.19,13.47 8,14.74V17A1,1 0 0,0 9,18H15A1,1 0 0,0 16,17V14.74C17.81,13.47 19,11.38 19,9A7,7 0 0,0 12,2M9,21A1,1 0 0,0 10,22H14A1,1 0 0,0 15,21V20H9V21Z',
    'alerta'     => 'M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z',
    'cerrar'     => 'M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z',
    'flecha'     => 'M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z',
    'foto'       => 'M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z',
    'contacto'   => 'M6,17C6,15 10,13.9 12,13.9C14,13.9 18,15 18,17V18H6M15,9A3,3 0 0,1 12,12A3,3 0 0,1 9,9A3,3 0 0,1 12,6A3,3 0 0,1 15,9M3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3H5C3.89,3 3,3.9 3,5Z',
    'calendario' => 'M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z',
    'correo'     => 'M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z',
    'formulario' => 'M17,9H7V7H17M17,13H7V11H17M14,17H7V15H14M12,3A1,1 0 0,1 13,4A1,1 0 0,1 12,5A1,1 0 0,1 11,4A1,1 0 0,1 12,3M19,3H14.82C14.4,1.84 13.3,1 12,1C10.7,1 9.6,1.84 9.18,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z',
    'actividad'  => 'M3,13H5.79L10.1,4.79L11.28,13.75L14.5,9.66L17.83,13H21V15H17L14.67,12.67L9.92,18.73L8.94,11.31L7,15H3V13Z',
    'tablero'    => 'M3,3H9V21H3V3M10,3H16V14H10V3M17,3H21V9H17V3Z',
    'chat'       => 'M20,2H4A2,2 0 0,0 2,4V22L6,18H20A2,2 0 0,0 22,16V4A2,2 0 0,0 20,2Z',
    'lista'      => 'M3,5H9V11H3V5M5,7V9H7V7H5M11,7H21V9H11V7M11,15H21V17H11V15M5,20L1.5,16.5L2.91,15.09L5,17.17L9.59,12.59L11,14L5,20Z',
];

/** Dibuja un ícono propio como SVG en línea. */
function svg(string $nombre, string $clase = 'ico'): string
{
    $d = ICONOS_PROPIOS[$nombre] ?? ICONOS_PROPIOS['ayuda'];
    return '<svg class="' . h($clase) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="' . h($d) . '"/></svg>';
}

/**
 * Ícono de una sección: el SVG real de la aplicación si existe; si no, el
 * dibujo propio indicado como respaldo.
 */
function icono(string $url, string $respaldo, string $clase = 'ico'): string
{
    if ($url !== '') {
        return '<img class="' . h($clase) . ' ico-app" src="' . h($url) . '" alt="" loading="lazy" data-respaldo="' . h($respaldo) . '">'
             . '<span class="ico-respaldo" hidden>' . svg($respaldo, $clase) . '</span>';
    }
    return svg($respaldo, $clase);
}

/** Etiqueta que indica a quién está dirigida una función. */
function rol(string $rol): string
{
    if ($rol === 'admin') {
        return '<span class="rol rol-admin">' . svg('llave', 'ico-rol') . 'Administrador</span>';
    }
    if ($rol === 'user') {
        return '<span class="rol rol-user">' . svg('persona', 'ico-rol') . 'Usuario</span>';
    }
    return '';
}

/* =========================================================================
 * DETECCIÓN Y ARMADO DE SECCIONES
 * ========================================================================= */

$RAIZ = buscar_instalacion();
$A = detectar_apps($RAIZ, $CATALOGO, $APPS_MANUALES, $APPS_OCULTAS);

/** ¿Está instalada la aplicación? */
function app(string $id): bool
{
    global $A;
    return !empty($A[$id]['instalada']);
}

/** URL del ícono real de la aplicación ('' si no tiene). */
function icono_app(string $id): string
{
    global $A;
    return $A[$id]['icono'] ?? '';
}

/** Versión principal detectada (0 si no se sabe). */
function version_app(string $id): int
{
    global $A;
    return (int) ($A[$id]['version'] ?? 0);
}

/** Ícono de un módulo propio de InSSA. */
function icono_modulo(string $clave): string
{
    global $MODULOS, $RAIZ;
    $ruta = (string) ($MODULOS[$clave]['icono'] ?? '');
    if ($ruta === '' || !preg_match('#^/[\w./-]+\.svg$#', $ruta) || str_contains($ruta, '..')) {
        return '';
    }
    if ($RAIZ !== null && !@is_file($RAIZ . $ruta)) {
        return '';
    }
    return NUBE_URL . $ruta;
}

/** Enlace de un módulo propio (sólo rutas internas de Nube InSSA). */
function url_modulo(string $clave): string
{
    global $MODULOS;
    $ruta = (string) ($MODULOS[$clave]['url'] ?? '');
    if ($ruta === '' || !preg_match('#^/[\w./?=&%-]*$#', $ruta)) {
        return '';
    }
    return NUBE_URL . $ruta;
}

function modulo_activo(string $clave): bool
{
    global $MODULOS;
    return !empty($MODULOS[$clave]['activo']);
}

// Registro de secciones, en el orden en que aparecen.
// 'icono'   : URL real del SVG ('' si no hay)
// 'respaldo': dibujo propio si no hay SVG
// 'rol'     : 'todos', 'user' o 'admin'
// 'abrir'   : dirección del botón "Abrir" ('' = sin botón)
$SECCIONES = [];

function registrar(string $id, string $titulo, string $resumen, string $icono, string $respaldo, string $rol = 'todos', string $abrir = '', string $claves = ''): void
{
    global $SECCIONES;
    $SECCIONES[$id] = compact('id', 'titulo', 'resumen', 'icono', 'respaldo', 'rol', 'abrir', 'claves');
}

function ruta_app(string $id): string
{
    global $CATALOGO;
    $r = $CATALOGO[$id]['ruta'] ?? '';
    return ($r !== '' && app($id)) ? NUBE_URL . $r : '';
}

registrar('primeros-pasos', 'Primeros pasos', 'Cómo moverte por Nube InSSA la primera vez.', '', 'pasos', 'todos', '', 'inicio ingresar sesion menu barra superior avatar perfil salir cerrar sesion');
registrar('archivos', 'Archivos', 'Guardá, ordená y encontrá todos tus archivos.', icono_app('files'), 'carpeta', 'todos', ruta_app('files'), 'subir descargar carpeta renombrar mover copiar favoritos etiquetas comentarios versiones vista previa ordenar seleccionar recientes almacenamiento cuota espacio');
registrar('compartir', 'Compartir información', 'Dale acceso a otras personas, grupos o por enlace.', icono_app('files_sharing'), 'compartir', 'todos', '', 'enlace publico permisos lectura edicion descarga grupos contraseña vencimiento dejar de compartir');
if (app('onlyoffice')) {
    registrar('documentos', 'Documentos, planillas y presentaciones', 'Creá y editá documentos en el navegador, en equipo y al mismo tiempo.', icono_app('onlyoffice'), 'documento', 'todos', '', 'onlyoffice word excel powerpoint texto planilla hoja de calculo presentacion edicion colaborativa simultanea comentarios control de cambios');
}
if (app('photos')) {
    registrar('fotos', 'Fotos', 'Mirá, ordená y compartí tus fotos y videos.', icono_app('photos'), 'foto', 'todos', ruta_app('photos'), 'imagenes videos albumes galeria');
}
if (app('contacts')) {
    registrar('contactos', 'Contactos', 'Tu agenda de personas, siempre a mano.', icono_app('contacts'), 'contacto', 'todos', ruta_app('contacts'), 'agenda libreta direcciones telefono vcard importar');
}
if (app('calendar')) {
    registrar('calendario', 'Calendario', 'Organizá eventos, reuniones y recordatorios.', icono_app('calendar'), 'calendario', 'todos', ruta_app('calendar'), 'eventos reuniones invitaciones participantes recordatorios repetir recurrente agenda');
}
if (app('mail')) {
    registrar('correo', 'Correo', 'Leé y enviá correos sin salir de Nube InSSA.', icono_app('mail'), 'correo', 'todos', ruta_app('mail'), 'mail email bandeja entrada enviados borradores adjuntos responder reenviar redactar');
}
if (app('forms')) {
    registrar('formularios', 'Formularios', 'Armá encuestas e inscripciones y mirá las respuestas.', icono_app('forms'), 'formulario', 'todos', ruta_app('forms'), 'encuesta preguntas respuestas inscripcion resultados exportar csv');
}
if (app('deck')) {
    registrar('tableros', 'Tableros', 'Organizá tareas en tarjetas y columnas.', icono_app('deck'), 'tablero', 'todos', ruta_app('deck'), 'deck tarjetas listas kanban tareas proyectos');
}
if (app('spreed')) {
    registrar('conversaciones', 'Conversaciones y videollamadas', 'Chateá y hacé llamadas con otras personas de la institución.', icono_app('spreed'), 'chat', 'todos', ruta_app('spreed'), 'chat mensajes llamada video reunion');
}
if (app('tasks') || app('notes') || app('polls') || app('bookmarks')) {
    registrar('mas-apps', 'Otras aplicaciones', 'Herramientas extra disponibles en Nube InSSA.', '', 'lista', 'todos', '', 'tareas notas encuestas marcadores');
}
registrar('actividad', 'Actividad', 'Todo lo que pasó con tus archivos y tu cuenta.', icono_app('activity'), 'actividad', 'todos', ruta_app('activity'), 'historial cambios reciente modificados compartidos comentarios');
registrar('notificaciones', 'Notificaciones', 'Avisos sobre lo que te interesa.', icono_app('notifications'), 'campana', 'todos', '', 'avisos campana menciones alertas correo');
registrar('busqueda', 'Búsqueda', 'Encontrá cualquier cosa desde un solo lugar.', '', 'buscar', 'todos', '', 'buscar encontrar filtros lupa');
registrar('papelera', 'Papelera y recuperación', 'Recuperá lo que borraste por error.', icono_app('files_trashbin'), 'papelera', 'todos', '', 'eliminar borrar restaurar recuperar eliminados definitivo');
if (modulo_activo('cursos')) {
    registrar('cursos', 'Cursos y contenidos educativos', 'Materiales y recursos para la formación.', icono_modulo('cursos'), 'curso', 'todos', url_modulo('cursos'), 'curso clase materiales educacion alumnos docentes capacitacion');
}
if (modulo_activo('editor')) {
    registrar('editor', 'Editor de sitio', 'Mantené actualizada la información institucional.', icono_modulo('editor'), 'editor', 'admin', url_modulo('editor'), 'sitio web enlaces novedades noticias editar contenido institucional');
}
if (modulo_activo('publicacion')) {
    registrar('publicacion', 'Carga y publicación de contenidos', 'La diferencia entre guardar y publicar.', icono_modulo('publicacion'), 'publicar', 'todos', url_modulo('publicacion'), 'publicar libros articulos publicaciones novedades recursos subir cargar');
}
if (modulo_activo('estadisticas') || app('serverinfo') || app('analytics')) {
    registrar('estadisticas', 'Estadísticas', 'Datos sobre el uso de Nube InSSA.', icono_modulo('estadisticas') ?: icono_app('analytics'), 'grafico', 'todos', url_modulo('estadisticas'), 'estadisticas descargas accesos uso indicadores informes');
}
registrar('usuarios', 'Usuarios y grupos', 'Quién es quién dentro de Nube InSSA.', '', 'grupo', 'todos', '', 'usuarios grupos cuentas miembros pertenencia');
registrar('configuracion', 'Configuración', 'Tus datos, tu idioma y tus preferencias.', icono_app('settings'), 'engranaje', 'user', ruta_app('settings'), 'ajustes preferencias perfil foto idioma zona horaria contraseña');
registrar('seguridad', 'Privacidad y seguridad', 'Cómo se cuida tu información y qué podés hacer vos.', '', 'escudo', 'todos', '', 'privacidad seguridad contraseña sesiones dispositivos cifrado dos pasos autenticacion');
registrar('moviles', 'Celulares y tablets', 'Usá Nube InSSA desde cualquier dispositivo.', '', 'movil', 'user', '', 'celular telefono tablet movil sincronizar');
registrar('administracion', 'Administración', 'Herramientas para quienes administran Nube InSSA.', '', 'llave', 'admin', NUBE_URL . '/index.php/settings/admin', 'administrador panel usuarios grupos aplicaciones politicas mantenimiento sistema');

/* =========================================================================
 * AYUDANTES DE MAQUETADO
 * ========================================================================= */

function seccion_abrir(string $id): void
{
    global $SECCIONES;
    $s = $SECCIONES[$id];
    echo '<section class="seccion" id="' . h($id) . '" data-rol="' . h($s['rol']) . '" data-claves="' . h($s['claves']) . '">';
    echo '<header class="seccion-cabecera">';
    echo '<span class="chip chip-grande">' . icono($s['icono'], $s['respaldo']) . '</span>';
    echo '<div class="seccion-titulos"><h2>' . h($s['titulo']) . '</h2><p class="seccion-resumen">' . h($s['resumen']) . '</p>';
    if ($s['rol'] !== 'todos') {
        echo rol($s['rol']);
    }
    echo '</div>';
    if ($s['abrir'] !== '') {
        echo '<a class="boton" href="' . h($s['abrir']) . '" target="_top">' . svg('abrir') . 'Abrir</a>';
    }
    echo '</header>';
}

function seccion_cerrar(): void
{
    echo '<a class="volver" href="#inicio">' . svg('arriba') . 'Volver arriba</a></section>';
}

function acc(string $titulo, string $rol = 'todos', bool $abierto = false): void
{
    echo '<details class="acc" data-rol="' . h($rol) . '"' . ($abierto ? ' open data-abierto="1"' : '') . '>';
    echo '<summary><span class="acc-titulo">' . h($titulo) . '</span>' . ($rol !== 'todos' ? rol($rol) : '') . svg('flecha', 'ico acc-flecha') . '</summary><div class="acc-cuerpo">';
}

function acc_fin(): void
{
    echo '</div></details>';
}

function tip(string $html, string $tipo = 'idea'): void
{
    $iconos = ['idea' => 'idea', 'alerta' => 'alerta', 'info' => 'ayuda'];
    echo '<div class="nota nota-' . h($tipo) . '">' . svg($iconos[$tipo] ?? 'idea') . '<div>' . $html . '</div></div>';
}

/* =========================================================================
 * CABECERAS DE SEGURIDAD
 * ========================================================================= */

$NONCE = base64_encode(random_bytes(16));
$HOST_NUBE = h(NUBE_URL);

if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header("Content-Security-Policy: default-src 'none'; img-src 'self' " . NUBE_URL . " data:; style-src 'nonce-{$NONCE}'; script-src 'nonce-{$NONCE}'; frame-ancestors 'self' " . NUBE_URL . "; base-uri 'none'; form-action 'none'");
}

$ANIO = date('Y');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Ayuda oficial de Nube InSSA: qué podés hacer y cómo hacerlo.">
<meta name="robots" content="noindex">
<title>Ayuda de Nube InSSA</title>
<style nonce="<?= h($NONCE) ?>">
:root {
  --color-primary:      #FF7A2C;
  --color-primary-osc:  #E0621A;
  --color-nav-bg:       #FFE4D5;
  --color-main-bg:      #ffffff;
  --color-soft-bg:      #FFF7F2;
  --color-text:         #222222;
  --color-text-muted:   #6b6b6b;
  --color-hover:        #F5F5F5;
  --color-border:       #ededed;
  --color-globo-fondo:  #FFF1E9;
  --color-globo-texto:  #590900;
  --color-admin:        #3E4C7A;
  --color-admin-bg:     #E9ECF7;
  --color-user:         #1F6F50;
  --color-user-bg:      #E3F4EC;
  --color-alerta:       #9A3412;
  --color-alerta-bg:    #FFF4E5;

  --radius:             8px;
  --radius-seleccion:   8px;
  --radius-tarjeta:     14px;
  --sidebar-width:      300px;
  --nav-ancho-movil:    min(300px, 80vw);
  --topbar-alto:        44px;
}

*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
html, body { height:100%; }
body {
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
  font-size: 14px;
  color: var(--color-text);
  background: var(--color-main-bg);
  height: 100vh;
  overflow: hidden;
}
[hidden] { display:none !important; }

/* ---------- ESTRUCTURA ---------- */

#app { display:flex; height:100vh; overflow:hidden; }

#app-navigation {
  width: var(--sidebar-width);
  min-width: var(--sidebar-width);
  background: var(--color-nav-bg);
  height: 100vh;
  transition: width .2s ease, min-width .2s ease;
  flex-shrink: 0;
  z-index: 200;
  display: flex;
  flex-direction: column;
}
#app.sidebar-collapsed #app-navigation { width:0; min-width:0; overflow:hidden; }

.app-navigation__body { flex:1; overflow-y:auto; min-height:0; padding-bottom:6px; }
.app-navigation__pie  { flex-shrink:0; padding:6px 0; border-top:1px solid rgba(0,0,0,.07); }

#app-content {
  flex:1; display:flex; flex-direction:column;
  background:var(--color-main-bg); overflow:hidden; min-width:0;
}

#app-topbar {
  height: var(--topbar-alto);
  border-bottom: 1px solid var(--color-border);
  display: flex; align-items: center; gap: 10px;
  padding: 0 14px; flex-shrink: 0;
  background: var(--color-main-bg);
  position: relative;
  z-index: 210;
}
#app-topbar h1 { font-size: 15px; font-weight: 600; flex: 1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.topbar-user { font-size: 12px; color: var(--color-text-muted); display:flex; align-items:center; gap:6px; text-decoration:none; padding:6px 10px; border-radius:var(--radius); white-space:nowrap; }
.topbar-user:hover { background: var(--color-hover); color: var(--color-text); }
.topbar-user .ico { width:16px; height:16px; }

#app-content-inner { flex:1; overflow-y:auto; padding: 22px 26px 60px; scroll-behavior:smooth; }
@media (prefers-reduced-motion: reduce) { #app-content-inner { scroll-behavior:auto; } }

/* ---------- BUSCADOR DEL MENÚ ---------- */

.nav-buscador { position:relative; padding:10px 10px 8px; flex-shrink:0; }
.nav-buscador .ico {
  position:absolute; left:20px; top:50%; transform:translateY(-4px);
  width:14px; height:14px; color:var(--color-text-muted); pointer-events:none;
}
.nav-buscador input {
  width:100%; padding:8px 12px 8px 32px;
  border:1px solid rgba(0,0,0,.12); border-radius:var(--radius);
  background:#fff; font-size:13px; color:var(--color-text); font-family:inherit;
}
.nav-buscador input:focus { outline:none; border-color:var(--color-primary); }

/* ---------- FILAS DEL MENÚ ---------- */

ul { list-style:none; }
.nav-grupo {
  font-size:11px; font-weight:600; letter-spacing:.04em; text-transform:uppercase;
  color:var(--color-text-muted); padding:12px 18px 4px;
}

.list-item__wrapper { padding: 0 8px; }

.list-item__anchor {
  display: flex;
  align-items: center;
  gap: 0;
  min-height: 34px;
  padding: 0 9px 0 0;
  text-decoration: none;
  color: var(--color-text);
  border-radius: var(--radius-seleccion);
  position: relative;
  font-size: 15px;
  font-weight: 400;
}
.list-item__anchor:hover { background: var(--color-hover); }
.list-item__anchor:focus-visible { outline:2px solid var(--color-primary); outline-offset:-2px; }
.list-item__wrapper.active .list-item__anchor { background: var(--color-primary); color: #fff; }

.nav-icon {
  width:34px; height:34px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center;
}
.nav-icon .ico { width:18px; height:18px; color:var(--color-text); }
/* Los íconos de las aplicaciones vienen pensados para la barra oscura
   (dibujo blanco). En el menú claro se muestran en gris oscuro y, en el
   ítem elegido, en blanco. */
.nav-icon img.ico { filter: brightness(0); opacity:.78; }
.list-item__wrapper.active .nav-icon .ico { color:#fff; }
.list-item__wrapper.active .nav-icon img.ico { filter: brightness(0) invert(1); opacity:1; }

.list-item-content__name { flex:1; font-weight:400; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.counter-bubble__counter {
  min-width: 22px; height: 22px; padding: 0 6px;
  border-radius: 11px; font-size: 11px; font-weight: 700;
  display: inline-flex; align-items: center; justify-content: center;
  background: var(--color-globo-fondo); color: var(--color-globo-texto);
}
.list-item__wrapper.active .counter-bubble__counter {
  background: var(--color-globo-fondo); color: var(--color-globo-texto);
}

/* ---------- BOTÓN DEL MENÚ ---------- */

.app-navigation-toggle {
  width: 36px; height: 36px; border: none; background: transparent;
  border-radius: var(--radius); cursor: pointer;
  color: var(--color-text);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.app-navigation-toggle:hover { background: var(--color-hover); }
.app-navigation-toggle .icono-cerrado { display: none; }
.app-navigation-toggle.cerrado .icono-abierto { display: none; }
.app-navigation-toggle.cerrado .icono-cerrado { display: block; }

/* ---------- ÍCONOS ---------- */

.ico { width:20px; height:20px; flex-shrink:0; display:block; }
.chip {
  width:40px; height:40px; border-radius:12px; flex-shrink:0;
  background: var(--color-primary); color:#fff;
  display:flex; align-items:center; justify-content:center;
}
.chip .ico { width:22px; height:22px; color:#fff; }
/* En la ficha naranja, los íconos de las aplicaciones van en blanco. */
.chip img.ico { filter: brightness(0) invert(1); }
.chip-grande { width:52px; height:52px; border-radius:14px; }
.chip-grande .ico { width:28px; height:28px; }

/* ---------- PORTADA ---------- */

.portada {
  background: linear-gradient(135deg, #FF7A2C 0%, #FF9A5A 60%, #FFB27F 100%);
  color:#fff; border-radius: 18px; padding: 34px 32px 30px;
  margin-bottom: 26px; position:relative; overflow:hidden;
}
.portada::after {
  content:""; position:absolute; right:-60px; top:-60px; width:240px; height:240px;
  border-radius:50%; background: rgba(255,255,255,.12);
}
.portada h2 { font-size: 28px; font-weight:700; line-height:1.2; margin-bottom:8px; position:relative; }
.portada p { font-size: 16px; max-width: 620px; opacity:.95; position:relative; line-height:1.5; }
.portada-buscador { position:relative; margin-top:20px; max-width:620px; }
.portada-buscador .ico { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--color-text-muted); width:20px; height:20px; }
.portada-buscador input {
  width:100%; height:50px; border:none; border-radius:12px; padding:0 48px 0 48px;
  font-size:16px; font-family:inherit; color:var(--color-text);
  box-shadow: 0 6px 20px rgba(89,9,0,.18);
}
.portada-buscador input:focus { outline: 3px solid rgba(255,255,255,.7); }
input[type="search"]::-webkit-search-cancel-button { -webkit-appearance:none; appearance:none; }
.limpiar {
  position:absolute; right:8px; top:50%; transform:translateY(-50%);
  width:34px; height:34px; border:none; border-radius:8px; background:transparent;
  color:var(--color-text-muted); cursor:pointer; display:flex; align-items:center; justify-content:center;
}
.limpiar:hover { background: var(--color-hover); }

.filtro-rol { display:flex; flex-wrap:wrap; gap:8px; margin-top:16px; position:relative; align-items:center; }
.filtro-rol span { font-size:13px; opacity:.95; margin-right:4px; }
.filtro-rol button {
  border:1px solid rgba(255,255,255,.7); background:transparent; color:#fff;
  padding:6px 14px; border-radius:999px; font-size:13px; cursor:pointer; font-family:inherit;
}
.filtro-rol button[aria-pressed="true"] { background:#fff; color:var(--color-globo-texto); border-color:#fff; font-weight:600; }

.resultado-busqueda {
  display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px;
  background: var(--color-soft-bg); border:1px solid #FFD9C2; margin-bottom:20px; font-size:14px;
}
.sin-resultados { text-align:center; padding:40px 20px; color:var(--color-text-muted); }
.sin-resultados .ico { width:48px; height:48px; margin:0 auto 10px; color:#FFB27F; }
.sin-resultados strong { display:block; color:var(--color-text); font-size:16px; margin-bottom:6px; }

/* ---------- ACCESOS RÁPIDOS ---------- */

.titulo-bloque { font-size:18px; font-weight:600; margin: 4px 0 14px; }
.accesos {
  display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap:14px; margin-bottom: 34px;
}
.acceso {
  display:flex; gap:12px; align-items:flex-start; padding:16px;
  border:1px solid var(--color-border); border-radius: var(--radius-tarjeta);
  text-decoration:none; color:var(--color-text); background:#fff;
  transition: box-shadow .15s ease, transform .15s ease, border-color .15s ease;
}
.acceso:hover { box-shadow: 0 8px 22px rgba(0,0,0,.08); transform: translateY(-2px); border-color:#FFD2B8; }
.acceso:focus-visible { outline:2px solid var(--color-primary); outline-offset:2px; }
.acceso strong { display:block; font-size:15px; margin-bottom:3px; }
.acceso small { font-size:13px; color:var(--color-text-muted); line-height:1.4; display:block; }

/* ---------- SECCIONES ---------- */

.seccion {
  scroll-margin-top: 12px;
  border:1px solid var(--color-border); border-radius: 18px;
  padding: 24px; margin-bottom: 26px; background:#fff;
}
.seccion-cabecera { display:flex; align-items:flex-start; gap:16px; margin-bottom:18px; flex-wrap:wrap; }
.seccion-titulos { flex:1; min-width:200px; }
.seccion-titulos h2 { font-size:22px; font-weight:700; line-height:1.25; }
.seccion-resumen { color:var(--color-text-muted); font-size:15px; margin-top:3px; margin-bottom:6px; }

.boton {
  display:inline-flex; align-items:center; gap:8px; padding:9px 16px; border-radius:10px;
  background: var(--color-primary); color:#fff; text-decoration:none; font-weight:600; font-size:14px;
  white-space:nowrap;
}
.boton .ico { width:16px; height:16px; }
.boton:hover { background: var(--color-primary-osc); }
.boton:focus-visible { outline:2px solid var(--color-globo-texto); outline-offset:2px; }

.intro { font-size:15px; line-height:1.6; margin-bottom:16px; max-width: 820px; }

.tarjetas { display:grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap:12px; margin: 4px 0 18px; }
.tarjeta { background: var(--color-soft-bg); border-radius: 12px; padding:14px 16px; }
.tarjeta h3 { font-size:14px; font-weight:600; margin-bottom:4px; display:flex; align-items:center; gap:8px; }
.tarjeta h3 .ico { width:18px; height:18px; color: var(--color-primary); }
.tarjeta p { font-size:13.5px; line-height:1.5; color:#444; }

/* Acordeones */
.acc { border:1px solid var(--color-border); border-radius:12px; margin-bottom:10px; background:#fff; }
.acc > summary {
  list-style:none; cursor:pointer; display:flex; align-items:center; gap:10px;
  padding:13px 16px; font-size:15px; font-weight:600; border-radius:12px;
}
.acc > summary::-webkit-details-marker { display:none; }
.acc > summary:hover { background: var(--color-hover); }
.acc > summary:focus-visible { outline:2px solid var(--color-primary); outline-offset:-2px; }
.acc-titulo { flex:1; }
.acc-flecha { width:20px; height:20px; color:var(--color-text-muted); transition: transform .2s ease; }
.acc[open] > summary .acc-flecha { transform: rotate(180deg); }
.acc[open] > summary { border-bottom:1px solid var(--color-border); border-radius:12px 12px 0 0; }
.acc-cuerpo { padding: 14px 18px 16px; font-size:14.5px; line-height:1.65; }
.acc-cuerpo p { margin-bottom:10px; }
.acc-cuerpo p:last-child { margin-bottom:0; }
.acc-cuerpo ol, .acc-cuerpo ul { margin: 0 0 10px 22px; }
.acc-cuerpo ul { list-style: disc; }
.acc-cuerpo li { margin-bottom:5px; }
.acc-cuerpo li::marker { color: var(--color-primary); }
.acc-cuerpo h4 { font-size:14.5px; margin: 12px 0 6px; }
.acc-cuerpo code { background: var(--color-hover); padding:1px 6px; border-radius:5px; font-size:13px; word-break:break-all; }

.ejemplo {
  border-left: 3px solid var(--color-primary); background: var(--color-soft-bg);
  padding:10px 14px; border-radius: 0 10px 10px 0; margin: 10px 0; font-size:14px;
}
.ejemplo b { color: var(--color-globo-texto); }

.nota { display:flex; gap:10px; padding:12px 14px; border-radius:12px; margin: 12px 0; font-size:14px; line-height:1.55; }
.nota .ico { width:20px; height:20px; margin-top:1px; }
.nota-idea   { background:#FFF7E0; color:#5C4300; }
.nota-alerta { background: var(--color-alerta-bg); color: var(--color-alerta); border:1px solid #FDD8AE; }
.nota-info   { background:#EEF3FB; color:#27406B; }

/* Etiquetas de rol */
.rol {
  display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600;
  padding:3px 9px; border-radius:999px; white-space:nowrap;
}
.ico-rol { width:13px; height:13px; }
.rol-admin { background: var(--color-admin-bg); color: var(--color-admin); }
.rol-user  { background: var(--color-user-bg);  color: var(--color-user); }

.dos-columnas { display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px; }
.columna { border-radius:12px; padding:14px 16px; }
.columna h3 { font-size:15px; margin-bottom:8px; display:flex; align-items:center; gap:8px; }
.columna ul { margin-left:20px; font-size:14px; line-height:1.6; list-style:disc; }
.columna-user  { background: var(--color-user-bg); }
.columna-admin { background: var(--color-admin-bg); }

.volver {
  display:inline-flex; align-items:center; gap:6px; margin-top:14px; font-size:13px;
  color: var(--color-text-muted); text-decoration:none; padding:5px 8px; border-radius:8px;
}
.volver .ico { width:15px; height:15px; }
.volver:hover { color: var(--color-text); background: var(--color-hover); }

mark { background: #FFE08A; color: inherit; border-radius:3px; padding:0 1px; }

.subir {
  position: fixed; right: 22px; bottom: 22px; z-index: 150;
  width:46px; height:46px; border-radius:50%; border:none; cursor:pointer;
  background: var(--color-primary); color:#fff; box-shadow: 0 6px 18px rgba(0,0,0,.2);
  display:flex; align-items:center; justify-content:center;
  opacity:0; pointer-events:none; transition: opacity .2s ease;
}
.subir.visible { opacity:1; pointer-events:auto; }
.subir .ico { width:22px; height:22px; }

.pie-ayuda { text-align:center; font-size:12.5px; color: var(--color-text-muted); padding: 10px 0 20px; }

/* Filtro por perfil */
body[data-vista="user"]  [data-rol="admin"] { display:none !important; }
body[data-vista="admin"] [data-rol="user"]  { display:none !important; }

/* ---------- PANTALLA CHICA ---------- */

@media (max-width: 1024px) {
  #app-navigation {
    position: fixed; left:0; top:0; height:100vh;
    width: var(--nav-ancho-movil) !important; min-width: 0 !important;
    transform: translateX(-100%);
    transition: transform .2s ease;
    box-shadow: 2px 0 10px rgba(0,0,0,.12);
    background: rgba(255, 255, 255, .85);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    z-index: 220;
  }
  #app-navigation.mobile-open { transform: translateX(0); }

  @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
    #app-navigation { background: rgba(255, 255, 255, .97); }
  }

  .app-navigation-toggle { transition: transform .2s ease; }
  #app.nav-abierto .app-navigation-toggle { transform: translateX(var(--nav-ancho-movil)); }

  #app-content-inner { padding: 16px 16px 60px; }
  .dos-columnas { grid-template-columns: 1fr; }
}

@media (max-width: 600px) {
  .portada { padding: 24px 18px 22px; border-radius:14px; }
  .portada h2 { font-size: 22px; }
  .portada p { font-size: 15px; }
  .seccion { padding: 18px 14px; border-radius:14px; }
  .seccion-titulos h2 { font-size: 19px; }
  .chip-grande { width:44px; height:44px; }
  .accesos { grid-template-columns: 1fr; }
  .acc-cuerpo { padding: 12px 14px 14px; }
  .topbar-user .texto { display:none; }
  .subir { right:14px; bottom:14px; }
}

@media print {
  body { height:auto; overflow:visible; }
  #app, #app-content, #app-content-inner { display:block; height:auto; overflow:visible; }
  #app-navigation, #app-topbar, .subir, .volver, .portada-buscador, .filtro-rol { display:none !important; }
  .acc-cuerpo { display:block !important; }
}
</style>
</head>
<body data-vista="todos">
<div id="app">

  <nav id="app-navigation" aria-label="Índice de la ayuda">

    <div class="nav-buscador" role="search">
      <?= svg('buscar') ?>
      <input type="search" id="buscarNav" placeholder="Buscar en la ayuda…" aria-label="Buscar en la ayuda" autocomplete="off">
    </div>

    <div class="app-navigation__body">
      <ul>
        <li class="list-item__wrapper active" data-destino="inicio">
          <a href="#inicio" class="list-item__anchor">
            <span class="nav-icon"><?= svg('inicio') ?></span>
            <span class="list-item-content__name">Inicio</span>
          </a>
        </li>
<?php foreach ($SECCIONES as $s): ?>
        <li class="list-item__wrapper" data-destino="<?= h($s['id']) ?>" data-rol="<?= h($s['rol']) ?>">
          <a href="#<?= h($s['id']) ?>" class="list-item__anchor">
            <span class="nav-icon"><?= icono($s['icono'], $s['respaldo']) ?></span>
            <span class="list-item-content__name"><?= h($s['titulo']) ?></span>
            <span class="counter-bubble__counter" hidden></span>
          </a>
        </li>
<?php endforeach; ?>
      </ul>
    </div>

    <div class="app-navigation__pie">
      <ul>
        <li class="list-item__wrapper">
          <a href="<?= $HOST_NUBE ?>" class="list-item__anchor" target="_top">
            <span class="nav-icon"><?= svg('abrir') ?></span>
            <span class="list-item-content__name">Ir a Nube InSSA</span>
          </a>
        </li>
      </ul>
    </div>

  </nav>

  <div id="app-content">
    <div id="app-topbar">
      <button class="app-navigation-toggle" id="navToggle" type="button" aria-label="Mostrar u ocultar el menú" aria-controls="app-navigation">
        <svg class="icono-abierto" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
          <path fill="currentColor" d="M21,15.61L19.59,17L14.58,12L19.59,7L21,8.39L17.44,12L21,15.61M3,6H16V8H3V6M3,13V11H13V13H3M3,18V16H16V18H3Z"/>
        </svg>
        <svg class="icono-cerrado" viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
          <path fill="currentColor" d="M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z"/>
        </svg>
      </button>
      <h1>Ayuda de Nube InSSA</h1>
      <a class="topbar-user" href="<?= $HOST_NUBE ?>" target="_top"><?= svg('inicio') ?><span class="texto">Volver a Nube InSSA</span></a>
    </div>

    <main id="app-content-inner">

      <div id="inicio" class="portada">
        <h2>¿En qué te ayudamos?</h2>
        <p>Esta es la guía de Nube InSSA. Acá encontrás todo lo que podés hacer: guardar y compartir archivos, trabajar en documentos con tu equipo, organizar tu agenda y mucho más.</p>
        <div class="portada-buscador" role="search">
          <?= svg('buscar') ?>
          <input type="search" id="buscar" placeholder="Por ejemplo: compartir una carpeta, recuperar un archivo…" aria-label="Buscar en la ayuda" autocomplete="off">
          <button type="button" class="limpiar" id="limpiar" aria-label="Borrar la búsqueda" hidden><?= svg('cerrar') ?></button>
        </div>
        <div class="filtro-rol" role="group" aria-label="Mostrar funciones para">
          <span>Mostrar funciones para:</span>
          <button type="button" data-vista="todos" aria-pressed="true">Todos</button>
          <button type="button" data-vista="user" aria-pressed="false">Usuarios</button>
          <button type="button" data-vista="admin" aria-pressed="false">Administradores</button>
        </div>
      </div>

      <div id="resultado" class="resultado-busqueda" hidden role="status" aria-live="polite"></div>
      <div id="sinResultados" class="sin-resultados" hidden>
        <?= svg('buscar') ?>
        <strong>No encontramos nada con esas palabras</strong>
        Probá con otras, por ejemplo "subir", "enlace" o "contraseña".
      </div>

      <div id="bloqueAccesos">
        <h2 class="titulo-bloque">¿Qué querés hacer?</h2>
        <div class="accesos">
<?php foreach ($SECCIONES as $s): ?>
          <a class="acceso" href="#<?= h($s['id']) ?>" data-rol="<?= h($s['rol']) ?>">
            <span class="chip"><?= icono($s['icono'], $s['respaldo']) ?></span>
            <span><strong><?= h($s['titulo']) ?></strong><small><?= h($s['resumen']) ?></small></span>
          </a>
<?php endforeach; ?>
        </div>
      </div>

<!-- ================================================================== -->
<!-- PRIMEROS PASOS                                                     -->
<!-- ================================================================== -->
<?php seccion_abrir('primeros-pasos'); ?>
      <p class="intro">Nube InSSA es tu espacio de trabajo en línea. Todo lo que guardás acá está disponible desde cualquier computadora, celular o tablet con conexión a internet. Solamente necesitás tu usuario y tu contraseña.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('lista') ?>Barra superior</h3><p>Arriba de todo están los íconos de las aplicaciones. Tocá uno para abrirla.</p></div>
        <div class="tarjeta"><h3><?= svg('persona') ?>Tu foto o tus iniciales</h3><p>En la esquina superior derecha. Desde ahí entrás a tu configuración y cerrás la sesión.</p></div>
        <div class="tarjeta"><h3><?= svg('buscar') ?>La lupa</h3><p>Busca en toda Nube InSSA a la vez: archivos, contactos, eventos y más.</p></div>
        <div class="tarjeta"><h3><?= svg('campana') ?>La campana</h3><p>Te avisa cuando alguien te comparte algo, te menciona o hay novedades.</p></div>
      </div>
<?php acc('Cómo ingresar', 'todos', true); ?>
        <ol>
          <li>Abrí tu navegador y entrá a <code><?= $HOST_NUBE ?></code>.</li>
          <li>Escribí tu usuario y tu contraseña.</li>
          <li>Tocá el botón para iniciar sesión.</li>
        </ol>
        <p>Si te olvidaste la contraseña, usá la opción para restablecerla que aparece en la pantalla de ingreso, si está disponible. Si no aparece, pedile ayuda a la administración de Nube InSSA.</p>
<?php acc_fin(); ?>
<?php acc('Cómo moverte entre aplicaciones'); ?>
        <p>Cada ícono de la barra superior es una aplicación: Archivos<?= app('calendar') ? ', Calendario' : '' ?><?= app('contacts') ? ', Contactos' : '' ?><?= app('mail') ? ', Correo' : '' ?> y las demás que tenga habilitadas tu cuenta. Al pasar el mouse por encima vas a ver su nombre.</p>
        <p>Dentro de cada aplicación, a la izquierda, hay un menú con sus secciones. En el celular ese menú se esconde. Lo abrís con el botón de tres rayas que está arriba a la izquierda.</p>
<?php acc_fin(); ?>
<?php if (app('dashboard')): ?>
<?php acc('El panel de inicio'); ?>
        <p>El panel es la primera pantalla que ves al entrar. Reúne en un solo lugar un resumen de lo más importante: tus próximos eventos, archivos recientes, novedades y otros elementos, según lo que tengas activado.</p>
        <p>Podés elegir qué se muestra con el botón para personalizarlo, que está al pie del panel.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php acc('Cómo cerrar la sesión'); ?>
        <p>Tocá tu foto o tus iniciales, arriba a la derecha, y elegí la opción para cerrar sesión.</p>
        <?php tip('Si usaste una computadora compartida, siempre cerrá la sesión al terminar. Cerrar la pestaña del navegador no alcanza.', 'alerta'); ?>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- ARCHIVOS                                                           -->
<!-- ================================================================== -->
<?php seccion_abrir('archivos'); ?>
      <p class="intro">Archivos es el corazón de Nube InSSA. Es como tener una carpeta personal que te sigue a todos lados. Desde acá podés administrar tus archivos, organizarlos en carpetas y compartirlos.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('publicar') ?>Subir</h3><p>Arrastrá archivos desde tu computadora o usá el botón <b>+ Nuevo</b>.</p></div>
        <div class="tarjeta"><h3><?= svg('carpeta') ?>Organizar</h3><p>Carpetas, favoritos y etiquetas para encontrar todo rápido.</p></div>
        <div class="tarjeta"><h3><?= svg('compartir') ?>Compartir</h3><p>Con una persona, con un grupo o con un enlace.</p></div>
        <div class="tarjeta"><h3><?= svg('historial') ?>Recuperar</h3><p>Versiones anteriores y papelera por si algo sale mal.</p></div>
      </div>

<?php acc('Subir archivos', 'todos', true); ?>
        <p>Tenés dos formas de hacerlo:</p>
        <ul>
          <li><b>Arrastrando.</b> Seleccioná los archivos en tu computadora y soltalos sobre la lista de Archivos. Se suben a la carpeta que tengas abierta.</li>
          <li><b>Con el botón + Nuevo.</b> Tocalo, elegí la opción para subir archivos y buscalos en tu equipo.</li>
        </ul>
        <p>Mientras se suben vas a ver una barra de progreso. No cierres la pestaña hasta que termine.</p>
        <div class="ejemplo"><b>Ejemplo:</b> tenés las fotos de una jornada en tu escritorio. Abrí la carpeta "Jornadas" en Nube InSSA y arrastralas todas juntas.</div>
<?php acc_fin(); ?>

<?php acc('Crear carpetas y documentos'); ?>
        <ol>
          <li>Entrá a la carpeta donde querés crear algo.</li>
          <li>Tocá <b>+ Nuevo</b>.</li>
          <li>Elegí <b>Nueva carpeta</b> o el tipo de documento que quieras crear.</li>
          <li>Escribí el nombre y confirmá.</li>
        </ol>
<?php if (app('onlyoffice')): ?>
        <p>En el mismo menú vas a encontrar las opciones para crear un documento de texto, una planilla de cálculo o una presentación. Se abren directamente en el editor. Mirá la sección <a href="#documentos">Documentos, planillas y presentaciones</a>.</p>
<?php endif; ?>
<?php acc_fin(); ?>

<?php acc('Abrir y ver archivos (vista previa)'); ?>
        <p>Tocá cualquier archivo para abrirlo. Las fotos, los videos, los PDF y muchos otros formatos se ven directamente en el navegador, sin descargarlos.</p>
        <p>Mientras mirás una imagen o un PDF podés pasar al siguiente con las flechas de los costados, y cerrar la vista con la cruz.</p>
<?php acc_fin(); ?>

<?php acc('Descargar'); ?>
        <p>Cada archivo tiene un botón de tres puntos (<b>⋯</b>) con todas sus acciones. Ahí está la opción <b>Descargar</b>.</p>
        <p>Si seleccionás varios archivos o una carpeta completa y los descargás juntos, se bajan en un único archivo comprimido (.zip).</p>
<?php acc_fin(); ?>

<?php acc('Renombrar, mover, copiar y eliminar'); ?>
        <p>Todas estas acciones están en el menú de tres puntos (<b>⋯</b>) de cada archivo o carpeta.</p>
        <ul>
          <li><b>Renombrar:</b> cambiá el nombre y apretá Enter.</li>
          <li><b>Mover o copiar:</b> se abre una ventana para elegir la carpeta de destino. Ahí decidís si lo movés (deja de estar en el lugar original) o lo copiás (queda en los dos lugares).</li>
          <li><b>Eliminar:</b> el archivo pasa a la papelera. Todavía lo podés recuperar. Mirá <a href="#papelera">Papelera y recuperación</a>.</li>
        </ul>
        <?php tip('También podés mover archivos arrastrándolos sobre una carpeta de la lista.'); ?>
<?php acc_fin(); ?>

<?php acc('Seleccionar varios archivos a la vez'); ?>
        <p>Pasá el mouse por un archivo y marcá la casilla que aparece a su izquierda. Podés marcar todos los que quieras. Con la casilla del encabezado seleccionás todo lo que hay en la carpeta.</p>
        <p>Cuando hay archivos seleccionados, arriba de la lista aparecen las acciones que se pueden aplicar a todos juntos: descargar, mover o copiar, eliminar y otras.</p>
        <div class="ejemplo"><b>Ejemplo:</b> para ordenar veinte documentos sueltos, seleccionalos todos y movelos juntos a una carpeta nueva.</div>
<?php acc_fin(); ?>

<?php acc('Ordenar, clasificar y cambiar la vista'); ?>
        <p>Tocá el título de una columna (nombre, tamaño o fecha de modificación) para ordenar la lista por ese dato. Si lo tocás de nuevo, se invierte el orden.</p>
        <p>Arriba a la derecha podés pasar de la vista en lista a la vista en cuadrícula, que muestra miniaturas grandes. Es cómoda para carpetas con muchas imágenes.</p>
<?php acc_fin(); ?>

<?php acc('Favoritos'); ?>
        <p>Marcá como favorito lo que usás todo el tiempo. Abrí el menú de tres puntos (<b>⋯</b>) y elegí la opción para agregarlo a favoritos. Aparece una estrellita.</p>
        <p>Después lo encontrás en la sección <b>Favoritos</b> del menú izquierdo de Archivos, sin tener que recorrer carpetas.</p>
<?php acc_fin(); ?>

<?php acc('Recientes y compartidos'); ?>
        <p>El menú izquierdo de Archivos tiene accesos directos muy útiles:</p>
        <ul>
          <li><b>Recientes:</b> los últimos archivos que se modificaron.</li>
          <li><b>Favoritos:</b> lo que marcaste con estrella.</li>
          <li><b>Compartidos:</b> lo que otras personas compartieron con vos y lo que vos compartiste, incluidos los enlaces.</li>
<?php if (app('systemtags')): ?>
          <li><b>Etiquetas:</b> los archivos agrupados por etiqueta.</li>
<?php endif; ?>
        </ul>
<?php acc_fin(); ?>

<?php acc('El panel de detalles: comentarios, versiones y actividad'); ?>
        <p>Abrí el menú de tres puntos (<b>⋯</b>) de un archivo y elegí la opción para ver sus detalles. Se abre un panel a la derecha con varias pestañas:</p>
        <ul>
<?php if (app('activity')): ?>
          <li><b>Actividad:</b> quién creó, cambió o compartió el archivo y cuándo.</li>
<?php endif; ?>
<?php if (app('comments')): ?>
          <li><b>Comentarios:</b> dejá notas sobre el archivo para las personas que lo comparten con vos. Si escribís <code>@</code> seguido del nombre de alguien, esa persona recibe un aviso.</li>
<?php endif; ?>
          <li><b>Compartir:</b> con quién está compartido y con qué permisos.</li>
<?php if (app('files_versions')): ?>
          <li><b>Versiones:</b> las copias anteriores del archivo, guardadas cada vez que alguien lo modificó.</li>
<?php endif; ?>
        </ul>
<?php acc_fin(); ?>

<?php if (app('files_versions')): ?>
<?php acc('Versiones e historial de cambios'); ?>
        <p>Cada vez que un archivo se modifica, Nube InSSA guarda la versión anterior. Si alguien borró un párrafo importante o reemplazó el archivo por error, podés volver atrás.</p>
        <ol>
          <li>Abrí los detalles del archivo.</li>
          <li>Entrá a la pestaña <b>Versiones</b>.</li>
          <li>Buscá la versión que querés por su fecha y elegí la opción para restaurarla. También podés descargarla para revisarla antes.</li>
        </ol>
        <p>Al restaurar no se pierde la versión actual: queda guardada como una versión más.</p>
        <?php tip('Las versiones viejas se van borrando solas con el tiempo, sobre todo si te estás quedando sin espacio. No uses las versiones como único respaldo de algo importante.', 'info'); ?>
<?php acc_fin(); ?>
<?php endif; ?>

<?php if (app('systemtags')): ?>
<?php acc('Etiquetas'); ?>
        <p>Las etiquetas sirven para clasificar archivos que están en carpetas distintas. Por ejemplo, "Urgente", "2026" o "Para revisar".</p>
        <p>Se agregan desde el panel de detalles del archivo. Después podés ver todos los archivos con una misma etiqueta desde la sección <b>Etiquetas</b> del menú izquierdo.</p>
<?php acc_fin(); ?>
<?php endif; ?>

<?php if (app('groupfolders')): ?>
<?php acc('Carpetas de grupo'); ?>
        <p>Algunas carpetas pertenecen a un grupo o área, no a una persona. Las ves en tu lista de archivos con un ícono distinto. Todas las personas del grupo pueden trabajar en ellas según los permisos que haya definido la administración.</p>
        <p>Si te vas del grupo, dejás de ver la carpeta, pero su contenido sigue ahí para el resto.</p>
<?php acc_fin(); ?>
<?php endif; ?>

<?php acc('Buscar dentro de Archivos'); ?>
        <p>Para encontrar un archivo usá la lupa de la barra superior. Si estás dentro de Archivos, podés filtrar la lista de la carpeta actual escribiendo el nombre. Más detalles en <a href="#busqueda">Búsqueda</a>.</p>
<?php acc_fin(); ?>

<?php acc('Almacenamiento: cuánto espacio tenés'); ?>
        <p>Al pie del menú izquierdo de Archivos se ve cuánto espacio estás usando y, si tu cuenta tiene un límite, cuánto te queda.</p>
        <p>Si te acercás al límite:</p>
        <ul>
          <li>Vaciá la <a href="#papelera">papelera</a>, porque lo que está ahí también ocupa lugar.</li>
          <li>Borrá los archivos duplicados o que ya no necesites.</li>
          <li>Si necesitás más espacio para tu trabajo, consultalo con la administración de Nube InSSA.</li>
        </ul>
        <?php tip('Los archivos que otras personas comparten con vos ocupan espacio en la cuenta de quien los compartió, no en la tuya.', 'info'); ?>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- COMPARTIR                                                          -->
<!-- ================================================================== -->
<?php seccion_abrir('compartir'); ?>
      <p class="intro">Compartir es darle acceso a otra persona a un archivo o una carpeta tuya, sin mandar copias por correo. Todos trabajan sobre el mismo archivo, así que nadie se queda con una versión vieja.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('persona') ?>Con una persona</h3><p>Escribís su nombre y listo. Lo ve en su propia cuenta.</p></div>
        <div class="tarjeta"><h3><?= svg('grupo') ?>Con un grupo</h3><p>Todas las personas del grupo reciben acceso de una sola vez.</p></div>
        <div class="tarjeta"><h3><?= svg('compartir') ?>Con un enlace</h3><p>Para alguien que no tiene cuenta en Nube InSSA.</p></div>
        <div class="tarjeta"><h3><?= svg('candado') ?>Con permisos</h3><p>Vos decidís si puede solamente ver, o también editar.</p></div>
      </div>

<?php acc('Compartir con una persona o un grupo', 'todos', true); ?>
        <ol>
          <li>En Archivos, tocá el ícono de compartir del archivo o carpeta, o abrí sus detalles y entrá a la pestaña <b>Compartir</b>.</li>
          <li>Escribí el nombre de la persona o del grupo en el buscador.</li>
          <li>Elegilo de la lista.</li>
          <li>Revisá los permisos y confirmá.</li>
        </ol>
        <p>La otra persona lo va a encontrar en su sección <b>Compartidos</b> y recibe una notificación.</p>
        <div class="ejemplo"><b>Ejemplo:</b> compartís la carpeta "Informe anual" con el grupo de tu área. Cada integrante puede abrirla desde su propia cuenta y, si le diste permiso, agregar sus partes.</div>
<?php acc_fin(); ?>

<?php acc('Enlaces públicos'); ?>
        <p>Un enlace público sirve para compartir con alguien que no tiene cuenta. Cualquiera que tenga el enlace puede abrir el archivo, así que usalo con cuidado.</p>
        <ol>
          <li>En la pestaña <b>Compartir</b>, elegí la opción para crear un enlace.</li>
          <li>Copiá el enlace y mandáselo a quien lo necesite.</li>
        </ol>
        <p>En las opciones del enlace podés, según lo que tenga habilitado la plataforma:</p>
        <ul>
          <li>Ponerle una <b>contraseña</b>.</li>
          <li>Ponerle una <b>fecha de vencimiento</b>, para que deje de funcionar solo.</li>
          <li>Decidir si se puede solamente ver o también editar o subir archivos.</li>
          <li>Ocultar la descarga, si la opción aparece.</li>
        </ul>
        <?php tip('Para información personal o sensible, poné siempre contraseña y fecha de vencimiento. Mandá la contraseña por otro medio, no en el mismo mensaje que el enlace.', 'alerta'); ?>
<?php acc_fin(); ?>

<?php acc('Permisos: qué puede hacer la otra persona'); ?>
        <p>Al compartir podés ajustar qué se permite. Las opciones más comunes son:</p>
        <ul>
          <li><b>Solo lectura:</b> puede ver y descargar, pero no cambiar nada.</li>
          <li><b>Edición:</b> puede modificar el contenido.</li>
          <li><b>Crear y eliminar</b> (en carpetas): puede agregar archivos nuevos o borrar los existentes.</li>
          <li><b>Volver a compartir:</b> puede compartirlo a su vez con otras personas.</li>
        </ul>
        <div class="ejemplo"><b>Ejemplo:</b> para un reglamento que todos tienen que leer, alcanza con solo lectura. Para una planilla que completa tu equipo, dales edición.</div>
<?php acc_fin(); ?>

<?php acc('Cambiar los permisos o dejar de compartir'); ?>
        <p>En la pestaña <b>Compartir</b> ves la lista de personas, grupos y enlaces con acceso. Abrí el menú de cada uno para cambiar sus permisos o para dejar de compartir.</p>
        <p>Cuando dejás de compartir, la otra persona pierde el acceso en el momento. Si ya lo había descargado, esa copia queda en su equipo.</p>
<?php acc_fin(); ?>

<?php acc('Lo que otros comparten con vos'); ?>
        <p>Aparece en tu lista de archivos y en la sección <b>Compartidos</b>. Podés moverlo a la carpeta que quieras dentro de tu cuenta sin afectar a nadie.</p>
        <p>Si ya no lo necesitás, podés quitarlo de tu lista. Eso no borra el archivo original de quien lo compartió.</p>
<?php acc_fin(); ?>

<?php acc('Reglas de compartición de la institución', 'admin'); ?>
        <p>La administración define desde la configuración general qué se puede compartir y cómo. Por ejemplo, si se permiten enlaces públicos, si los enlaces requieren contraseña, si tienen vencimiento obligatorio o si se puede compartir con cualquier persona o solamente con miembros de los mismos grupos.</p>
        <p>Estas reglas se aplican a todas las cuentas. Si una opción no te aparece al compartir, probablemente esté desactivada por esta configuración.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<?php if (app('onlyoffice')): ?>
<!-- ================================================================== -->
<!-- DOCUMENTOS (ONLYOFFICE)                                            -->
<!-- ================================================================== -->
<?php seccion_abrir('documentos'); ?>
      <p class="intro">Nube InSSA incluye el editor ONLYOFFICE. Con él podés crear y editar documentos de texto, planillas de cálculo y presentaciones directamente en el navegador, sin instalar nada. Y lo mejor es que varias personas pueden trabajar en el mismo archivo al mismo tiempo.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('documento') ?>Documentos de texto</h3><p>Notas, informes, cartas. Compatible con archivos de Word (.docx).</p></div>
        <div class="tarjeta"><h3><?= svg('grafico') ?>Planillas de cálculo</h3><p>Tablas, fórmulas y gráficos. Compatible con Excel (.xlsx).</p></div>
        <div class="tarjeta"><h3><?= svg('tablero') ?>Presentaciones</h3><p>Diapositivas para exponer. Compatible con PowerPoint (.pptx).</p></div>
        <div class="tarjeta"><h3><?= svg('grupo') ?>En equipo</h3><p>Ves en vivo lo que escriben las demás personas.</p></div>
      </div>

<?php acc('Crear un documento nuevo', 'todos', true); ?>
        <ol>
          <li>En Archivos, entrá a la carpeta donde lo querés guardar.</li>
          <li>Tocá <b>+ Nuevo</b> y elegí documento, planilla o presentación.</li>
          <li>Ponele un nombre y confirmá. El editor se abre enseguida.</li>
        </ol>
<?php acc_fin(); ?>

<?php acc('Abrir y editar un documento existente'); ?>
        <p>Tocá el archivo en la lista. Se abre en el editor dentro de Nube InSSA. Funciona con los archivos de Office que ya tengas (.docx, .xlsx, .pptx) y con otros formatos habituales.</p>
        <p>Algunos formatos más viejos (como .doc o .xls) pueden abrirse en modo de solo lectura o pedirte convertirlos a un formato moderno para editarlos.</p>
<?php acc_fin(); ?>

<?php acc('¿Cómo se guardan los cambios?'); ?>
        <p>No hace falta que guardes a mano. Los cambios se guardan solos mientras trabajás.</p>
        <p>Cuando cerrás el editor, el archivo se actualiza en Nube InSSA. Si hay varias personas trabajando, la versión final queda guardada cuando todas cierran el documento. Puede tardar unos segundos en verse reflejado en la lista.</p>
        <p>Cada guardado genera una versión nueva, así que siempre podés volver atrás desde la pestaña <b>Versiones</b> del archivo.</p>
        <?php tip('Para cerrar, usá el botón de cerrar del editor en lugar de cerrar la pestaña del navegador de golpe. Así te asegurás de que todo quede guardado.', 'info'); ?>
<?php acc_fin(); ?>

<?php acc('Trabajar al mismo tiempo que otras personas'); ?>
        <p>Para que alguien edite con vos, compartile el archivo con permiso de edición. Mirá <a href="#compartir">Compartir información</a>.</p>
        <p>Cuando dos o más personas tienen el documento abierto, ves arriba quiénes están conectadas y, en el texto, marcas de colores que muestran dónde está escribiendo cada una.</p>
        <p>El editor tiene dos modos de coedición, que se eligen desde la pestaña <b>Colaboración</b>:</p>
        <ul>
          <li><b>Rápido:</b> ves los cambios de los demás a medida que escriben.</li>
          <li><b>Estricto:</b> los cambios se muestran recién cuando cada persona guarda.</li>
        </ul>
        <div class="ejemplo"><b>Ejemplo:</b> tres personas completan el mismo informe. Cada una escribe su parte en el mismo archivo y nadie tiene que juntar tres versiones al final.</div>
<?php acc_fin(); ?>

<?php acc('Comentarios y control de cambios dentro del documento'); ?>
        <p>Seleccioná un texto y agregá un comentario desde la pestaña <b>Colaboración</b>. Las demás personas pueden responderlo o marcarlo como resuelto.</p>
        <p>Si activás el <b>control de cambios</b>, cada modificación queda marcada para que alguien la acepte o la rechace. Es útil para revisiones y correcciones.</p>
<?php acc_fin(); ?>

<?php acc('Descargar en otro formato (por ejemplo, PDF)'); ?>
        <p>Dentro del editor, en el menú <b>Archivo</b>, buscá la opción para descargar como. Ahí elegís el formato: PDF, el formato de Office o un formato abierto.</p>
        <p>También podés descargar el archivo original desde la lista de Archivos, como cualquier otro archivo.</p>
<?php acc_fin(); ?>
<?php if (app('text')): ?>
<?php acc('Notas rápidas en texto simple'); ?>
        <p>Además del editor de documentos, Nube InSSA permite crear archivos de texto simple (.md o .txt) que se editan directamente en la lista de archivos. Son ideales para anotaciones rápidas o listas.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (app('photos')): ?>
<!-- ================================================================== -->
<!-- FOTOS                                                              -->
<!-- ================================================================== -->
<?php seccion_abrir('fotos'); ?>
      <p class="intro">Fotos reúne todas las imágenes y videos que guardaste en Nube InSSA y los muestra ordenados por fecha, como una galería. No tenés que subirlos dos veces: son los mismos archivos que ves en Archivos.</p>

<?php acc('Ver tus fotos y videos', 'todos', true); ?>
        <p>Al abrir Fotos ves todas tus imágenes ordenadas de la más nueva a la más vieja. Tocá una para verla en grande. Usá las flechas para pasar a la siguiente.</p>
        <p>En el menú izquierdo podés elegir ver sólo fotos, sólo videos o tus favoritas, entre otras vistas.</p>
<?php acc_fin(); ?>
<?php if (version_app('photos') >= 2): ?>
<?php acc('Álbumes'); ?>
        <p>Los álbumes agrupan fotos sin moverlas de su carpeta original. Una misma foto puede estar en varios álbumes.</p>
        <ol>
          <li>Entrá a la sección de álbumes y creá uno nuevo con un nombre.</li>
          <li>Agregale las fotos que quieras.</li>
        </ol>
        <p>Los álbumes se pueden compartir con otras personas de Nube InSSA para que también los vean.</p>
        <div class="ejemplo"><b>Ejemplo:</b> un álbum "Acto de fin de año" con las fotos que sacaron distintas personas, cada una guardada en su propia carpeta.</div>
<?php acc_fin(); ?>
<?php endif; ?>
<?php acc('Organizar y marcar favoritas'); ?>
        <p>Marcá tus mejores fotos como favoritas para encontrarlas rápido. Para ordenarlas en carpetas, hacelo desde <a href="#archivos">Archivos</a>: cualquier cambio ahí se refleja en Fotos.</p>
        <p>Desde la configuración de Fotos podés elegir de qué carpetas se toman las imágenes, si esa opción está disponible en tu versión.</p>
<?php acc_fin(); ?>
<?php acc('Compartir y descargar fotos'); ?>
        <p>Seleccioná una o varias fotos para ver las acciones disponibles: descargar, marcar como favoritas, agregar a un álbum y otras.</p>
        <p>Para compartir una carpeta completa de fotos, usá las opciones de <a href="#compartir">Compartir</a> desde Archivos.</p>
<?php acc_fin(); ?>
<?php acc('Fotos desde el celular'); ?>
        <p>Fotos se adapta a la pantalla del teléfono. Podés ver tu galería desde el navegador del celular igual que en la computadora.</p>
        <p>Para subir fotos del teléfono, abrí Archivos desde el navegador, entrá a la carpeta y usá <b>+ Nuevo</b> para subirlas. Mirá también <a href="#moviles">Celulares y tablets</a>.</p>
<?php acc_fin(); ?>
<?php acc('Tus fotos personales'); ?>
        <p>Tus fotos son privadas. Nadie más las ve salvo que vos las compartas. Recordá que Nube InSSA es una herramienta institucional: guardá ahí lo que corresponde a tu trabajo.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (app('contacts')): ?>
<!-- ================================================================== -->
<!-- CONTACTOS                                                          -->
<!-- ================================================================== -->
<?php seccion_abrir('contactos'); ?>
      <p class="intro">Contactos es tu agenda. Guardá nombres, teléfonos, correos y direcciones en un solo lugar y tenelos disponibles desde cualquier dispositivo.</p>

<?php acc('Crear un contacto', 'todos', true); ?>
        <ol>
          <li>Abrí Contactos.</li>
          <li>Tocá el botón para crear un contacto nuevo, arriba a la izquierda.</li>
          <li>Completá los datos: nombre, teléfono, correo, organización y lo que necesites. Podés agregar varios teléfonos o correos.</li>
        </ol>
        <p>Los datos se guardan solos a medida que los escribís.</p>
<?php acc_fin(); ?>
<?php acc('Editar o eliminar un contacto'); ?>
        <p>Elegí el contacto de la lista y modificá cualquier campo directamente. Para borrarlo, usá el menú de acciones del contacto y elegí la opción de eliminar.</p>
        <?php tip('Al eliminar un contacto desaparece de todos tus dispositivos sincronizados.', 'alerta'); ?>
<?php acc_fin(); ?>
<?php acc('Organizar: grupos y libretas'); ?>
        <p>Podés asignar cada contacto a uno o más grupos (por ejemplo, "Proveedores" o "Docentes"). Los grupos aparecen en el menú izquierdo y te dejan ver sólo esa parte de la agenda.</p>
        <p>También podés tener varias libretas de direcciones y compartirlas con otras personas desde la configuración de Contactos.</p>
<?php acc_fin(); ?>
<?php acc('Buscar contactos'); ?>
        <p>Usá el buscador de la parte superior de la lista. Busca por nombre, correo, teléfono y otros datos.</p>
<?php acc_fin(); ?>
<?php acc('Importar contactos'); ?>
        <p>Si tenés tus contactos en otro programa, exportalos como archivo .vcf y usá la opción de importar de Contactos para traerlos todos juntos.</p>
<?php acc_fin(); ?>
<?php acc('Tus contactos en otras aplicaciones'); ?>
        <p>Los contactos se usan en otras partes de Nube InSSA:</p>
        <ul>
<?php if (app('mail')): ?>
          <li>En <b>Correo</b>, al escribir un destinatario te sugiere direcciones de tu agenda.</li>
<?php endif; ?>
<?php if (app('calendar')): ?>
          <li>En <b>Calendario</b>, podés invitarlos a eventos. Además, los cumpleaños que cargues en tus contactos pueden aparecer en un calendario de cumpleaños.</li>
<?php endif; ?>
          <li>En tus dispositivos, si los sincronizás. Mirá <a href="#moviles">Celulares y tablets</a>.</li>
        </ul>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (app('calendar')): ?>
<!-- ================================================================== -->
<!-- CALENDARIO                                                         -->
<!-- ================================================================== -->
<?php seccion_abrir('calendario'); ?>
      <p class="intro">Con Calendario organizás tu agenda: reuniones, entregas, capacitaciones y cualquier fecha importante. Podés invitar a otras personas y compartir calendarios completos.</p>

<?php acc('Crear un evento', 'todos', true); ?>
        <ol>
          <li>Hacé clic en el día y la hora del calendario, o usá el botón para crear un evento nuevo.</li>
          <li>Escribí el título y ajustá la fecha y la hora.</li>
          <li>Si querés, agregá lugar, descripción, participantes y recordatorios.</li>
          <li>Guardá.</li>
        </ol>
        <p>Si marcás que dura todo el día, el evento no tiene horario (útil para feriados o licencias).</p>
<?php acc_fin(); ?>
<?php acc('Editar o eliminar un evento'); ?>
        <p>Tocá el evento para abrirlo. Cambiá lo que necesites y guardá. Para moverlo de horario, también podés arrastrarlo en la vista de semana o de día.</p>
        <p>Para borrarlo, abrilo y usá la opción de eliminar de su menú de acciones.</p>
<?php acc_fin(); ?>
<?php acc('Eventos que se repiten'); ?>
        <p>Al crear o editar un evento, buscá la opción de repetición. Podés hacer que se repita cada día, cada semana, cada mes o cada año, y decidir hasta cuándo.</p>
        <p>Cuando modifiques uno de esos eventos, se te va a preguntar si el cambio es sólo para ese día o para todos los siguientes.</p>
        <div class="ejemplo"><b>Ejemplo:</b> la reunión de equipo de todos los lunes a las 9. La creás una vez y aparece sola cada semana.</div>
<?php acc_fin(); ?>
<?php acc('Participantes e invitaciones'); ?>
        <p>En el evento, agregá participantes escribiendo su nombre o su correo. Nube InSSA les manda la invitación por correo si ese envío está configurado en la plataforma. Cada persona puede responder si asiste o no, y vos ves las respuestas en el evento.</p>
        <p>Si tenés varios participantes de Nube InSSA, podés revisar su disponibilidad para elegir un horario libre.</p>
<?php acc_fin(); ?>
<?php acc('Recordatorios'); ?>
        <p>Agregá uno o más recordatorios a cada evento (por ejemplo, 15 minutos antes). Te llega un aviso como <a href="#notificaciones">notificación</a> o por correo, según lo que elijas.</p>
<?php acc_fin(); ?>
<?php acc('Tus calendarios y los compartidos'); ?>
        <p>Podés tener varios calendarios, cada uno con su color: por ejemplo "Trabajo" y "Capacitaciones". Los creás desde el menú izquierdo.</p>
        <p>Cada calendario se puede compartir:</p>
        <ul>
          <li>Con personas o grupos de Nube InSSA, para que lo vean o lo editen.</li>
          <li>Con un enlace público, para que alguien externo lo vea.</li>
        </ul>
        <p>Los calendarios que otros comparten con vos aparecen en tu lista y podés mostrarlos u ocultarlos con un clic.</p>
<?php acc_fin(); ?>
<?php acc('Vista por día, semana, mes o lista'); ?>
        <p>Arriba a la izquierda elegís cómo ver el calendario: por día, por semana, por mes o como lista de próximos eventos. Con las flechas avanzás o retrocedés, y con el botón de hoy volvés a la fecha actual.</p>
<?php acc_fin(); ?>
<?php acc('Sincronizar con el celular u otros programas'); ?>
        <p>Tus calendarios se pueden sincronizar con aplicaciones de calendario compatibles con el estándar CalDAV. Mirá <a href="#moviles">Celulares y tablets</a>.</p>
        <p>También podés importar eventos desde un archivo .ics, o exportar un calendario entero, desde la configuración de Calendario.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (app('mail')): ?>
<!-- ================================================================== -->
<!-- CORREO                                                             -->
<!-- ================================================================== -->
<?php seccion_abrir('correo'); ?>
      <p class="intro">Correo te permite leer y enviar mensajes de tu casilla sin salir de Nube InSSA. Se conecta con tu cuenta de correo existente.</p>

<?php acc('Configurar tu cuenta de correo', 'todos', true); ?>
        <p>La primera vez que abrís Correo, te pide los datos de tu casilla: tu dirección, tu contraseña y, en algunos casos, los datos del servidor de correo. Si no sabés cuáles son, consultá con la administración.</p>
        <p>Podés agregar más de una cuenta. Cada una aparece en el menú izquierdo con sus carpetas.</p>
        <?php tip('En algunas instalaciones la cuenta institucional ya viene configurada. Si al entrar ya ves tus mensajes, no tenés que hacer nada.', 'info'); ?>
<?php acc_fin(); ?>
<?php acc('Bandeja de entrada, enviados, borradores y papelera'); ?>
        <p>En el menú izquierdo ves las carpetas de tu cuenta:</p>
        <ul>
          <li><b>Bandeja de entrada:</b> los mensajes que recibís.</li>
          <li><b>Enviados:</b> los que mandaste.</li>
          <li><b>Borradores:</b> los mensajes que empezaste y no enviaste.</li>
          <li><b>Papelera:</b> los que borraste.</li>
        </ul>
        <p>Además aparecen las carpetas propias que tengas en tu casilla.</p>
<?php acc_fin(); ?>
<?php acc('Leer, responder y reenviar'); ?>
        <p>Tocá un mensaje para leerlo. Arriba del mensaje tenés las opciones para responder, responder a todos y reenviar, y un menú con más acciones.</p>
<?php acc_fin(); ?>
<?php acc('Redactar un mensaje nuevo'); ?>
        <ol>
          <li>Tocá el botón para escribir un mensaje nuevo, arriba a la izquierda.</li>
          <li>Escribí los destinatarios. Te sugiere direcciones de tus contactos.</li>
          <li>Completá el asunto y el texto.</li>
          <li>Enviá.</li>
        </ol>
        <p>Si cerrás el mensaje sin enviarlo, queda guardado en Borradores.</p>
<?php acc_fin(); ?>
<?php acc('Adjuntar archivos y descargar adjuntos'); ?>
        <p>Al redactar, usá el botón de adjuntar. Podés subir un archivo desde tu computadora o elegir uno que ya esté en tus Archivos de Nube InSSA.</p>
        <p>Cuando recibís un adjunto, podés descargarlo a tu equipo o guardarlo directamente en tus Archivos.</p>
<?php acc_fin(); ?>
<?php acc('Buscar y organizar el correo'); ?>
        <p>Usá el buscador de la parte superior de la lista de mensajes para encontrar correos por remitente, asunto o texto.</p>
        <p>Podés mover mensajes a otras carpetas, marcarlos como importantes, como leídos o no leídos, y borrarlos.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (app('forms')): ?>
<!-- ================================================================== -->
<!-- FORMULARIOS                                                        -->
<!-- ================================================================== -->
<?php seccion_abrir('formularios'); ?>
      <p class="intro">Con Formularios creás encuestas, inscripciones o relevamientos en pocos minutos. Compartís un enlace, la gente responde y vos ves los resultados ordenados.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('formulario') ?>Inscripciones</h3><p>A una capacitación, jornada o actividad.</p></div>
        <div class="tarjeta"><h3><?= svg('lista') ?>Relevamientos</h3><p>Datos del personal, inventarios, necesidades.</p></div>
        <div class="tarjeta"><h3><?= svg('grafico') ?>Encuestas</h3><p>Satisfacción, opiniones, evaluaciones de cursos.</p></div>
      </div>

<?php acc('Crear un formulario', 'todos', true); ?>
        <ol>
          <li>Abrí Formularios y tocá el botón para crear uno nuevo.</li>
          <li>Escribí el título y una descripción que explique para qué es.</li>
          <li>Agregá las preguntas.</li>
        </ol>
<?php acc_fin(); ?>
<?php acc('Tipos de preguntas'); ?>
        <p>Podés combinar distintos tipos de preguntas, por ejemplo:</p>
        <ul>
          <li>Opción única o casillas de varias opciones.</li>
          <li>Lista desplegable.</li>
          <li>Respuesta corta o texto largo.</li>
          <li>Fecha u hora.</li>
        </ul>
        <p>Cada pregunta se puede marcar como obligatoria.</p>
<?php acc_fin(); ?>
<?php acc('Compartir el formulario y recibir respuestas'); ?>
        <p>Desde la configuración del formulario podés compartirlo con personas o grupos de Nube InSSA, o generar un enlace para que responda cualquiera.</p>
        <p>También podés decidir, entre otras opciones, si las respuestas son anónimas, si cada persona puede responder una sola vez y hasta qué fecha se aceptan respuestas.</p>
<?php acc_fin(); ?>
<?php acc('Ver los resultados'); ?>
        <p>En la sección de respuestas del formulario ves un resumen con gráficos por pregunta y el detalle de cada respuesta individual.</p>
<?php acc_fin(); ?>
<?php acc('Exportar las respuestas'); ?>
        <p>Podés exportar las respuestas a una planilla para trabajarlas o archivarlas, y guardarlas en tus Archivos o descargarlas.</p>
        <div class="ejemplo"><b>Ejemplo:</b> para una capacitación, armás el formulario de inscripción, compartís el enlace y al cerrar la inscripción exportás la lista de personas anotadas.</div>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (app('deck')): ?>
<!-- ================================================================== -->
<!-- TABLEROS                                                           -->
<!-- ================================================================== -->
<?php seccion_abrir('tableros'); ?>
      <p class="intro">Tableros te ayuda a organizar tareas y proyectos de forma visual, con tarjetas que movés entre columnas. Por ejemplo: "Pendiente", "En curso" y "Terminado".</p>
<?php acc('Crear un tablero', 'todos', true); ?>
        <p>Creá un tablero nuevo desde el menú izquierdo y ponele un nombre. Después agregá columnas (listas) y, dentro de cada una, tarjetas con las tareas.</p>
<?php acc_fin(); ?>
<?php acc('Trabajar con tarjetas'); ?>
        <p>Cada tarjeta puede tener descripción, fecha de vencimiento, etiquetas de colores, personas asignadas, comentarios y archivos adjuntos. Arrastrala de una columna a otra a medida que avanza.</p>
<?php acc_fin(); ?>
<?php acc('Compartir un tablero con tu equipo'); ?>
        <p>Desde el panel del tablero podés compartirlo con personas o grupos y decidir si pueden editarlo o sólo verlo.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (app('spreed')): ?>
<!-- ================================================================== -->
<!-- CONVERSACIONES                                                     -->
<!-- ================================================================== -->
<?php seccion_abrir('conversaciones'); ?>
      <p class="intro">Conversaciones te permite chatear y hacer llamadas de voz o video con otras personas de la institución.</p>
<?php acc('Empezar una conversación', 'todos', true); ?>
        <p>Abrí la aplicación y buscá a la persona o al grupo con quien querés hablar. Podés crear conversaciones individuales o grupales.</p>
<?php acc_fin(); ?>
<?php acc('Llamadas y videollamadas'); ?>
        <p>Dentro de una conversación, usá el botón de llamada para empezar una llamada. El navegador te va a pedir permiso para usar la cámara y el micrófono.</p>
<?php acc_fin(); ?>
<?php acc('Compartir archivos en el chat'); ?>
        <p>Podés adjuntar archivos desde tu equipo o desde tus Archivos de Nube InSSA. Las personas de la conversación reciben acceso a ellos.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (isset($SECCIONES['mas-apps'])): ?>
<!-- ================================================================== -->
<!-- OTRAS APLICACIONES                                                 -->
<!-- ================================================================== -->
<?php seccion_abrir('mas-apps'); ?>
      <p class="intro">Además de las herramientas principales, tu cuenta tiene estas aplicaciones.</p>
      <div class="accesos">
<?php if (app('tasks')): ?>
        <a class="acceso" href="<?= h(ruta_app('tasks')) ?>" target="_top"><span class="chip"><?= icono(icono_app('tasks'), 'lista') ?></span><span><strong>Tareas</strong><small>Listas de pendientes con fechas y prioridades. Se sincronizan con tu calendario.</small></span></a>
<?php endif; ?>
<?php if (app('notes')): ?>
        <a class="acceso" href="<?= h(ruta_app('notes')) ?>" target="_top"><span class="chip"><?= icono(icono_app('notes'), 'documento') ?></span><span><strong>Notas</strong><small>Apuntes rápidos, guardados como archivos en tu cuenta.</small></span></a>
<?php endif; ?>
<?php if (app('polls')): ?>
        <a class="acceso" href="<?= h(ruta_app('polls')) ?>" target="_top"><span class="chip"><?= icono(icono_app('polls'), 'grafico') ?></span><span><strong>Encuestas</strong><small>Votaciones simples y búsqueda de fechas para reuniones.</small></span></a>
<?php endif; ?>
<?php if (app('bookmarks')): ?>
        <a class="acceso" href="<?= h(ruta_app('bookmarks')) ?>" target="_top"><span class="chip"><?= icono(icono_app('bookmarks'), 'abrir') ?></span><span><strong>Marcadores</strong><small>Guardá y ordená enlaces de sitios web.</small></span></a>
<?php endif; ?>
      </div>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<!-- ================================================================== -->
<!-- ACTIVIDAD                                                          -->
<!-- ================================================================== -->
<?php seccion_abrir('actividad'); ?>
      <p class="intro">Actividad es un registro de todo lo que pasó con tus archivos y tu cuenta. Sirve para responder preguntas como "¿quién cambió este documento?" o "¿qué me compartieron esta semana?".</p>
<?php acc('Qué muestra la actividad', 'todos', true); ?>
        <ul>
          <li>Archivos creados, modificados, renombrados, movidos o eliminados.</li>
          <li>Archivos y carpetas que alguien compartió con vos, o que vos compartiste.</li>
          <li>Comentarios en tus archivos.</li>
<?php if (app('calendar')): ?>
          <li>Cambios en calendarios y eventos.</li>
<?php endif; ?>
<?php if (app('contacts')): ?>
          <li>Cambios en libretas de contactos compartidas.</li>
<?php endif; ?>
          <li>Eventos de seguridad de tu cuenta, como un cambio de contraseña.</li>
        </ul>
<?php acc_fin(); ?>
<?php acc('Filtrar la actividad'); ?>
        <p>En el menú izquierdo de Actividad elegís qué ver: toda la actividad, sólo lo que hiciste vos, sólo lo que hicieron otras personas, lo relacionado con tus favoritos, las comparticiones, los comentarios y otras categorías.</p>
<?php acc_fin(); ?>
<?php acc('Actividad de un archivo en particular'); ?>
        <p>En <a href="#archivos">Archivos</a>, abrí los detalles de un archivo y entrá a la pestaña <b>Actividad</b>. Ves solamente su historia.</p>
<?php acc_fin(); ?>
<?php acc('Actividades institucionales'); ?>
        <p>Además de tu actividad personal, en Actividad aparecen los movimientos de las carpetas y documentos que tu área comparte con vos. Así te enterás cuando se sube un material nuevo o se actualiza un documento de trabajo común.</p>
<?php acc_fin(); ?>
<?php acc('Recibir un resumen por correo'); ?>
        <p>En tu configuración, en la sección de notificaciones o actividad, podés elegir qué tipo de movimientos querés recibir por correo y con qué frecuencia (por ejemplo, un resumen diario o semanal).</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- NOTIFICACIONES                                                     -->
<!-- ================================================================== -->
<?php seccion_abrir('notificaciones'); ?>
      <p class="intro">Las notificaciones te avisan de lo que requiere tu atención. Aparecen en la campana de la barra superior. Un punto sobre la campana indica que tenés avisos sin leer.</p>
<?php acc('Qué tipo de avisos vas a recibir', 'todos', true); ?>
        <ul>
          <li>Alguien compartió un archivo o carpeta con vos.</li>
          <li>Alguien te mencionó con <code>@</code> en un comentario.</li>
<?php if (app('calendar')): ?>
          <li>Recordatorios de eventos y respuestas a tus invitaciones.</li>
<?php endif; ?>
          <li>Avisos de la administración sobre la plataforma.</li>
          <li>Avisos de tu propia cuenta, como que te estás quedando sin espacio.</li>
        </ul>
<?php acc_fin(); ?>
<?php acc('Leer y descartar avisos'); ?>
        <p>Tocá la campana para ver la lista. Tocá un aviso para ir a lo que se refiere. Podés descartarlos de a uno o todos juntos.</p>
<?php acc_fin(); ?>
<?php acc('Elegir qué avisos querés recibir'); ?>
        <p>Entrá a tu configuración (tu foto, arriba a la derecha) y buscá la sección de notificaciones. Ahí decidís qué eventos te avisan en la plataforma y cuáles también por correo.</p>
        <p>El navegador también puede mostrarte avisos en la pantalla aunque tengas otra pestaña abierta, si le das permiso.</p>
<?php acc_fin(); ?>
<?php if (app('user_status')): ?>
<?php acc('Tu estado'); ?>
        <p>Desde el menú de tu foto podés indicar tu estado: disponible, ausente, no molestar o invisible, y escribir un mensaje. Con "no molestar" dejás de recibir avisos emergentes.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- BÚSQUEDA                                                           -->
<!-- ================================================================== -->
<?php seccion_abrir('busqueda'); ?>
      <p class="intro">El buscador de Nube InSSA encuentra información en todas las aplicaciones al mismo tiempo. Lo abrís con la lupa de la barra superior.</p>
<?php acc('Cómo buscar', 'todos', true); ?>
        <ol>
          <li>Tocá la lupa de la barra superior.</li>
          <li>Escribí una palabra: parte del nombre de un archivo, de una persona o de un evento.</li>
          <li>Los resultados aparecen agrupados por aplicación. Tocá el que te interese.</li>
        </ol>
<?php acc_fin(); ?>
<?php acc('Qué podés encontrar'); ?>
        <ul>
          <li>Archivos y carpetas por su nombre.</li>
<?php if (app('onlyoffice')): ?>
          <li>Documentos, planillas y presentaciones.</li>
<?php endif; ?>
<?php if (app('photos')): ?>
          <li>Fotos y videos.</li>
<?php endif; ?>
<?php if (app('contacts')): ?>
          <li>Contactos de tu agenda.</li>
<?php endif; ?>
<?php if (app('calendar')): ?>
          <li>Eventos del calendario.</li>
<?php endif; ?>
<?php if (app('mail')): ?>
          <li>Mensajes de correo.</li>
<?php endif; ?>
<?php if (app('comments')): ?>
          <li>Comentarios en archivos.</li>
<?php endif; ?>
        </ul>
<?php acc_fin(); ?>
<?php acc('Filtros'); ?>
        <p>Debajo del cuadro de búsqueda aparecen filtros para acotar los resultados, por ejemplo por aplicación, por fecha o por persona. Los filtros disponibles dependen de la versión de la plataforma.</p>
        <div class="ejemplo"><b>Ejemplo:</b> buscás "presupuesto" y filtrás por fecha del último mes para quedarte con la versión más nueva.</div>
<?php acc_fin(); ?>
<?php acc('Buscar dentro de una aplicación'); ?>
        <p>Varias aplicaciones tienen su propio buscador o filtro, que busca sólo ahí: Archivos filtra la carpeta que tenés abierta<?= app('contacts') ? ', Contactos filtra tu agenda' : '' ?><?= app('mail') ? ' y Correo busca en tus mensajes' : '' ?>.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- PAPELERA                                                           -->
<!-- ================================================================== -->
<?php seccion_abrir('papelera'); ?>
      <p class="intro">Cuando eliminás un archivo o una carpeta, no se borra enseguida: pasa a la papelera. Desde ahí lo podés recuperar si te equivocaste.</p>
<?php acc('Dónde está la papelera', 'todos', true); ?>
        <p>En Archivos, al pie del menú izquierdo, está la sección de archivos eliminados (la papelera). Ahí ves todo lo que borraste, con la fecha y la carpeta de donde salió.</p>
<?php acc_fin(); ?>
<?php acc('Restaurar un archivo'); ?>
        <ol>
          <li>Entrá a la papelera.</li>
          <li>Buscá el archivo o la carpeta.</li>
          <li>Elegí la opción para restaurarlo.</li>
        </ol>
        <p>Vuelve a la misma carpeta donde estaba. Si esa carpeta ya no existe, lo vas a encontrar en la raíz de tus Archivos.</p>
<?php acc_fin(); ?>
<?php acc('Eliminar definitivamente'); ?>
        <p>Desde la papelera podés borrar un archivo para siempre, o vaciarla por completo para liberar espacio.</p>
        <?php tip('<b>Esta acción no se puede deshacer.</b> Lo que eliminás de la papelera no se puede recuperar desde tu cuenta. Revisá bien antes de confirmar.', 'alerta'); ?>
<?php acc_fin(); ?>
<?php acc('¿Cuánto tiempo se guarda?'); ?>
        <p>Los archivos eliminados se conservan en la papelera durante un tiempo que define la administración. Pasado ese tiempo, o si tu cuenta se queda sin espacio, se borran solos, empezando por los más viejos.</p>
<?php acc_fin(); ?>
<?php acc('Archivos compartidos y papelera'); ?>
        <p>Si borrás algo que otra persona compartió con vos, solamente lo quitás de tu lista: el original sigue en la cuenta de quien lo compartió. Si sos la dueña o el dueño y lo borrás, lo pierden todos, pero va a tu papelera y lo podés recuperar.</p>
<?php acc_fin(); ?>
<?php if (app('files_versions')): ?>
<?php acc('¿Y si el archivo no está borrado pero se arruinó?'); ?>
        <p>Para eso están las versiones. Abrí los detalles del archivo, entrá a <b>Versiones</b> y restaurá una versión anterior. Mirá <a href="#archivos">Archivos</a>.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php seccion_cerrar(); ?>

<?php if (isset($SECCIONES['cursos'])): ?>
<!-- ================================================================== -->
<!-- CURSOS                                                             -->
<!-- ================================================================== -->
<?php seccion_abrir('cursos'); ?>
      <p class="intro">Nube InSSA también se usa para la formación. Desde el módulo de cursos se organizan los contenidos educativos: materiales de estudio, documentos de apoyo y recursos para cada curso.</p>
      <div class="dos-columnas">
        <div class="columna columna-user">
          <h3><?= svg('persona', 'ico ico-rol') ?>Si participás de un curso</h3>
          <ul>
            <li>Accedés a los materiales y recursos del curso.</li>
            <li>Consultás los documentos desde cualquier dispositivo.</li>
            <li>Descargás lo que necesites para estudiar.</li>
          </ul>
        </div>
        <div class="columna columna-admin" data-rol="admin">
          <h3><?= svg('llave', 'ico ico-rol') ?>Si administrás cursos</h3>
          <ul>
            <li>Cargás y ordenás los materiales.</li>
            <li>Organizás los contenidos por curso o tema.</li>
            <li>Publicás los recursos para las personas inscriptas.</li>
            <li>Hacés el seguimiento de la actividad.</li>
          </ul>
        </div>
      </div>
<?php acc('Cómo acceder a los cursos', 'todos', true); ?>
        <p>Los cursos están disponibles desde su propio acceso en la barra superior de Nube InSSA<?= url_modulo('cursos') !== '' ? ' o con el botón <b>Abrir</b> de esta sección' : '' ?>. Vas a ver los cursos que tenés disponibles según tu usuario.</p>
<?php acc_fin(); ?>
<?php acc('Materiales y documentos del curso'); ?>
        <p>Cada curso reúne sus materiales: documentos, presentaciones, lecturas y otros recursos. Podés abrirlos en el navegador y, si está permitido, descargarlos.</p>
        <p>Los documentos que se abren desde Nube InSSA se ven con el mismo visor y editor que el resto de la plataforma.</p>
<?php acc_fin(); ?>
<?php acc('Organización y publicación de contenidos', 'admin'); ?>
        <p>Quienes administran cursos cargan los materiales y los organizan por curso, unidad o tema. Mientras un material está en preparación queda como un archivo guardado; se vuelve visible para las personas del curso cuando se publica. Mirá la diferencia en <a href="#publicacion">Carga y publicación de contenidos</a>.</p>
        <?php tip('Antes de publicar un material, revisá que el archivo sea la versión final y que el nombre sea claro para quien lo va a buscar.'); ?>
<?php acc_fin(); ?>
<?php acc('Usuarios, actividades y seguimiento', 'admin'); ?>
        <p>El acceso a cada curso depende de los usuarios y grupos de Nube InSSA. La administración define quién participa de cada uno. Para conocer el movimiento de los materiales se puede recurrir a la <a href="#actividad">actividad</a> y a las <a href="#estadisticas">estadísticas</a> disponibles.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (isset($SECCIONES['editor'])): ?>
<!-- ================================================================== -->
<!-- EDITOR DE SITIO                                                    -->
<!-- ================================================================== -->
<?php seccion_abrir('editor'); ?>
      <p class="intro">El Editor de sitio es la herramienta para mantener actualizada la información institucional que se muestra al público. Lo usan las personas responsables de comunicación y administración, con permiso de edición.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('editor') ?>Editar contenidos</h3><p>Modificar textos e información institucional.</p></div>
        <div class="tarjeta"><h3><?= svg('abrir') ?>Enlaces</h3><p>Agregar, corregir o quitar enlaces.</p></div>
        <div class="tarjeta"><h3><?= svg('publicar') ?>Novedades</h3><p>Publicar noticias y avisos.</p></div>
      </div>
<?php acc('Para qué sirve', 'admin', true); ?>
        <p>Desde el Editor de sitio se gestionan los contenidos del sitio: la información institucional, los enlaces y las novedades. Los cambios que se guardan ahí se ven en el sitio sin necesidad de conocimientos técnicos.</p>
<?php acc_fin(); ?>
<?php acc('Buenas prácticas al editar', 'admin'); ?>
        <ul>
          <li>Revisá el texto antes de publicarlo: lo que se publica lo ve cualquier persona.</li>
          <li>Probá cada enlace nuevo para confirmar que abre la página correcta.</li>
          <li>Mantené las novedades al día y quitá las que ya no corresponden.</li>
          <li>Si tenés dudas sobre un cambio, consultalo antes con la persona responsable del área.</li>
        </ul>
<?php acc_fin(); ?>
<?php acc('¿No ves el Editor de sitio?', 'todos'); ?>
        <p>Es normal. Sólo lo ven las personas con permiso para editar el sitio. Si necesitás que se publique o corrija algo, pedíselo al área responsable.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (isset($SECCIONES['publicacion'])): ?>
<!-- ================================================================== -->
<!-- CARGA Y PUBLICACIÓN                                                -->
<!-- ================================================================== -->
<?php seccion_abrir('publicacion'); ?>
      <p class="intro">En Nube InSSA se cargan documentos, libros, artículos, publicaciones, materiales educativos, novedades y recursos institucionales. Antes de empezar conviene tener clara una diferencia.</p>
      <div class="dos-columnas">
        <div class="columna columna-user">
          <h3><?= svg('carpeta', 'ico ico-rol') ?>Guardar un archivo</h3>
          <ul>
            <li>El archivo queda en tu cuenta, en Archivos.</li>
            <li>Es privado: sólo lo ves vos.</li>
            <li>Lo ven otras personas sólo si lo compartís.</li>
            <li>Ideal para borradores y documentos de trabajo.</li>
          </ul>
        </div>
        <div class="columna columna-admin">
          <h3><?= svg('publicar', 'ico ico-rol') ?>Publicar un contenido</h3>
          <ul>
            <li>El contenido pasa a estar disponible para su público: un curso, el personal o el sitio.</li>
            <li>Lo hace una persona con permiso de publicación.</li>
            <li>Tiene que ser la versión final y revisada.</li>
          </ul>
        </div>
      </div>
<?php acc('Cargar un contenido', 'todos', true); ?>
        <p>Todo contenido empieza como un archivo. Subilo a <a href="#archivos">Archivos</a> en la carpeta que corresponda, con un nombre claro. Mientras lo preparás, podés compartirlo con quien lo tenga que revisar.</p>
<?php acc_fin(); ?>
<?php acc('Publicar', 'admin'); ?>
        <p>Cuando el contenido está listo, la persona responsable lo publica desde la herramienta correspondiente: el módulo de <a href="#cursos">cursos</a> para materiales educativos o el <a href="#editor">Editor de sitio</a> para novedades e información institucional.</p>
        <?php tip('Publicar hace visible el contenido para mucha gente. Controlá ortografía, datos y permisos antes de hacerlo.', 'alerta'); ?>
<?php acc_fin(); ?>
<?php acc('Consejos para nombrar y ordenar'); ?>
        <ul>
          <li>Usá nombres que describan el contenido: "Protocolo de atención 2026.pdf" en lugar de "doc final (2).pdf".</li>
          <li>Guardá todo lo de un mismo tema en una misma carpeta.</li>
          <li>Preferí PDF para lo que sólo se tiene que leer, y formatos editables para lo que se va a modificar.</li>
        </ul>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<?php if (isset($SECCIONES['estadisticas'])): ?>
<!-- ================================================================== -->
<!-- ESTADÍSTICAS                                                       -->
<!-- ================================================================== -->
<?php seccion_abrir('estadisticas'); ?>
      <p class="intro">Nube InSSA ofrece información sobre cómo se usa la plataforma. Lo que ves depende de tu perfil: algunas estadísticas son personales y otras son exclusivas de la administración.</p>
<?php acc('Tu actividad', 'user', true); ?>
        <p>Tu registro personal de movimientos está en <a href="#actividad">Actividad</a>: qué archivos se crearon, modificaron o compartieron, y quién lo hizo. En Archivos ves además cuánto espacio estás usando.</p>
<?php acc_fin(); ?>
<?php if (modulo_activo('estadisticas')): ?>
<?php acc('Estadísticas institucionales'); ?>
        <p>El módulo de estadísticas de Nube InSSA reúne indicadores sobre el uso de los recursos institucionales, como el movimiento de documentos y materiales. Los datos que muestra y quién puede consultarlos los define la administración.</p>
<?php if (url_modulo('estadisticas') !== ''): ?>
        <p>Podés entrar con el botón <b>Abrir</b> de esta sección.</p>
<?php endif; ?>
<?php acc_fin(); ?>
<?php endif; ?>
<?php if (app('analytics')): ?>
<?php acc('Informes y gráficos'); ?>
        <p>La aplicación de análisis permite armar informes con tablas y gráficos a partir de datos cargados en la plataforma, por ejemplo desde una planilla guardada en Archivos. Los informes se pueden compartir con otras personas.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php if (app('serverinfo')): ?>
<?php acc('Información del sistema', 'admin'); ?>
        <p>En la configuración de administración hay una página de información del sistema. Muestra datos generales de uso: cantidad de cuentas, cuentas activas en distintos períodos, cantidad de archivos, espacio ocupado, recursos compartidos y el estado del servidor.</p>
        <p>Son datos globales de la plataforma y sólo los ve la administración.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php acc('Actividad de los recursos compartidos', 'todos'); ?>
        <p>Para saber qué pasó con un documento que compartiste (si se modificó, quién lo cambió o si se volvió a compartir) mirá la pestaña <b>Actividad</b> de ese archivo.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>
<?php endif; ?>

<!-- ================================================================== -->
<!-- USUARIOS Y GRUPOS                                                  -->
<!-- ================================================================== -->
<?php seccion_abrir('usuarios'); ?>
      <p class="intro">Cada persona tiene su propia cuenta en Nube InSSA. Las cuentas se agrupan por área, sector o función, para que sea fácil compartir con todo un equipo.</p>
      <div class="dos-columnas">
        <div class="columna columna-user" data-rol="user">
          <h3><?= svg('persona', 'ico ico-rol') ?>Lo que podés hacer como usuario</h3>
          <ul>
            <li>Ver a qué grupos pertenecés, en tu información personal.</li>
            <li>Compartir archivos, carpetas y calendarios con un grupo entero.</li>
            <li>Encontrar a otras personas al compartir, escribiendo su nombre.</li>
          </ul>
        </div>
        <div class="columna columna-admin" data-rol="admin">
          <h3><?= svg('llave', 'ico ico-rol') ?>Lo que hace la administración</h3>
          <ul>
            <li>Crear, modificar y desactivar cuentas.</li>
            <li>Crear grupos y definir quién pertenece a cada uno.</li>
            <li>Asignar el espacio de almacenamiento de cada cuenta.</li>
            <li>Designar administradores de grupo.</li>
          </ul>
        </div>
      </div>
<?php acc('Qué es un grupo', 'todos', true); ?>
        <p>Un grupo es un conjunto de cuentas. Cuando compartís algo con un grupo, todas las personas que lo integran reciben acceso, y quienes se sumen más adelante también.</p>
        <div class="ejemplo"><b>Ejemplo:</b> en lugar de compartir un protocolo con quince personas una por una, lo compartís una sola vez con el grupo del área.</div>
<?php acc_fin(); ?>
<?php acc('¿Cómo me agregan o me sacan de un grupo?'); ?>
        <p>Vos no podés sumarte a un grupo por tu cuenta. Si necesitás estar en un grupo, o ya no deberías estar en uno, pedíselo a la administración.</p>
<?php acc_fin(); ?>
<?php acc('Gestión de cuentas', 'admin'); ?>
        <p>Desde la página de cuentas de la configuración de administración se crean las cuentas, se asignan los grupos y el espacio disponible, se restablecen contraseñas y se desactivan las cuentas que ya no se usan.</p>
        <p>Desactivar una cuenta le impide ingresar, pero conserva sus archivos. Eliminarla borra sus datos.</p>
        <?php tip('Eliminar una cuenta borra sus archivos y deja de compartirlos con el resto. Antes de hacerlo, asegurate de transferir o respaldar lo que haga falta.', 'alerta'); ?>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- CONFIGURACIÓN                                                      -->
<!-- ================================================================== -->
<?php seccion_abrir('configuracion'); ?>
      <p class="intro">En tu configuración personal ajustás tus datos y cómo funciona Nube InSSA para vos. Entrás tocando tu foto o tus iniciales, arriba a la derecha, y eligiendo la opción de configuración.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('persona') ?>Información personal</h3><p>Nombre, foto, correo, teléfono e idioma.</p></div>
        <div class="tarjeta"><h3><?= svg('escudo') ?>Seguridad</h3><p>Contraseña, sesiones y dispositivos conectados.</p></div>
        <div class="tarjeta"><h3><?= svg('campana') ?>Notificaciones</h3><p>Qué avisos recibís y por dónde.</p></div>
        <div class="tarjeta"><h3><?= svg('compartir') ?>Compartir</h3><p>Preferencias al recibir archivos compartidos.</p></div>
      </div>
<?php acc('Información personal y fotografía', 'user', true); ?>
        <p>Completá tu nombre completo, tu correo y, si querés, otros datos como teléfono u organización. También podés subir una foto de perfil: se muestra en la barra superior, en los comentarios y cuando compartís.</p>
        <p>Algunos datos pueden venir cargados por la administración y no se pueden cambiar desde tu cuenta.</p>
        <p>Al lado de algunos campos hay un selector de privacidad que define quién puede ver ese dato.</p>
<?php acc_fin(); ?>
<?php acc('Idioma, configuración regional y zona horaria', 'user'); ?>
        <p>En tu información personal elegís el idioma de la plataforma y la configuración regional, que define el formato de fechas y el primer día de la semana.</p>
        <p>La zona horaria se toma de tu navegador.<?= app('calendar') ? ' En la configuración de Calendario podés elegir otra zona horaria para tus eventos.' : '' ?></p>
<?php acc_fin(); ?>
<?php acc('Notificaciones y actividad', 'user'); ?>
        <p>Elegí qué avisos querés recibir en la plataforma y cuáles también por correo, y cada cuánto querés recibir los resúmenes de actividad.</p>
<?php acc_fin(); ?>
<?php acc('Seguridad, sesiones y dispositivos', 'user'); ?>
        <p>En la sección de seguridad podés:</p>
        <ul>
          <li>Cambiar tu contraseña.</li>
          <li>Ver los <b>dispositivos y sesiones</b> conectados a tu cuenta, y cerrar los que no reconozcas.</li>
          <li>Crear <b>contraseñas de aplicación</b> para conectar programas o dispositivos sin usar tu contraseña principal.</li>
<?php if (app('twofactor_totp')): ?>
          <li>Activar la <b>verificación en dos pasos</b>.</li>
<?php endif; ?>
        </ul>
        <p>Más detalles en <a href="#seguridad">Privacidad y seguridad</a>.</p>
<?php acc_fin(); ?>
<?php if (app('privacy')): ?>
<?php acc('Privacidad', 'user'); ?>
        <p>La sección de privacidad explica quién tiene acceso a tus datos dentro de la plataforma y dónde se almacenan.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php if (app('theming')): ?>
<?php acc('Apariencia y accesibilidad', 'user'); ?>
        <p>En la sección de apariencia y accesibilidad podés elegir un tema claro u oscuro, activar el alto contraste o usar una tipografía más fácil de leer, según las opciones que ofrezca la plataforma.</p>
<?php acc_fin(); ?>
<?php endif; ?>
<?php acc('Aplicaciones y almacenamiento', 'user'); ?>
        <p>Las aplicaciones disponibles para tu cuenta las define la administración. Si necesitás alguna que no ves, consultalo.</p>
        <p>El espacio que usás se muestra al pie del menú de Archivos y en tu información personal.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- PRIVACIDAD Y SEGURIDAD                                             -->
<!-- ================================================================== -->
<?php seccion_abrir('seguridad'); ?>
      <p class="intro">Nube InSSA está pensada para guardar y gestionar la información de la institución de forma segura. Una parte de esa seguridad la cuida la plataforma y otra depende de cómo la usamos cada una de las personas.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('candado') ?>Conexión protegida</h3><p>Se accede por una conexión segura (HTTPS). Fijate que la dirección empiece con <code>https://</code>.</p></div>
        <div class="tarjeta"><h3><?= svg('persona') ?>Cuentas personales</h3><p>Cada persona entra con su propio usuario. Nadie ve tus archivos salvo que los compartas.</p></div>
        <div class="tarjeta"><h3><?= svg('compartir') ?>Control de acceso</h3><p>Vos decidís quién ve cada cosa y con qué permisos, y podés quitar el acceso cuando quieras.</p></div>
        <div class="tarjeta"><h3><?= svg('historial') ?>Registro</h3><p>La actividad queda registrada y podés revisarla.</p></div>
      </div>
<?php acc('Tu contraseña', 'todos', true); ?>
        <ul>
          <li>Usá una contraseña larga y que no uses en otros sitios.</li>
          <li>No la compartas con nadie, ni siquiera con compañeros de trabajo.</li>
          <li>Si sospechás que alguien la conoce, cambiala enseguida desde tu configuración de seguridad.</li>
        </ul>
<?php if (app('password_policy')): ?>
        <p>La plataforma aplica reglas mínimas para las contraseñas, definidas por la administración. Si una contraseña no se acepta, es porque no cumple alguna de esas reglas.</p>
<?php endif; ?>
<?php acc_fin(); ?>
<?php if (app('twofactor_totp') || app('twofactor_backupcodes')): ?>
<?php acc('Verificación en dos pasos'); ?>
<?php if (app('twofactor_totp')): ?>
        <p>La verificación en dos pasos agrega una segunda comprobación al ingresar: además de la contraseña, se pide un código temporal que genera una aplicación de autenticación en tu teléfono. Así, aunque alguien conozca tu contraseña, no puede entrar.</p>
        <p>Se activa desde la sección de seguridad de tu configuración, si la administración lo habilitó.</p>
<?php endif; ?>
<?php if (app('twofactor_backupcodes')): ?>
        <p>Cuando la actives, generá también los <b>códigos de respaldo</b> y guardalos en un lugar seguro. Te sirven para entrar si perdés el teléfono.</p>
<?php endif; ?>
<?php acc_fin(); ?>
<?php endif; ?>
<?php acc('Sesiones y dispositivos conectados'); ?>
        <p>En tu configuración de seguridad ves la lista de navegadores y dispositivos que tienen una sesión abierta con tu cuenta. Si aparece uno que no reconocés, cerralo desde ahí y cambiá tu contraseña.</p>
<?php acc_fin(); ?>
<?php acc('Permisos y enlaces compartidos'); ?>
        <p>Compartir es muy práctico, pero cada enlace es una puerta abierta. Algunas recomendaciones:</p>
        <ul>
          <li>Compartí con personas o grupos siempre que puedas, en lugar de usar enlaces públicos.</li>
          <li>Si usás un enlace, ponele contraseña y fecha de vencimiento.</li>
          <li>Dá permiso de edición sólo a quien lo necesite.</li>
          <li>Revisá cada tanto la sección <b>Compartidos</b> y quitá lo que ya no haga falta.</li>
        </ul>
<?php acc_fin(); ?>
<?php acc('Cifrado'); ?>
        <p>La información viaja cifrada entre tu dispositivo y Nube InSSA gracias a la conexión segura (HTTPS).</p>
<?php if (app('encryption')): ?>
        <p>La plataforma cuenta además con un módulo de cifrado de los archivos almacenados. Si está activado y cómo se aplica lo decide la administración; si necesitás saberlo para un caso puntual, consultalo.</p>
<?php endif; ?>
<?php if (app('end_to_end_encryption')): ?>
        <p>También dispone de cifrado de extremo a extremo para carpetas, que se usa desde aplicaciones compatibles. Consultá a la administración si lo necesitás.</p>
<?php endif; ?>
        <?php tip('Para información especialmente delicada (datos de salud, documentos personales), además de guardarla en Nube InSSA, limitá al mínimo con quién la compartís.', 'info'); ?>
<?php acc_fin(); ?>
<?php acc('Buenas prácticas'); ?>
        <ul>
          <li>Cerrá la sesión en computadoras compartidas.</li>
          <li>Desconfiá de correos que te pidan tu contraseña: nadie de la institución te la va a pedir.</li>
          <li>Mantené actualizado tu navegador.</li>
          <li>Guardá la información institucional en Nube InSSA y no en pendrives o cuentas personales.</li>
        </ul>
<?php acc_fin(); ?>
<?php acc('Seguridad de la plataforma', 'admin'); ?>
        <p>La administración gestiona desde la configuración la política de contraseñas, las reglas de compartición, la verificación en dos pasos y los registros de actividad. La página de información general de la administración muestra además advertencias de seguridad y configuración cuando detecta algo para revisar.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- CELULARES Y TABLETS                                                -->
<!-- ================================================================== -->
<?php seccion_abrir('moviles'); ?>
      <p class="intro">Podés usar Nube InSSA desde el teléfono o la tablet. La forma más simple es entrar desde el navegador: la plataforma se adapta sola al tamaño de la pantalla.</p>
<?php acc('Desde el navegador del celular', 'todos', true); ?>
        <ol>
          <li>Abrí el navegador del teléfono y entrá a <code><?= $HOST_NUBE ?></code>.</li>
          <li>Ingresá con tu usuario y contraseña.</li>
          <li>Tocá el botón de tres rayas para ver el menú de cada aplicación.</li>
        </ol>
        <p>Desde el celular podés ver y subir archivos, abrir documentos, mirar tus fotos<?= app('calendar') ? ', consultar el calendario' : '' ?><?= app('mail') ? ', leer el correo' : '' ?> y compartir.</p>
        <?php tip('Agregá Nube InSSA a la pantalla de inicio del teléfono desde el menú del navegador. Así la abrís con un toque, como una aplicación.'); ?>
<?php acc_fin(); ?>
<?php acc('Con aplicaciones compatibles'); ?>
        <p>Nube InSSA usa estándares abiertos para sincronizar información:</p>
        <ul>
          <li><b>Archivos:</b> WebDAV.</li>
<?php if (app('calendar')): ?>
          <li><b>Calendarios:</b> CalDAV.</li>
<?php endif; ?>
<?php if (app('contacts')): ?>
          <li><b>Contactos:</b> CardDAV.</li>
<?php endif; ?>
        </ul>
        <p>Cualquier aplicación compatible con estos estándares puede conectarse. La dirección de conexión es:</p>
        <p><code><?= $HOST_NUBE ?>/remote.php/dav</code></p>
        <p>Consultá con la administración qué aplicaciones se recomiendan en la institución.</p>
        <?php tip('Para conectar una aplicación, lo más seguro es crear una contraseña de aplicación en tu configuración de seguridad, en lugar de usar tu contraseña principal. Si perdés el teléfono, revocás sólo esa contraseña.', 'info'); ?>
<?php acc_fin(); ?>
<?php acc('Si perdés el celular'); ?>
        <p>Entrá a Nube InSSA desde otra computadora, andá a tu configuración de seguridad y cerrá la sesión de ese dispositivo. Después cambiá tu contraseña.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

<!-- ================================================================== -->
<!-- ADMINISTRACIÓN                                                     -->
<!-- ================================================================== -->
<?php seccion_abrir('administracion'); ?>
      <?php tip('Esta sección describe funciones <b>exclusivas de las cuentas administradoras</b>. Si no sos administrador, estas opciones no te aparecen y los accesos de esta sección no te van a funcionar.', 'info'); ?>
      <p class="intro">La administración de Nube InSSA se hace desde la configuración, en las páginas de administración. Se entra tocando la foto de perfil, arriba a la derecha.</p>
      <div class="tarjetas">
        <div class="tarjeta"><h3><?= svg('grupo') ?>Cuentas y grupos</h3><p>Alta, baja y organización del personal.</p></div>
        <div class="tarjeta"><h3><?= svg('lista') ?>Aplicaciones</h3><p>Qué herramientas están disponibles.</p></div>
        <div class="tarjeta"><h3><?= svg('compartir') ?>Compartición</h3><p>Reglas para compartir y usar enlaces.</p></div>
        <div class="tarjeta"><h3><?= svg('escudo') ?>Seguridad</h3><p>Contraseñas, dos pasos y advertencias.</p></div>
      </div>
<?php acc('Gestión de cuentas y grupos', 'admin', true); ?>
        <p>En la página de cuentas se crean y editan usuarios, se asignan grupos, se fija el espacio disponible de cada cuenta y se desactivan las cuentas que ya no se usan.</p>
        <p><a class="boton" href="<?= $HOST_NUBE ?>/index.php/settings/users" target="_top"><?= svg('abrir') ?>Abrir cuentas</a></p>
        <p>También se pueden designar <b>administradores de grupo</b>: personas que gestionan las cuentas de su grupo sin tener acceso a toda la administración.</p>
<?php acc_fin(); ?>
<?php acc('Aplicaciones', 'admin'); ?>
        <p>Desde la página de aplicaciones se ven las aplicaciones instaladas, se activan o desactivan, y se puede limitar una aplicación a ciertos grupos.</p>
        <?php tip('Desactivar una aplicación la quita a todas las cuentas. Avisá antes al personal si la estaban usando.', 'alerta'); ?>
<?php acc_fin(); ?>
<?php acc('Configuración general y compartición', 'admin'); ?>
        <p>La configuración de administración está organizada en secciones. Entre ellas:</p>
        <ul>
          <li><b>Información general:</b> estado de la plataforma y advertencias de configuración o seguridad.</li>
          <li><b>Configuración básica:</b> servidor de correo para los envíos de la plataforma, tareas en segundo plano y otros ajustes generales.</li>
          <li><b>Compartir:</b> si se permiten enlaces públicos, si requieren contraseña o vencimiento, y con quién se puede compartir.</li>
          <li><b>Seguridad:</b> política de contraseñas y verificación en dos pasos.</li>
<?php if (app('theming')): ?>
          <li><b>Apariencia:</b> nombre, logotipo y colores institucionales.</li>
<?php endif; ?>
<?php if (app('serverinfo')): ?>
          <li><b>Información del sistema:</b> uso de la plataforma y estado del servidor. Mirá <a href="#estadisticas">Estadísticas</a>.</li>
<?php endif; ?>
        </ul>
        <p><a class="boton" href="<?= $HOST_NUBE ?>/index.php/settings/admin" target="_top"><?= svg('abrir') ?>Abrir la administración</a></p>
<?php acc_fin(); ?>
<?php acc('Almacenamiento', 'admin'); ?>
        <p>El espacio de cada cuenta se define en la página de cuentas. La papelera y las versiones de archivos también ocupan espacio y se limpian solas según la configuración de la plataforma.</p>
<?php acc_fin(); ?>
<?php acc('Actividad, registros y mantenimiento', 'admin'); ?>
        <p>La administración puede consultar la actividad de la plataforma y los registros de funcionamiento desde la configuración. Las tareas de mantenimiento técnico (actualizaciones, copias de seguridad, ajustes del servidor) las realiza el equipo técnico responsable y no se hacen desde esta ayuda.</p>
<?php acc_fin(); ?>
<?php acc('Políticas de uso', 'admin'); ?>
        <p>Conviene que la institución tenga reglas claras y conocidas por todo el personal: qué se guarda en Nube InSSA, cómo se nombran y ordenan las carpetas comunes, cuándo se usan enlaces públicos y cómo se da de baja una cuenta cuando una persona deja la institución.</p>
<?php acc_fin(); ?>
<?php seccion_cerrar(); ?>

      <p class="pie-ayuda">Ayuda de Nube InSSA · <?= h($ANIO) ?> · ¿No encontraste lo que buscabas? Consultá con la administración de la plataforma.</p>
    </main>
  </div>
</div>

<button type="button" class="subir" id="subir" aria-label="Volver arriba"><?= svg('arriba') ?></button>

<script nonce="<?= h($NONCE) ?>">
(function () {
    'use strict';

    var app      = document.getElementById('app');
    var nav      = document.getElementById('app-navigation');
    var toggle   = document.getElementById('navToggle');
    var contenido = document.getElementById('app-content-inner');

    /* ---------- Íconos: si un SVG no carga, usar el dibujo de respaldo ---------- */
    Array.prototype.forEach.call(document.querySelectorAll('img.ico-app'), function (img) {
        function fallar() {
            var resp = img.nextElementSibling;
            if (resp && resp.classList.contains('ico-respaldo') && resp.firstElementChild) {
                img.replaceWith(resp.firstElementChild);
                resp.remove();
            } else {
                img.remove();
            }
        }
        if (img.complete && img.naturalWidth === 0) { fallar(); }
        else { img.addEventListener('error', fallar); }
    });

    /* ---------- Menú lateral ---------- */

    function esMobile() { return window.innerWidth <= 1024; }

    function actualizarIcono() {
        var abierto = esMobile()
            ? nav.classList.contains('mobile-open')
            : !app.classList.contains('sidebar-collapsed');
        toggle.classList.toggle('cerrado', !abierto);
        toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
    }

    function alternarMenu() {
        if (esMobile()) {
            nav.classList.toggle('mobile-open');
            app.classList.toggle('nav-abierto', nav.classList.contains('mobile-open'));
            app.classList.remove('sidebar-collapsed');
        } else {
            app.classList.toggle('sidebar-collapsed');
        }
        actualizarIcono();
    }

    function cerrarMenuMovil() {
        if (esMobile() && nav.classList.contains('mobile-open')) { alternarMenu(); }
    }

    toggle.addEventListener('click', alternarMenu);

    // En pantalla chica, tocar el contenido con el menú abierto sólo lo cierra.
    document.getElementById('app-content').addEventListener('click', function (evento) {
        if (!esMobile() || !nav.classList.contains('mobile-open')) return;
        if (toggle.contains(evento.target)) return;
        evento.preventDefault();
        evento.stopPropagation();
        alternarMenu();
    }, true);

    app.classList.remove('sidebar-collapsed', 'nav-abierto');
    nav.classList.remove('mobile-open');
    actualizarIcono();

    window.addEventListener('resize', function () {
        if (!esMobile()) {
            nav.classList.remove('mobile-open');
            app.classList.remove('nav-abierto');
        } else {
            app.classList.remove('sidebar-collapsed');
        }
        actualizarIcono();
    });

    /* ---------- Enlaces internos ---------- */

    var bloqueoSpy = 0;
    function irA(id) {
        var destino = document.getElementById(id);
        if (!destino) return;
        marcarActivo(id);
        bloqueoSpy = Date.now() + 900;
        destino.scrollIntoView({ block: 'start' });
        if (history.replaceState) { history.replaceState(null, '', '#' + id); }
    }

    document.addEventListener('click', function (e) {
        var a = e.target.closest ? e.target.closest('a[href^="#"]') : null;
        if (!a) return;
        var id = a.getAttribute('href').slice(1);
        if (!id || !document.getElementById(id)) return;
        e.preventDefault();
        if (nav.contains(a)) { cerrarMenuMovil(); }
        irA(id);
    });

    if (location.hash.length > 1) {
        setTimeout(function () { irA(decodeURIComponent(location.hash.slice(1))); }, 50);
    }

    /* ---------- Resaltar la sección visible en el menú ---------- */

    var itemsNav = Array.prototype.slice.call(nav.querySelectorAll('.app-navigation__body .list-item__wrapper'));
    function marcarActivo(id) {
        itemsNav.forEach(function (li) {
            li.classList.toggle('active', li.getAttribute('data-destino') === id);
        });
    }

    var bloques = Array.prototype.slice.call(document.querySelectorAll('#inicio, .seccion'));
    function seccionVisible() {
        if (Date.now() < bloqueoSpy) return;
        var tope = contenido.getBoundingClientRect().top + 90;
        var actual = 'inicio';
        bloques.forEach(function (b) {
            if (b.offsetParent === null) return;
            if (b.getBoundingClientRect().top <= tope) { actual = b.id; }
        });
        marcarActivo(actual);
    }

    var boton = document.getElementById('subir');
    var pendiente = false;
    contenido.addEventListener('scroll', function () {
        if (pendiente) return;
        pendiente = true;
        requestAnimationFrame(function () {
            pendiente = false;
            seccionVisible();
            boton.classList.toggle('visible', contenido.scrollTop > 500);
        });
    }, { passive: true });

    boton.addEventListener('click', function () {
        contenido.scrollTo({ top: 0 });
        if (history.replaceState) { history.replaceState(null, '', location.pathname + location.search); }
    });

    /* ---------- Filtro por perfil (Todos / Usuarios / Administradores) ---------- */

    var botonesVista = Array.prototype.slice.call(document.querySelectorAll('.filtro-rol button'));
    function ponerVista(vista) {
        document.body.setAttribute('data-vista', vista);
        botonesVista.forEach(function (b) {
            b.setAttribute('aria-pressed', b.getAttribute('data-vista') === vista ? 'true' : 'false');
        });
        try { localStorage.setItem('ayudaInssaVista', vista); } catch (e) {}
        buscar();
    }
    botonesVista.forEach(function (b) {
        b.addEventListener('click', function () { ponerVista(b.getAttribute('data-vista')); });
    });

    /* ---------- Buscador de la ayuda ---------- */

    var campo      = document.getElementById('buscar');
    var campoNav   = document.getElementById('buscarNav');
    var limpiar    = document.getElementById('limpiar');
    var resultado  = document.getElementById('resultado');
    var sinRes     = document.getElementById('sinResultados');
    var accesos    = document.getElementById('bloqueAccesos');
    var secciones  = Array.prototype.slice.call(document.querySelectorAll('.seccion'));

    function normalizar(t) {
        return (t || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    // Cada letra se busca con y sin tilde.
    var VARIANTES = { a: 'aáàâä', e: 'eéèêë', i: 'iíìîï', o: 'oóòôö', u: 'uúùûü', n: 'nñ', c: 'cç' };
    function patron(termino) {
        return termino.split('').map(function (ch) {
            if (VARIANTES[ch]) return '[' + VARIANTES[ch] + ']';
            return ch.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }).join('');
    }

    function visiblePorVista(el) {
        var vista = document.body.getAttribute('data-vista');
        if (vista === 'todos') return true;
        var r = el.closest('[data-rol]');
        while (r) {
            var rol = r.getAttribute('data-rol');
            if ((vista === 'user' && rol === 'admin') || (vista === 'admin' && rol === 'user')) return false;
            r = r.parentElement ? r.parentElement.closest('[data-rol]') : null;
        }
        return true;
    }

    // Guarda el texto de cada sección y de cada acordeón una sola vez.
    secciones.forEach(function (s) {
        s._cabecera = normalizar(s.querySelector('.seccion-cabecera').textContent + ' ' + (s.getAttribute('data-claves') || ''));
        s._acc = Array.prototype.slice.call(s.querySelectorAll('.acc')).map(function (d) {
            return { el: d, texto: normalizar(d.textContent) };
        });
        s._resto = Array.prototype.slice.call(s.querySelectorAll('.intro, .tarjetas, .dos-columnas, .accesos, .nota'))
            .filter(function (el) { return !el.closest('.acc'); })
            .map(function (el) { return { el: el, texto: normalizar(el.textContent) }; });
    });

    function quitarMarcas() {
        Array.prototype.forEach.call(document.querySelectorAll('mark[data-busqueda]'), function (m) {
            var padre = m.parentNode;
            padre.replaceChild(document.createTextNode(m.textContent), m);
            padre.normalize();
        });
    }

    function marcar(raiz, regex) {
        var caminante = document.createTreeWalker(raiz, NodeFilter.SHOW_TEXT, {
            acceptNode: function (n) {
                if (!n.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
                var p = n.parentNode;
                if (!p || p.closest('svg, script, style, mark, .acc-flecha')) return NodeFilter.FILTER_REJECT;
                return NodeFilter.FILTER_ACCEPT;
            }
        });
        var nodos = [];
        while (caminante.nextNode()) nodos.push(caminante.currentNode);
        nodos.forEach(function (n) {
            regex.lastIndex = 0;
            if (!regex.test(n.nodeValue)) return;
            regex.lastIndex = 0;
            var frag = document.createDocumentFragment();
            var texto = n.nodeValue, ultimo = 0, m;
            while ((m = regex.exec(texto)) !== null) {
                if (m[0] === '') { regex.lastIndex++; continue; }
                frag.appendChild(document.createTextNode(texto.slice(ultimo, m.index)));
                var mk = document.createElement('mark');
                mk.setAttribute('data-busqueda', '');
                mk.textContent = m[0];
                frag.appendChild(mk);
                ultimo = m.index + m[0].length;
            }
            frag.appendChild(document.createTextNode(texto.slice(ultimo)));
            n.parentNode.replaceChild(frag, n);
        });
    }

    function contador(id, n) {
        var li = nav.querySelector('.list-item__wrapper[data-destino="' + id + '"]');
        if (!li) return;
        var c = li.querySelector('.counter-bubble__counter');
        if (c) { c.hidden = n === null; c.textContent = n === null ? '' : String(n); }
        li.hidden = n === 0;
    }

    function buscar() {
        var q = campo.value.trim();
        limpiar.hidden = q === '';
        quitarMarcas();

        var terminos = normalizar(q).split(/\s+/).filter(function (t) { return t.length >= 2; });

        if (!terminos.length) {
            secciones.forEach(function (s) {
                s.hidden = false;
                s._acc.forEach(function (a) {
                    a.el.hidden = false;
                    a.el.open = a.el.hasAttribute('data-abierto');
                });
                s._resto.forEach(function (r) { r.el.hidden = false; });
                contador(s.id, null);
            });
            accesos.hidden = false;
            resultado.hidden = true;
            sinRes.hidden = true;
            return;
        }

        function coincide(texto) {
            return terminos.every(function (t) { return texto.indexOf(t) !== -1; });
        }

        var total = 0, seccionesConResultado = 0;

        secciones.forEach(function (s) {
            if (!visiblePorVista(s)) { contador(s.id, 0); return; }
            var enCabecera = coincide(s._cabecera);
            var accOk = s._acc.filter(function (a) { return visiblePorVista(a.el) && coincide(a.texto); });
            var restoOk = s._resto.filter(function (r) { return coincide(r.texto); });

            var mostrar = enCabecera || accOk.length > 0 || restoOk.length > 0;
            s.hidden = !mostrar;

            s._acc.forEach(function (a) {
                var ok = accOk.indexOf(a) !== -1;
                a.el.hidden = !(ok || enCabecera);
                a.el.open = ok;
            });
            s._resto.forEach(function (r) {
                r.el.hidden = !(enCabecera || restoOk.indexOf(r) !== -1 || accOk.length === 0);
            });

            var n = mostrar ? Math.max(accOk.length, 1) : 0;
            contador(s.id, n);
            if (mostrar) { total += n; seccionesConResultado++; }
        });

        var regex = new RegExp(terminos.map(patron).join('|'), 'gi');
        secciones.forEach(function (s) { if (!s.hidden) marcar(s, regex); });

        accesos.hidden = true;
        sinRes.hidden = total > 0;
        resultado.hidden = total === 0;
        if (total > 0) {
            resultado.textContent = total === 1
                ? 'Encontramos 1 resultado.'
                : 'Encontramos ' + total + ' resultados en ' + seccionesConResultado + (seccionesConResultado === 1 ? ' sección.' : ' secciones.');
        }
    }

    var espera = null;
    function alEscribir(origen, otro) {
        otro.value = origen.value;
        clearTimeout(espera);
        espera = setTimeout(function () {
            buscar();
            contenido.scrollTop = 0;
        }, 150);
    }
    campo.addEventListener('input', function () { alEscribir(campo, campoNav); });
    campoNav.addEventListener('input', function () { alEscribir(campoNav, campo); });

    [campo, campoNav].forEach(function (c) {
        c.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { c.value = ''; alEscribir(c, c === campo ? campoNav : campo); }
            if (e.key === 'Enter') {
                e.preventDefault();
                var primera = secciones.filter(function (s) { return !s.hidden; })[0];
                if (primera) { cerrarMenuMovil(); irA(primera.id); }
            }
        });
    });

    limpiar.addEventListener('click', function () {
        campo.value = '';
        campoNav.value = '';
        buscar();
        campo.focus();
    });

    // Atajo: la tecla "/" enfoca el buscador.
    document.addEventListener('keydown', function (e) {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT') {
            e.preventDefault();
            campo.focus();
        }
    });

    /* ---------- Inicio ---------- */

    var vistaGuardada = null;
    try { vistaGuardada = localStorage.getItem('ayudaInssaVista'); } catch (e) {}
    if (vistaGuardada === 'user' || vistaGuardada === 'admin') {
        ponerVista(vistaGuardada);
    }
})();
</script>
</body>
</html>
