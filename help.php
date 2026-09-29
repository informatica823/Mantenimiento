<?php
/**
 * Ayuda de Nube InSSA
 *
 * Plantilla de la página de Ayuda (settings/templates/help.php).
 * Se muestra dentro de la interfaz de la nube, con su menú lateral y sus
 * colores. Todo lo que consulta a la plataforma es de solo lectura:
 *   - qué aplicaciones tiene activas la persona,
 *   - los íconos reales de esas aplicaciones (img/app.svg),
 *   - los sitios del menú superior (aplicación de sitios externos),
 *   - si la persona es administradora ($_['admin']).
 * Si algo no se puede consultar, la sección correspondiente no se muestra.
 */

\OC_Util::addStyle('settings', 'help');

/* =========================================================================
 * CONFIGURACIÓN
 * ========================================================================= */

// Módulos propios de InSSA. Si en el menú superior hay un sitio cuyo nombre
// contiene alguna de las 'palabras', la sección enlaza a ese sitio y usa su
// ícono. 'activo' => false oculta la sección.
$AYUDA_MODULOS = [
	'cursos'       => ['activo' => true, 'palabras' => ['curso', 'capacita', 'aula']],
	'editor'       => ['activo' => true, 'palabras' => ['editor', 'sitio web']],
	'publicacion'  => ['activo' => true, 'palabras' => ['public', 'biblioteca', 'libro']],
	'estadisticas' => ['activo' => true, 'palabras' => ['estad']],
];

/* =========================================================================
 * CONSULTAS A LA PLATAFORMA (solo lectura)
 * ========================================================================= */

$ayudaServicio = static function (string $clase) {
	try {
		if (class_exists(\OCP\Server::class)) {
			return \OCP\Server::get($clase);
		}
		return \OC::$server->get($clase);
	} catch (\Throwable $e) {
		return null;
	}
};

$ayudaApps    = $ayudaServicio(\OCP\App\IAppManager::class);
$ayudaUrl     = $ayudaServicio(\OCP\IURLGenerator::class);
$ayudaEsAdmin = !empty($_['admin']);

$ayudaCache = [];
$app = static function (string $id) use ($ayudaApps, &$ayudaCache): bool {
	if (!array_key_exists($id, $ayudaCache)) {
		try {
			$ayudaCache[$id] = $ayudaApps !== null && $ayudaApps->isEnabledForUser($id);
		} catch (\Throwable $e) {
			$ayudaCache[$id] = false;
		}
	}
	return $ayudaCache[$id];
};

$h = static function ($texto): string {
	return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

// Ícono real de una aplicación: el primer archivo que exista de la lista.
$iconoApp = static function (string $id, array $archivos = ['app.svg']) use ($ayudaUrl, $app): string {
	if ($ayudaUrl === null || !$app($id)) {
		return '';
	}
	foreach ($archivos as $archivo) {
		try {
			return $ayudaUrl->imagePath($id, $archivo);
		} catch (\Throwable $e) {
			// No existe: probar el siguiente.
		}
	}
	return '';
};

// Dirección de una aplicación.
$rutaApp = static function (string $id, string $ruta = '') use ($ayudaUrl, $app): string {
	if ($ayudaUrl === null || !$app($id)) {
		return '';
	}
	try {
		return $ruta !== '' ? $ayudaUrl->linkTo('', $ruta) : $ayudaUrl->linkToRoute($id . '.page.index');
	} catch (\Throwable $e) {
		return $ayudaUrl->linkTo('', 'index.php/apps/' . $id . '/');
	}
};

$urlBase = static function (string $ruta) use ($ayudaUrl): string {
	return $ayudaUrl !== null ? $ayudaUrl->linkTo('', $ruta) : '/' . $ruta;
};

/* ---- Sitios del menú superior (aplicación de sitios externos) ---- */

$ayudaSitios = [];
if ($app('external')) {
	try {
		$config  = $ayudaServicio(\OCP\IConfig::class);
		$sesion  = $ayudaServicio(\OCP\IUserSession::class);
		$grupos  = $ayudaServicio(\OCP\IGroupManager::class);
		$usuario = $sesion !== null ? $sesion->getUser() : null;
		$misGrupos = ($usuario !== null && $grupos !== null) ? $grupos->getUserGroupIds($usuario) : [];
		$datos = $config !== null ? json_decode((string) $config->getAppValue('external', 'sites', ''), true) : null;
		if (is_array($datos)) {
			foreach ($datos as $sitio) {
				if (!is_array($sitio) || empty($sitio['name']) || !isset($sitio['id'])) {
					continue;
				}
				if (($sitio['type'] ?? 'link') !== 'link') {
					continue;
				}
				if (!empty($sitio['groups']) && is_array($sitio['groups']) && !array_intersect($sitio['groups'], $misGrupos)) {
					continue;
				}
				$enlace = '';
				try {
					$enlace = !empty($sitio['redirect']) && !empty($sitio['url'])
						? (string) $sitio['url']
						: $ayudaUrl->linkToRoute('external.site.showPage', ['id' => (int) $sitio['id']]);
				} catch (\Throwable $e) {
					$enlace = $urlBase('index.php/apps/external/' . (int) $sitio['id']);
				}
				$icono = '';
				try {
					$icono = !empty($sitio['icon']) && $sitio['icon'] !== 'external.svg'
						? $ayudaUrl->linkToRoute('external.icon.showIcon', ['icon' => (string) $sitio['icon']])
						: $ayudaUrl->imagePath('external', 'external.svg');
				} catch (\Throwable $e) {
					$icono = '';
				}
				$ayudaSitios[] = ['nombre' => (string) $sitio['name'], 'enlace' => $enlace, 'icono' => $icono];
			}
		}
	} catch (\Throwable $e) {
		$ayudaSitios = [];
	}
}

$normalizar = static function (string $t): string {
	$t = mb_strtolower($t, 'UTF-8');
	return strtr($t, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
};

$sitioDeModulo = static function (string $clave) use ($AYUDA_MODULOS, $ayudaSitios, $normalizar): ?array {
	foreach ($ayudaSitios as $sitio) {
		foreach ($AYUDA_MODULOS[$clave]['palabras'] ?? [] as $palabra) {
			if (str_contains($normalizar($sitio['nombre']), $normalizar($palabra))) {
				return $sitio;
			}
		}
	}
	return null;
};

$moduloActivo = static function (string $clave) use ($AYUDA_MODULOS): bool {
	return !empty($AYUDA_MODULOS[$clave]['activo']);
};

/* =========================================================================
 * ÍCONOS
 * Los de las aplicaciones se pintan con el color de la nube usando el SVG
 * real como máscara. Los propios se dibujan en línea.
 * ========================================================================= */

$ICONOS = [
	'pasos'      => 'M14.4,6L14,4H5V21H7V14H12.6L13,16H20V6H14.4Z',
	'carpeta'    => 'M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z',
	'subir'      => 'M9,16V10H5L12,3L19,10H15V16H9M5,20V18H19V20H5Z',
	'bajar'      => 'M5,20H19V18H5M19,9H15V3H9V9H5L12,16L19,9Z',
	'compartir'  => 'M18,16.08C17.24,16.08 16.56,16.38 16.04,16.85L8.91,12.7C8.96,12.47 9,12.24 9,12C9,11.76 8.96,11.53 8.91,11.3L15.96,7.19C16.5,7.69 17.21,8 18,8A3,3 0 0,0 21,5A3,3 0 0,0 18,2A3,3 0 0,0 15,5C15,5.24 15.04,5.47 15.09,5.7L8.04,9.81C7.5,9.31 6.79,9 6,9A3,3 0 0,0 3,12A3,3 0 0,0 6,15C6.79,15 7.5,14.69 8.04,14.19L15.16,18.34C15.11,18.55 15.08,18.77 15.08,19C15.08,20.61 16.39,21.91 18,21.91C19.61,21.91 20.92,20.61 20.92,19A2.92,2.92 0 0,0 18,16.08Z',
	'enlace'     => 'M3.9,12C3.9,10.29 5.29,8.9 7,8.9H11V7H7A5,5 0 0,0 2,12A5,5 0 0,0 7,17H11V15.1H7C5.29,15.1 3.9,13.71 3.9,12M8,13H16V11H8V13M17,7H13V8.9H17C18.71,8.9 20.1,10.29 20.1,12C20.1,13.71 18.71,15.1 17,15.1H13V17H17A5,5 0 0,0 22,12A5,5 0 0,0 17,7Z',
	'papelera'   => 'M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z',
	'restaurar'  => 'M13,3A9,9 0 0,0 4,12H1L4.89,15.89L4.96,16.03L9,12H6A7,7 0 0,1 13,5A7,7 0 0,1 20,12A7,7 0 0,1 13,19C11.07,19 9.32,18.21 8.06,16.94L6.64,18.36C8.27,20 10.5,21 13,21A9,9 0 0,0 22,12A9,9 0 0,0 13,3Z',
	'campana'    => 'M21,19V20H3V19L5,17V11C5,7.9 7.03,5.17 10,4.29C10,4.19 10,4.1 10,4A2,2 0 0,1 12,2A2,2 0 0,1 14,4C14,4.1 14,4.19 14,4.29C16.97,5.17 19,7.9 19,11V17L21,19M14,21A2,2 0 0,1 12,23A2,2 0 0,1 10,21',
	'buscar'     => 'M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z',
	'filtro'     => 'M14,12V19.88C14.04,20.18 13.94,20.5 13.71,20.71C13.32,21.1 12.69,21.1 12.3,20.71L10.29,18.7C10.06,18.47 9.96,18.16 10,17.87V12H9.97L4.21,4.62C3.87,4.19 3.95,3.56 4.38,3.22C4.57,3.08 4.78,3 5,3H19C19.22,3 19.43,3.08 19.62,3.22C20.05,3.56 20.13,4.19 19.79,4.62L14.03,12H14Z',
	'engranaje'  => 'M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.22 21.78,8.95 21.66,8.73L19.66,5.27C19.54,5.05 19.27,4.96 19.050,5.05L16.56,6.05C16.04,5.66 15.5,5.32 14.87,5.07L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,5.07C8.5,5.32 7.96,5.66 7.44,6.05L4.95,5.05C4.73,4.96 4.46,5.05 4.34,5.27L2.34,8.73C2.21,8.95 2.27,9.22 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.78 2.21,15.05 2.34,15.27L4.34,18.73C4.46,18.95 4.73,19.03 4.95,18.95L7.44,17.94C7.96,18.34 8.5,18.68 9.13,18.93L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,18.93C15.5,18.67 16.04,18.34 16.56,17.94L19.05,18.95C19.27,19.03 19.54,18.95 19.66,18.73L21.66,15.27C21.78,15.05 21.73,14.78 21.54,14.63L19.43,12.97Z',
	'escudo'     => 'M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z',
	'candado'    => 'M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z',
	'movil'      => 'M17,19H7V5H17M17,1H7C5.89,1 5,1.89 5,3V21A2,2 0 0,0 7,23H17A2,2 0 0,0 19,21V3C19,1.89 18.1,1 17,1Z',
	'persona'    => 'M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z',
	'grupo'      => 'M12,5.5A3.5,3.5 0 0,1 15.5,9A3.5,3.5 0 0,1 12,12.5A3.5,3.5 0 0,1 8.5,9A3.5,3.5 0 0,1 12,5.5M5,8C5.56,8 6.08,8.15 6.53,8.42C6.38,9.85 6.8,11.27 7.66,12.38C7.16,13.34 6.16,14 5,14A3,3 0 0,1 2,11A3,3 0 0,1 5,8M19,8A3,3 0 0,1 22,11A3,3 0 0,1 19,14C17.84,14 16.84,13.34 16.34,12.38C17.2,11.27 17.62,9.85 17.47,8.42C17.92,8.15 18.44,8 19,8M5.5,18.25C5.5,16.18 8.41,14.5 12,14.5C15.59,14.5 18.5,16.18 18.5,18.25V20H5.5V18.25M0,20V18.5C0,17.11 1.89,15.94 4.45,15.6C3.86,16.28 3.5,17.22 3.5,18.25V20H0M24,20H20.5V18.25C20.5,17.22 20.14,16.28 19.55,15.6C22.11,15.94 24,17.11 24,18.5V20Z',
	'llave'      => 'M22.7,19L13.6,9.9C14.5,7.6 14,4.9 12.1,3C10.1,1 7.1,0.6 4.7,1.7L9,6L6,9L1.6,4.7C0.4,7.1 0.9,10.1 2.9,12.1C4.8,14 7.5,14.5 9.8,13.6L18.9,22.7C19.3,23.1 19.9,23.1 20.3,22.7L22.6,20.4C23.1,20 23.1,19.3 22.7,19Z',
	'curso'      => 'M12,3L1,9L12,15L21,10.09V17H23V9M5,13.18V17.18L12,21L19,17.18V13.18L12,17L5,13.18Z',
	'editor'     => 'M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M16.7,9.35L15.7,10.35L13.65,8.3L14.65,7.3C14.86,7.08 15.21,7.08 15.42,7.3L16.7,8.58C16.92,8.79 16.92,9.14 16.7,9.35M7,14.94L13.06,8.88L15.12,10.94L9.06,17H7V14.94Z',
	'publicar'   => 'M5,4V6H19V4H5M5,14H9V20H15V14H19L12,7L5,14Z',
	'grafico'    => 'M22,21H2V3H4V19H6V10H10V19H12V6H16V19H18V14H22V21Z',
	'documento'  => 'M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z',
	'planilla'   => 'M5,4H19A2,2 0 0,1 21,6V18A2,2 0 0,1 19,20H5A2,2 0 0,1 3,18V6A2,2 0 0,1 5,4M5,8V12H11V8H5M13,8V12H19V8H13M5,14V18H11V14H5M13,14V18H19V14H13Z',
	'pantalla'   => 'M21,16H3V4H21M21,2H3C1.89,2 1,2.89 1,4V16A2,2 0 0,0 3,18H10V20H8V22H16V20H14V18H21A2,2 0 0,0 23,16V4C23,2.89 22.1,2 21,2Z',
	'historial'  => 'M13.5,8H12V13L16.28,15.54L17,14.33L13.5,12.25V8M13,3A9,9 0 0,0 4,12H1L4.96,16.03L9,12H6A7,7 0 0,1 13,5A7,7 0 0,1 20,12A7,7 0 0,1 13,19C11.07,19 9.32,18.21 8.06,16.94L6.64,18.36C8.27,20 10.5,21 13,21A9,9 0 0,0 22,12A9,9 0 0,0 13,3',
	'estrella'   => 'M12,17.27L18.18,21L16.54,13.64L22.2,8.88L14.81,8.24L12,1.5L9.19,8.24L1.8,8.88L7.45,13.64L5.82,21L12,17.27Z',
	'etiqueta'   => 'M5.5,7A1.5,1.5 0 0,1 4,5.5A1.5,1.5 0 0,1 5.5,4A1.5,1.5 0 0,1 7,5.5A1.5,1.5 0 0,1 5.5,7M21.41,11.58L12.41,2.58C12.05,2.22 11.55,2 11,2H4C2.89,2 2,2.89 2,4V11C2,11.55 2.22,12.05 2.59,12.41L11.58,21.41C11.95,21.77 12.45,22 13,22C13.55,22 14.05,21.77 14.41,21.41L21.41,14.41C21.78,14.05 22,13.55 22,13C22,12.44 21.77,11.94 21.41,11.58Z',
	'comentario' => 'M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9Z',
	'ojo'        => 'M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z',
	'lapiz'      => 'M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z',
	'mover'      => 'M14,18V15H10V11H14V8L19,13M20,6H12L10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6Z',
	'check'      => 'M10,17L5,12L6.41,10.59L10,14.17L17.59,6.58L19,8L10,17M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3Z',
	'orden'      => 'M18,21L14,17H17V7H14L18,3L22,7H19V17H22M2,19V17H12V19M2,13V11H9V13M2,7V5H6V7H2Z',
	'reloj'      => 'M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z',
	'repetir'    => 'M17,17H7V14L3,18L7,22V19H19V13H17M7,7H17V10L21,6L17,2V5H5V11H7V7Z',
	'adjunto'    => 'M16.5,6V17.5A4,4 0 0,1 12.5,21.5A4,4 0 0,1 8.5,17.5V5A2.5,2.5 0 0,1 11,2.5A2.5,2.5 0 0,1 13.5,5V15.5A1,1 0 0,1 12.5,16.5A1,1 0 0,1 11.5,15.5V6H10V15.5A2.5,2.5 0 0,0 12.5,18A2.5,2.5 0 0,0 15,15.5V5A4,4 0 0,0 11,1A4,4 0 0,0 7,5V17.5A5.5,5.5 0 0,0 12.5,23A5.5,5.5 0 0,0 18,17.5V6H16.5Z',
	'responder'  => 'M10,9V5L3,12L10,19V14.9C15,14.9 18.5,16.5 21,20C20,15 17,10 10,9Z',
	'mas'        => 'M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z',
	'abrir'      => 'M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z',
	'arriba'     => 'M13,20H11V8L5.5,13.5L4.08,12.08L12,4.16L19.92,12.08L18.5,13.5L13,8V20Z',
	'idea'       => 'M12,2A7,7 0 0,0 5,9C5,11.38 6.19,13.47 8,14.74V17A1,1 0 0,0 9,18H15A1,1 0 0,0 16,17V14.74C17.81,13.47 19,11.38 19,9A7,7 0 0,0 12,2M9,21A1,1 0 0,0 10,22H14A1,1 0 0,0 15,21V20H9V21Z',
	'alerta'     => 'M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z',
	'ayuda'      => 'M15.07,11.25L14.17,12.17C13.45,12.89 13,13.5 13,15H11V14.5C11,13.39 11.45,12.39 12.17,11.67L13.41,10.41C13.78,10.05 14,9.55 14,9C14,7.89 13.1,7 12,7A2,2 0 0,0 10,9H8A4,4 0 0,1 12,5A4,4 0 0,1 16,9C16,9.88 15.64,10.67 15.07,11.25M13,19H11V17H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12C22,6.47 17.5,2 12,2Z',
	'foto'       => 'M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5Z',
	'contacto'   => 'M6,17C6,15 10,13.9 12,13.9C14,13.9 18,15 18,17V18H6M15,9A3,3 0 0,1 12,12A3,3 0 0,1 9,9A3,3 0 0,1 12,6A3,3 0 0,1 15,9M3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3H5C3.89,3 3,3.9 3,5Z',
	'calendario' => 'M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z',
	'correo'     => 'M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z',
	'formulario' => 'M17,9H7V7H17M17,13H7V11H17M14,17H7V15H14M12,3A1,1 0 0,1 13,4A1,1 0 0,1 12,5A1,1 0 0,1 11,4A1,1 0 0,1 12,3M19,3H14.82C14.4,1.84 13.3,1 12,1C10.7,1 9.6,1.84 9.18,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3Z',
	'actividad'  => 'M3,13H5.79L10.1,4.79L11.28,13.75L14.5,9.66L17.83,13H21V15H17L14.67,12.67L9.92,18.73L8.94,11.31L7,15H3V13Z',
	'tablero'    => 'M3,3H9V21H3V3M10,3H16V14H10V3M17,3H21V9H17V3Z',
	'lista'      => 'M3,5H9V11H3V5M5,7V9H7V7H5M11,7H21V9H11V7M11,15H21V17H11V15M5,20L1.5,16.5L2.91,15.09L5,17.17L9.59,12.59L11,14L5,20Z',
	'web'        => 'M16.36,14C16.44,13.34 16.5,12.68 16.5,12C16.5,11.32 16.44,10.66 16.36,10H19.74C19.9,10.64 20,11.31 20,12C20,12.69 19.9,13.36 19.74,14M14.59,19.56C15.19,18.45 15.65,17.25 15.97,16H18.92C17.96,17.65 16.43,18.93 14.59,19.56M14.34,14H9.66C9.56,13.34 9.5,12.68 9.5,12C9.5,11.32 9.56,10.65 9.66,10H14.34C14.43,10.65 14.5,11.32 14.5,12C14.5,12.68 14.43,13.34 14.34,14M12,19.96C11.17,18.76 10.5,17.43 10.09,16H13.91C13.5,17.43 12.83,18.76 12,19.96M8,8H5.08C6.03,6.34 7.57,5.06 9.4,4.44C8.8,5.55 8.35,6.75 8,8M5.08,16H8C8.35,17.25 8.8,18.450 9.4,19.56C7.57,18.93 6.03,17.65 5.08,16M4.26,14C4.1,13.36 4,12.69 4,12C4,11.31 4.1,10.64 4.26,10H7.64C7.56,10.66 7.5,11.32 7.5,12C7.5,12.68 7.56,13.34 7.64,14M12,4.03C12.83,5.23 13.5,6.57 13.91,8H10.09C10.5,6.57 11.17,5.23 12,4.03M18.92,8H15.97C15.65,6.75 15.19,5.55 14.59,4.44C16.43,5.07 17.96,6.34 18.92,8M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z',
];

$svg = static function (string $nombre) use ($ICONOS, $h): string {
	$d = $ICONOS[$nombre] ?? $ICONOS['ayuda'];
	return '<svg class="ayuda-ico" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="' . $h($d) . '"/></svg>';
};

// Ícono con el SVG real (como máscara) o, si no hay, el dibujo propio.
$ico = static function (string $url, string $respaldo) use ($svg, $h): string {
	if ($url === '') {
		return $svg($respaldo);
	}
	return '<span class="ayuda-ico ayuda-ico--app" style="--ayuda-icono:url(&quot;' . $h($url) . '&quot;)" aria-hidden="true"></span>';
};

/* =========================================================================
 * SECCIONES
 * ========================================================================= */

$S = [];
$sec = static function (string $id, string $titulo, string $icono, string $respaldo, string $abrir = '', bool $admin = false) use (&$S): void {
	$S[$id] = compact('id', 'titulo', 'icono', 'respaldo', 'abrir', 'admin');
};

$sitioCursos = $sitioDeModulo('cursos');
$sitioEditor = $sitioDeModulo('editor');
$sitioPublic = $sitioDeModulo('publicacion');
$sitioEstad  = $sitioDeModulo('estadisticas');

$sec('primeros-pasos', 'Primeros pasos', '', 'pasos');
$sec('archivos', 'Archivos', $iconoApp('files'), 'carpeta', $rutaApp('files'));
$sec('compartir', 'Compartir', $iconoApp('files_sharing'), 'compartir');
if ($app('onlyoffice')) {
	$sec('documentos', 'Documentos', $iconoApp('onlyoffice', ['app.svg', 'app-dark.svg']), 'documento');
}
if ($app('photos')) {
	$sec('fotos', 'Fotos', $iconoApp('photos'), 'foto', $rutaApp('photos'));
}
if ($app('contacts')) {
	$sec('contactos', 'Contactos', $iconoApp('contacts'), 'contacto', $rutaApp('contacts'));
}
if ($app('calendar')) {
	$sec('calendario', 'Calendario', $iconoApp('calendar'), 'calendario', $rutaApp('calendar'));
}
if ($app('mail')) {
	$sec('correo', 'Correo', $iconoApp('mail'), 'correo', $rutaApp('mail'));
}
if ($app('forms')) {
	$sec('formularios', 'Formularios', $iconoApp('forms'), 'formulario', $rutaApp('forms'));
}
if ($app('deck')) {
	$sec('tableros', 'Tableros', $iconoApp('deck'), 'tablero', $rutaApp('deck'));
}
if ($app('activity')) {
	$sec('actividad', 'Actividad', $iconoApp('activity', ['app.svg', 'activity.svg']), 'actividad', $rutaApp('activity'));
}
$sec('notificaciones', 'Notificaciones', $iconoApp('notifications', ['app.svg', 'notifications.svg']), 'campana');
$sec('busqueda', 'Búsqueda', '', 'buscar');
$sec('papelera', 'Papelera', $iconoApp('files_trashbin'), 'papelera');
if ($moduloActivo('cursos')) {
	$sec('cursos', 'Cursos', $sitioCursos['icono'] ?? '', 'curso', $sitioCursos['enlace'] ?? '');
}
if ($moduloActivo('editor') && ($ayudaEsAdmin || $sitioEditor !== null)) {
	$sec('editor', 'Editor de sitio', $sitioEditor['icono'] ?? '', 'editor', $sitioEditor['enlace'] ?? '');
}
if ($moduloActivo('publicacion')) {
	$sec('publicacion', 'Publicación', $sitioPublic['icono'] ?? '', 'publicar', $sitioPublic['enlace'] ?? '');
}
if ($moduloActivo('estadisticas') || ($ayudaEsAdmin && $app('serverinfo'))) {
	$sec('estadisticas', 'Estadísticas', $sitioEstad['icono'] ?? '', 'grafico', $sitioEstad['enlace'] ?? '');
}
if ($ayudaSitios) {
	$sec('herramientas', 'Herramientas InSSA', '', 'web');
}
$sec('usuarios', 'Usuarios y grupos', '', 'grupo');
$sec('configuracion', 'Configuración', '', 'engranaje', $urlBase('index.php/settings/user'));
$sec('seguridad', 'Privacidad y seguridad', '', 'escudo', $urlBase('index.php/settings/user/security'));
$sec('moviles', 'Celulares y tablets', '', 'movil');
if ($ayudaEsAdmin) {
	$sec('administracion', 'Administración', '', 'llave', $urlBase('index.php/settings/admin'), true);
}

/* ---- Ayudantes de maquetado ---- */

$abrirSec = static function (string $id) use (&$S, $ico, $svg, $h): void {
	$s = $S[$id];
	echo '<section class="ayuda-panel" id="' . $h($id) . '">';
	echo '<header class="ayuda-panel__cabecera">' . $ico($s['icono'], $s['respaldo']) . '<span class="ayuda-panel__titulo">' . $h($s['titulo']) . '</span>';
	if ($s['admin']) {
		echo '<span class="ayuda-rol">' . $svg('llave') . 'Administración</span>';
	}
	if ($s['abrir'] !== '') {
		echo '<a class="ayuda-abrir" href="' . $h($s['abrir']) . '">' . $svg('abrir') . 'Abrir</a>';
	}
	echo '</header><div class="ayuda-panel__cuerpo">';
};

$cerrarSec = static function () use ($svg): void {
	echo '<a class="ayuda-volver" href="#ayuda-inicio">' . $svg('arriba') . 'Volver arriba</a></div></section>';
};

$sub = static function (string $titulo, string $icono, bool $admin = false) use ($svg, $h): void {
	echo '<h3 class="ayuda-sub">' . $svg($icono) . '<span>' . $h($titulo) . '</span>';
	if ($admin) {
		echo '<span class="ayuda-rol">' . $svg('llave') . 'Administración</span>';
	}
	echo '</h3>';
};

// Tabla de dos columnas. $filas: [icono, nombre, texto con HTML propio].
$tabla = static function (string $col1, string $col2, array $filas) use ($svg, $h): void {
	echo '<div class="ayuda-tabla-contenedor"><table class="ayuda-tabla"><thead><tr><th>' . $h($col1) . '</th><th>' . $h($col2) . '</th></tr></thead><tbody>';
	foreach ($filas as $f) {
		echo '<tr><td class="ayuda-tabla__nombre">' . $svg($f[0]) . '<span>' . $h($f[1]) . '</span></td><td>' . $f[2] . '</td></tr>';
	}
	echo '</tbody></table></div>';
};

$nota = static function (string $html, string $tipo = 'idea') use ($svg): void {
	echo '<div class="ayuda-nota ayuda-nota--' . ($tipo === 'alerta' ? 'alerta' : 'idea') . '">' . $svg($tipo === 'alerta' ? 'alerta' : 'idea') . '<div>' . $html . '</div></div>';
};

$enlace = static function (string $id, string $texto) use (&$S, $h): string {
	return isset($S[$id]) ? '<a href="#' . $h($id) . '">' . $h($texto) . '</a>' : $h($texto);
};

$nonce = '';
try {
	$gestor = $ayudaServicio(\OC\Security\CSP\ContentSecurityPolicyNonceManager::class);
	if ($gestor !== null) {
		$nonce = (string) $gestor->getNonce();
	}
} catch (\Throwable $e) {
	$nonce = '';
}
$dominio = $h($ayudaUrl !== null ? $ayudaUrl->getAbsoluteURL('/') : '');
?>
<style>
/* Ayuda de Nube InSSA. Usa las variables de color de la nube, así respeta
   el tema institucional y el modo oscuro. */

#app-navigation .ayuda-nav { padding-top: 4px; }
#app-navigation .ayuda-nav__titulo {
	font-size: 11px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
	color: var(--color-text-maxcontrast); padding: 12px 18px 4px;
}
#app-navigation .ayuda-nav li { padding: 0 8px; }
#app-navigation .ayuda-nav a.ayuda-nav__link {
	display: flex; align-items: center; min-height: 34px; line-height: 34px;
	padding: 0 9px 0 0 !important; background-image: none !important;
	border-radius: var(--border-radius-large, 8px); color: var(--color-main-text);
	font-size: 15px; font-weight: 400; opacity: 1; box-shadow: none;
}
#app-navigation .ayuda-nav a.ayuda-nav__link:hover { background-color: var(--color-background-hover); }
#app-navigation .ayuda-nav a.ayuda-nav__link.active {
	background-color: var(--color-primary-element); color: var(--color-primary-element-text);
}
#app-navigation .ayuda-nav__icono {
	width: 34px; height: 34px; flex-shrink: 0;
	display: flex; align-items: center; justify-content: center;
}
#app-navigation .ayuda-nav .ayuda-ico { width: 18px; height: 18px; color: inherit; }
#app-navigation .ayuda-nav__texto { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
#app-navigation .ayuda-nav__separador { border-top: 1px solid var(--color-border); margin: 8px 0 4px; }

#app-content.ayuda-nube { overflow-y: auto; }
.ayuda-contenido { padding: 22px 30px 60px; max-width: 1500px; }

.ayuda-ico { width: 18px; height: 18px; flex-shrink: 0; display: inline-block; color: var(--color-primary-element); }
.ayuda-ico--app {
	background-color: currentColor;
	-webkit-mask: var(--ayuda-icono) center / contain no-repeat;
	mask: var(--ayuda-icono) center / contain no-repeat;
}

/* Encabezado y buscador */
.ayuda-encabezado { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
.ayuda-encabezado h2 { font-size: 20px; font-weight: 700; margin: 0; flex: 1; min-width: 200px; }
.ayuda-buscador { position: relative; width: 340px; max-width: 100%; }
.ayuda-buscador .ayuda-ico { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-maxcontrast); }
.ayuda-buscador input[type="search"] {
	width: 100%; margin: 0; height: 36px; padding: 0 12px 0 38px;
	border: 2px solid var(--color-border-maxcontrast, var(--color-border-dark)); border-radius: var(--border-radius-large, 8px);
	background: var(--color-main-background); color: var(--color-main-text); font-size: 14px;
}
.ayuda-buscador input[type="search"]:focus { border-color: var(--color-primary-element); outline: none; }
.ayuda-intro { color: var(--color-text-maxcontrast); margin-bottom: 18px; line-height: 1.6; }

/* Fila de accesos, como botones */
.ayuda-accesos { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 22px; }
.ayuda-accesos a {
	display: inline-flex; align-items: center; gap: 8px; padding: 9px 16px;
	border: 1px solid var(--color-border); border-radius: var(--border-radius-large, 8px);
	background: var(--color-main-background); color: var(--color-main-text); font-size: 15px;
}
.ayuda-accesos a:hover { background: var(--color-background-hover); }
.ayuda-accesos a.ayuda-accesos--admin { border-style: dashed; }

/* Paneles */
.ayuda-panel {
	border: 1px solid var(--color-border); border-radius: var(--border-radius-large, 10px);
	background: var(--color-main-background); margin-bottom: 22px; overflow: hidden;
	scroll-margin-top: 12px;
}
.ayuda-panel__cabecera {
	display: flex; align-items: center; gap: 10px; padding: 12px 20px;
	background: var(--color-background-hover); border-bottom: 1px solid var(--color-border);
	font-size: 15px;
}
.ayuda-panel__titulo { flex: 1; }
.ayuda-abrir {
	display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
	border-radius: var(--border-radius-large, 8px); color: var(--color-primary-element-text);
	background: var(--color-primary-element); font-weight: 600; font-size: 13px;
}
.ayuda-abrir .ayuda-ico { width: 14px; height: 14px; color: inherit; }
.ayuda-abrir:hover { background: var(--color-primary-element-hover); }
.ayuda-panel__cuerpo { padding: 6px 24px 18px; }

.ayuda-sub {
	display: flex; align-items: center; gap: 10px; font-size: 17px; font-weight: 700;
	margin: 22px 0 12px; padding-bottom: 10px; border-bottom: 1px solid var(--color-border);
	color: var(--color-main-text);
}
.ayuda-sub .ayuda-ico { width: 20px; height: 20px; }
.ayuda-sub span:not(.ayuda-rol) { flex: 1; }
.ayuda-panel__cuerpo p, .ayuda-panel__cuerpo li { color: var(--color-text-maxcontrast); line-height: 1.7; font-size: 14.5px; }
.ayuda-panel__cuerpo p { margin: 0 0 10px; }
.ayuda-panel__cuerpo b, .ayuda-panel__cuerpo strong { color: var(--color-main-text); }
.ayuda-panel__cuerpo ol, .ayuda-panel__cuerpo ul { margin: 0 0 12px 22px; }
.ayuda-panel__cuerpo ol { list-style: decimal; }
.ayuda-panel__cuerpo ul { list-style: disc; }
.ayuda-panel__cuerpo li::marker { color: var(--color-primary-element); }
.ayuda-panel__cuerpo a:not(.ayuda-abrir):not(.ayuda-volver) { color: var(--color-primary-element); text-decoration: underline; }
.ayuda-panel__cuerpo code {
	background: var(--color-background-dark); padding: 2px 6px; border-radius: 4px;
	font-size: 13px; color: var(--color-main-text); word-break: break-all;
}

/* Tablas */
.ayuda-tabla-contenedor { overflow-x: auto; margin: 4px 0 14px; }
.ayuda-tabla { width: 100%; border-collapse: collapse; font-size: 14.5px; }
.ayuda-tabla th {
	text-align: left; font-size: 12px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
	color: var(--color-text-maxcontrast); background: var(--color-background-hover);
	padding: 10px 16px; border-bottom: 1px solid var(--color-border);
}
.ayuda-tabla td { padding: 11px 16px; border-bottom: 1px solid var(--color-border); vertical-align: top; color: var(--color-main-text); line-height: 1.55; }
.ayuda-tabla tr:last-child td { border-bottom: none; }
.ayuda-tabla__nombre { white-space: nowrap; width: 1%; }
.ayuda-tabla__nombre .ayuda-ico { vertical-align: -3px; margin-right: 10px; }
.ayuda-tabla td a { color: var(--color-primary-element); text-decoration: underline; }

/* Notas y etiquetas */
.ayuda-nota {
	display: flex; gap: 10px; padding: 11px 14px; border-radius: var(--border-radius-large, 8px);
	margin: 12px 0; font-size: 14px; line-height: 1.55; border-left: 4px solid;
}
.ayuda-nota .ayuda-ico { width: 20px; height: 20px; color: inherit; }
.ayuda-nota--idea   { background: var(--color-primary-element-light); border-color: var(--color-primary-element); color: var(--color-main-text); }
.ayuda-nota--idea .ayuda-ico { color: var(--color-primary-element); }
.ayuda-nota--alerta { background: var(--color-warning-hover, rgba(233,163,34,.12)); border-color: var(--color-warning, #e9a322); color: var(--color-main-text); }
.ayuda-nota--alerta .ayuda-ico { color: var(--color-warning-text, #a36a00); }
.ayuda-rol {
	display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600;
	padding: 2px 10px; border-radius: 999px; white-space: nowrap;
	background: var(--color-background-dark); color: var(--color-main-text);
}
.ayuda-rol .ayuda-ico { width: 13px; height: 13px; color: inherit; }

.ayuda-volver {
	display: inline-flex; align-items: center; gap: 6px; margin-top: 14px; font-size: 13px;
	color: var(--color-text-maxcontrast); padding: 4px 8px; border-radius: var(--border-radius, 6px);
}
.ayuda-volver .ayuda-ico { width: 14px; height: 14px; color: inherit; }
.ayuda-volver:hover { background: var(--color-background-hover); color: var(--color-main-text); }

.ayuda-sin-resultados { text-align: center; padding: 40px 20px; color: var(--color-text-maxcontrast); }
.ayuda-contenido mark { background: var(--color-primary-element-light); color: var(--color-main-text); border-radius: 3px; padding: 0 1px; }
.ayuda-oculto { display: none !important; }
.ayuda-buscador { display: none; }
.ayuda-js .ayuda-buscador { display: block; }

@media (max-width: 1024px) {
	.ayuda-contenido { padding: 16px 16px 60px; }
	.ayuda-panel__cuerpo { padding: 4px 16px 14px; }
	.ayuda-tabla__nombre { white-space: normal; }
}
@media (max-width: 600px) {
	.ayuda-accesos a { padding: 7px 12px; font-size: 14px; }
	.ayuda-tabla, .ayuda-tabla tbody, .ayuda-tabla tr, .ayuda-tabla td { display: block; width: 100%; }
	.ayuda-tabla thead { display: none; }
	.ayuda-tabla tr { border-bottom: 1px solid var(--color-border); padding: 8px 0; }
	.ayuda-tabla td { border: none; padding: 4px 12px; }
	.ayuda-tabla__nombre { font-weight: 600; }
}
</style>

<div id="app-navigation">
	<ul>
	<?php if ($_['admin']) { ?>
		<li>
			<a class="help-list__link icon-user-admin <?php if ($_['mode'] === 'admin') {
	p('active');
} ?>"
				<?php if ($_['mode'] === 'admin') {
	print_unescaped('aria-current="page"');
} ?>
				href="<?php print_unescaped($_['urlAdminDocs']); ?>">
				<span class="help-list__text">
					<?php p($l->t('Administrator documentation')); ?>
				</span>
			</a>
		</li>
	<?php } ?>
		<li>
			<a href="https://ayuda.inssa.net.ar/nube/ayuda.pdf" class="help-list__link icon-category-office" target="_blank" rel="noreferrer noopener">
				<span class="help-list__text">
					<?php p($l->t('Documentation')); ?> ↗
				</span>
			</a>
		</li>
		<li>
			<a href="https://wa.me/543416589067?text=Hola%20Ariel,%20Necesito%20ayuda%20con%20un%20tema%20relacionado%20a%20la%20nube" class="help-list__link icon-comment" target="_blank" rel="noreferrer noopener">
				<span class="help-list__text">
					<?php p($l->t('Chat')); ?> ↗
				</span>
			</a>
		</li>
		<li>
			<a href="https://nube.inssa.net.ar/index.php/settings/user/privacy" class="help-list__link icon-category-security" target="_blank" rel="noreferrer noopener">
				<span class="help-list__text">
					<?php p($l->t('Privacidad')); ?> ↗
				</span>
			</a>
		</li>
	</ul>
	<div class="ayuda-nav__separador"></div>
	<div class="ayuda-nav__titulo">Temas de ayuda</div>
	<ul class="ayuda-nav">
	<?php foreach ($S as $s) { ?>
		<li>
			<a class="ayuda-nav__link" href="#<?php print_unescaped($h($s['id'])); ?>" data-destino="<?php print_unescaped($h($s['id'])); ?>">
				<span class="ayuda-nav__icono"><?php print_unescaped($ico($s['icono'], $s['respaldo'])); ?></span>
				<span class="ayuda-nav__texto"><?php print_unescaped($h($s['titulo'])); ?></span>
			</a>
		</li>
	<?php } ?>
	</ul>
</div>

<div id="app-content" class="ayuda-nube">
<div class="ayuda-contenido" id="ayuda-inicio">

	<div class="ayuda-encabezado">
		<h2>Ayuda de Nube InSSA</h2>
		<div class="ayuda-buscador" role="search">
			<?php print_unescaped($svg('buscar')); ?>
			<input type="search" id="ayuda-buscar" placeholder="Buscar en la ayuda…" aria-label="Buscar en la ayuda" autocomplete="off">
		</div>
	</div>
	<p class="ayuda-intro">Acá encontrás todo lo que podés hacer en Nube InSSA, explicado paso a paso. Elegí un tema o escribí lo que necesitás en el buscador.</p>

	<nav class="ayuda-accesos" aria-label="Temas de ayuda">
	<?php foreach ($S as $s) { ?>
		<a href="#<?php print_unescaped($h($s['id'])); ?>"<?php if ($s['admin']) { print_unescaped(' class="ayuda-accesos--admin"'); } ?>><?php print_unescaped($ico($s['icono'], $s['respaldo'])); ?><?php print_unescaped($h($s['titulo'])); ?></a>
	<?php } ?>
	</nav>

	<div id="ayuda-sin-resultados" class="ayuda-sin-resultados ayuda-oculto">No encontramos nada con esas palabras. Probá con otras, por ejemplo "subir", "enlace" o "contraseña".</div>

<?php /* ============================ PRIMEROS PASOS ============================ */ ?>
<?php $abrirSec('primeros-pasos'); ?>
	<?php $sub('Qué es Nube InSSA', 'ayuda'); ?>
	<p>Es tu espacio de trabajo en línea. Todo lo que guardás acá está disponible desde cualquier computadora, celular o tablet con internet. Solamente necesitás tu usuario y tu contraseña.</p>

	<?php $sub('La pantalla', 'pantalla'); ?>
	<?php $tabla('Lugar', 'Para qué sirve', [
		['lista', 'Barra superior', 'Los íconos de las aplicaciones. Tocá uno para abrirla. Al pasar el mouse ves su nombre.'],
		['persona', 'Tu foto o tus iniciales', 'Arriba a la derecha. Desde ahí entrás a tu ' . $enlace('configuracion', 'configuración') . ', a esta ayuda y cerrás la sesión.'],
		['buscar', 'La lupa', 'Busca en toda Nube InSSA a la vez. Mirá ' . $enlace('busqueda', 'Búsqueda') . '.'],
		['campana', 'La campana', 'Te avisa cuando te comparten algo, te mencionan o hay novedades. Mirá ' . $enlace('notificaciones', 'Notificaciones') . '.'],
		['lista', 'Menú izquierdo', 'Las secciones de la aplicación que tenés abierta. En el celular se abre con el botón de tres rayas.'],
	]); ?>

	<?php $sub('Ingresar y salir', 'candado'); ?>
	<ol>
		<li>Entrá a <code><?php print_unescaped($dominio); ?></code> desde tu navegador.</li>
		<li>Escribí tu usuario y tu contraseña, y tocá el botón para iniciar sesión.</li>
		<li>Para salir, tocá tu foto arriba a la derecha y elegí la opción para cerrar sesión.</li>
	</ol>
	<?php $nota('Si usaste una computadora compartida, cerrá siempre la sesión al terminar. Cerrar la pestaña no alcanza.', 'alerta'); ?>
<?php if ($app('dashboard')) { ?>
	<?php $sub('El panel de inicio', 'tablero'); ?>
	<p>Es la primera pantalla al entrar. Reúne lo más importante del día: próximos eventos, archivos recientes y novedades, según lo que tengas activado. Lo personalizás con el botón que está al pie del panel.</p>
<?php } ?>
<?php $cerrarSec(); ?>

<?php /* ============================ ARCHIVOS ============================ */ ?>
<?php $abrirSec('archivos'); ?>
	<p>Archivos es el corazón de Nube InSSA. Guardás, ordenás y compartís todos tus documentos desde un solo lugar.</p>

	<?php $sub('Cómo entran los archivos', 'subir'); ?>
	<?php $tabla('Forma', 'Cuándo conviene', [
		['subir', 'Arrastrar y soltar', 'Seleccioná los archivos en tu computadora y soltalos sobre la lista. Se suben a la carpeta abierta. Es lo más rápido para muchos archivos.'],
		['mas', 'Botón + Nuevo', 'Arriba a la izquierda. Elegí la opción para subir archivos y buscalos en tu equipo. Desde el mismo botón creás carpetas' . ($app('onlyoffice') ? ' y documentos nuevos' : '') . '.'],
	]); ?>
	<?php $nota('Mientras se sube vas a ver una barra de progreso. No cierres la pestaña hasta que termine.'); ?>

	<?php $sub('Qué podés hacer con cada archivo', 'lista'); ?>
	<p>Todas las acciones están en el botón de tres puntos (<b>⋯</b>) de cada archivo o carpeta.</p>
	<?php $tabla('Acción', 'Qué hace', [
		['ojo', 'Abrir', 'Tocá el archivo. Fotos, videos, PDF y documentos se ven en el navegador sin descargarlos.'],
		['bajar', 'Descargar', 'Lo baja a tu equipo. Si elegís varios o una carpeta, se descargan en un .zip.'],
		['lapiz', 'Renombrar', 'Cambiá el nombre y apretá Enter.'],
		['mover', 'Mover o copiar', 'Elegís la carpeta de destino. Mover lo cambia de lugar; copiar deja uno en cada lado.'],
		['compartir', 'Compartir', 'Da acceso a otras personas. Mirá ' . $enlace('compartir', 'Compartir') . '.'],
		['estrella', 'Favorito', 'Le pone una estrella y aparece en la sección Favoritos.'],
		['papelera', 'Eliminar', 'Lo manda a la papelera. Todavía se puede recuperar. Mirá ' . $enlace('papelera', 'Papelera') . '.'],
	]); ?>

	<?php $sub('Ordenar y encontrar', 'orden'); ?>
	<?php $tabla('Herramienta', 'Qué hace', array_values(array_filter([
		['orden', 'Títulos de columna', 'Tocá Nombre, Tamaño o Modificado para ordenar. Tocá de nuevo para invertir el orden.'],
		['tablero', 'Vista lista o cuadrícula', 'Arriba a la derecha. La cuadrícula muestra miniaturas grandes, cómoda para imágenes.'],
		['check', 'Selección múltiple', 'Marcá la casilla de cada archivo. Arriba aparecen las acciones para todos juntos: descargar, mover, eliminar.'],
		['reloj', 'Recientes', 'En el menú izquierdo. Lo último que se modificó.'],
		['estrella', 'Favoritos', 'En el menú izquierdo. Lo que marcaste con estrella.'],
		['compartir', 'Compartidos', 'En el menú izquierdo. Lo que te compartieron y lo que compartiste, incluidos los enlaces.'],
		$app('systemtags') ? ['etiqueta', 'Etiquetas', 'Agrupan archivos de carpetas distintas, por ejemplo "Urgente" o "2026". Se agregan desde los detalles del archivo.'] : null,
	]))); ?>
	<?php $nota('Seleccionar varios archivos ahorra tiempo. Por ejemplo, para ordenar veinte documentos sueltos, marcalos todos y movelos juntos a una carpeta nueva.'); ?>

	<?php $sub('Detalles, comentarios y versiones', 'comentario'); ?>
	<p>Desde el menú <b>⋯</b> abrí los detalles del archivo. Se abre un panel a la derecha con estas pestañas:</p>
	<?php $tabla('Pestaña', 'Qué muestra', array_values(array_filter([
		$app('activity') ? ['actividad', 'Actividad', 'Quién creó, cambió o compartió el archivo y cuándo.'] : null,
		$app('comments') ? ['comentario', 'Comentarios', 'Notas para quienes comparten el archivo. Escribí <code>@</code> y un nombre para avisarle a esa persona.'] : null,
		['compartir', 'Compartir', 'Con quién está compartido y con qué permisos.'],
		$app('files_versions') ? ['historial', 'Versiones', 'Las copias anteriores del archivo. Podés descargar una o restaurarla si algo salió mal. La versión actual no se pierde: queda como una más.'] : null,
	]))); ?>
<?php if ($app('files_versions')) { ?>
	<?php $nota('Las versiones viejas se borran solas con el tiempo. No las uses como único respaldo de algo importante.'); ?>
<?php } ?>
<?php if ($app('groupfolders')) { ?>
	<?php $sub('Carpetas de grupo', 'grupo'); ?>
	<p>Algunas carpetas pertenecen a un área y no a una persona. Todas las personas del grupo trabajan en ellas según los permisos que definió la administración. Si dejás el grupo, dejás de verla, pero el contenido sigue para el resto.</p>
<?php } ?>

	<?php $sub('Espacio de almacenamiento', 'carpeta'); ?>
	<p>Al pie del menú izquierdo de Archivos ves cuánto espacio usás y, si tu cuenta tiene límite, cuánto te queda. Si te acercás al límite, vaciá la papelera y borrá lo que no necesites. Los archivos que te comparten ocupan lugar en la cuenta de quien los compartió, no en la tuya.</p>
<?php $cerrarSec(); ?>

<?php /* ============================ COMPARTIR ============================ */ ?>
<?php $abrirSec('compartir'); ?>
	<p>Compartir es darle acceso a otra persona a un archivo o una carpeta, sin mandar copias por correo. Todos trabajan sobre el mismo archivo y nadie se queda con una versión vieja.</p>

	<?php $sub('Con quién podés compartir', 'compartir'); ?>
	<?php $tabla('Forma', 'Cuándo conviene', [
		['persona', 'Con una persona', 'Para alguien que tiene cuenta en Nube InSSA. Escribís su nombre en la pestaña Compartir y lo ve en su propia cuenta.'],
		['grupo', 'Con un grupo', 'Para toda un área de una sola vez. Quienes se sumen al grupo más adelante también reciben acceso.'],
		['enlace', 'Con un enlace', 'Para alguien que no tiene cuenta. Cualquiera que tenga el enlace puede abrirlo, así que usalo con cuidado.'],
	]); ?>
	<ol>
		<li>Tocá el ícono de compartir del archivo, o abrí sus detalles y entrá a la pestaña <b>Compartir</b>.</li>
		<li>Escribí el nombre de la persona o del grupo, o elegí la opción para crear un enlace.</li>
		<li>Revisá los permisos y confirmá.</li>
	</ol>

	<?php $sub('Permisos', 'candado'); ?>
	<?php $tabla('Permiso', 'Qué puede hacer la otra persona', [
		['ojo', 'Solo lectura', 'Ver y descargar, sin cambiar nada. Ideal para reglamentos o documentos finales.'],
		['lapiz', 'Edición', 'Modificar el contenido. Para planillas o documentos que completa un equipo.'],
		['mas', 'Crear y eliminar', 'En carpetas: agregar archivos nuevos o borrar los que hay.'],
		['compartir', 'Volver a compartir', 'Compartirlo a su vez con otras personas.'],
	]); ?>

	<?php $sub('Opciones de los enlaces', 'enlace'); ?>
	<p>Según lo que tenga habilitado la plataforma, a un enlace le podés poner <b>contraseña</b>, <b>fecha de vencimiento</b> para que deje de funcionar solo, y decidir si permite sólo ver o también editar o subir archivos.</p>
	<?php $nota('Para información personal o sensible, poné siempre contraseña y vencimiento. Mandá la contraseña por otro medio, no en el mismo mensaje que el enlace.', 'alerta'); ?>

	<?php $sub('Cambiar permisos o dejar de compartir', 'lapiz'); ?>
	<p>En la pestaña <b>Compartir</b> ves quién tiene acceso. Abrí el menú de cada persona, grupo o enlace para cambiar sus permisos o para dejar de compartir. El acceso se corta en el momento; si la otra persona ya lo había descargado, esa copia queda en su equipo.</p>
<?php if ($ayudaEsAdmin) { ?>
	<?php $sub('Reglas de compartición', 'llave', true); ?>
	<p>Desde la configuración de administración, en la sección de compartir, se define si se permiten enlaces públicos, si requieren contraseña o vencimiento y con quién se puede compartir. Estas reglas se aplican a todas las cuentas.</p>
<?php } ?>
<?php $cerrarSec(); ?>

<?php if (isset($S['documentos'])) { /* ============================ DOCUMENTOS ============================ */ ?>
<?php $abrirSec('documentos'); ?>
	<p>Nube InSSA incluye el editor ONLYOFFICE. Creás y editás documentos, planillas y presentaciones directamente en el navegador, sin instalar nada, y varias personas pueden trabajar en el mismo archivo al mismo tiempo.</p>

	<?php $sub('Qué podés crear', 'documento'); ?>
	<?php $tabla('Tipo', 'Para qué sirve', [
		['documento', 'Documento de texto', 'Notas, informes, cartas. Compatible con archivos de Word (.docx).'],
		['planilla', 'Planilla de cálculo', 'Tablas, fórmulas y gráficos. Compatible con Excel (.xlsx).'],
		['pantalla', 'Presentación', 'Diapositivas para exponer. Compatible con PowerPoint (.pptx).'],
	]); ?>
	<ol>
		<li>En Archivos, entrá a la carpeta donde lo querés guardar.</li>
		<li>Tocá <b>+ Nuevo</b> y elegí documento, planilla o presentación.</li>
		<li>Ponele un nombre. El editor se abre enseguida.</li>
	</ol>
	<p>Para editar uno que ya tenés, tocalo en la lista de Archivos.</p>

	<?php $sub('Cómo se guardan los cambios', 'historial'); ?>
	<p>No hace falta guardar a mano: los cambios se guardan solos mientras trabajás. Cuando todas las personas cierran el documento, la versión final queda en Nube InSSA. Puede tardar unos segundos en verse en la lista. Cada guardado genera una versión nueva, así que siempre podés volver atrás.</p>
	<?php $nota('Cerrá el documento con el botón de cerrar del editor en lugar de cerrar la pestaña de golpe. Así te asegurás de que todo quede guardado.'); ?>

	<?php $sub('Trabajo en equipo', 'grupo'); ?>
	<?php $tabla('Herramienta', 'Qué hace', [
		['compartir', 'Compartir con edición', 'Para que alguien edite con vos, compartile el archivo con permiso de edición.'],
		['grupo', 'Edición simultánea', 'Arriba ves quién está conectado y, en el texto, marcas de colores donde escribe cada persona.'],
		['repetir', 'Modo Rápido o Estricto', 'En la pestaña Colaboración. Rápido muestra los cambios al instante; Estricto, recién cuando cada persona guarda.'],
		['comentario', 'Comentarios', 'Seleccioná un texto y comentalo desde la pestaña Colaboración. Se pueden responder y marcar como resueltos.'],
		['lapiz', 'Control de cambios', 'Cada modificación queda marcada para aceptarla o rechazarla. Útil para revisiones.'],
		['bajar', 'Descargar como', 'En el menú Archivo del editor. Por ejemplo, para bajarlo en PDF.'],
	]); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['fotos'])) { /* ============================ FOTOS ============================ */ ?>
<?php $abrirSec('fotos'); ?>
	<p>Fotos muestra todas las imágenes y videos que guardaste en Nube InSSA, ordenados por fecha. Son los mismos archivos que ves en Archivos: no hace falta subirlos dos veces.</p>
	<?php $tabla('Función', 'Qué hace', [
		['foto', 'Ver', 'Tocá una foto para verla en grande y pasá a la siguiente con las flechas.'],
		['filtro', 'Filtrar', 'En el menú izquierdo elegís ver todo, sólo fotos, sólo videos o tus favoritas, entre otras vistas.'],
		['tablero', 'Álbumes', 'Agrupan fotos sin moverlas de su carpeta. Una foto puede estar en varios álbumes y los álbumes se pueden compartir.'],
		['estrella', 'Favoritas', 'Marcá tus mejores fotos para encontrarlas rápido.'],
		['bajar', 'Descargar y compartir', 'Seleccioná una o varias fotos para ver las acciones disponibles.'],
		['movil', 'Desde el celular', 'Fotos se adapta a la pantalla del teléfono. Para subir fotos del celular, usá + Nuevo en Archivos.'],
	]); ?>
	<p>Tus fotos son privadas: nadie las ve salvo que las compartas. Para ordenarlas en carpetas, hacelo desde Archivos y el cambio se refleja en Fotos.</p>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['contactos'])) { /* ============================ CONTACTOS ============================ */ ?>
<?php $abrirSec('contactos'); ?>
	<p>Contactos es tu agenda: nombres, teléfonos, correos y direcciones en un solo lugar, disponibles desde cualquier dispositivo.</p>
	<?php $sub('Qué podés hacer', 'contacto'); ?>
	<?php $tabla('Acción', 'Cómo', [
		['mas', 'Crear', 'Con el botón para crear un contacto nuevo, arriba a la izquierda. Completá los datos; se guardan solos mientras escribís.'],
		['lapiz', 'Editar', 'Elegí el contacto y modificá cualquier campo directamente.'],
		['papelera', 'Eliminar', 'Desde el menú de acciones del contacto. Desaparece también de tus dispositivos sincronizados.'],
		['grupo', 'Organizar', 'Asigná grupos (por ejemplo, "Proveedores"). Aparecen en el menú izquierdo para ver sólo esa parte de la agenda.'],
		['buscar', 'Buscar', 'Con el buscador de la lista, por nombre, correo, teléfono u otros datos.'],
		['subir', 'Importar', 'Traé tu agenda de otro programa con un archivo .vcf.'],
	]); ?>
	<?php $sub('Tus contactos en otras aplicaciones', 'enlace'); ?>
	<ul>
<?php if ($app('mail')) { ?>
		<li>En <b>Correo</b>, al escribir un destinatario te sugiere direcciones de tu agenda.</li>
<?php } ?>
<?php if ($app('calendar')) { ?>
		<li>En <b>Calendario</b>, podés invitarlos a eventos, y los cumpleaños cargados pueden aparecer en un calendario de cumpleaños.</li>
<?php } ?>
		<li>En tus dispositivos, si los sincronizás. Mirá <?php print_unescaped($enlace('moviles', 'Celulares y tablets')); ?>.</li>
	</ul>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['calendario'])) { /* ============================ CALENDARIO ============================ */ ?>
<?php $abrirSec('calendario'); ?>
	<p>Organizá reuniones, entregas, capacitaciones y cualquier fecha importante. Podés invitar a otras personas y compartir calendarios completos.</p>
	<?php $sub('Eventos', 'calendario'); ?>
	<?php $tabla('Acción', 'Cómo', [
		['mas', 'Crear', 'Hacé clic en el día y la hora, o usá el botón para crear un evento. Escribí el título, ajustá el horario y guardá.'],
		['lapiz', 'Editar o mover', 'Tocá el evento para cambiarlo. En la vista de día o semana también lo podés arrastrar.'],
		['papelera', 'Eliminar', 'Abrí el evento y usá la opción de eliminar de su menú.'],
		['repetir', 'Repetir', 'Hacé que se repita cada día, semana, mes o año. Al modificar uno, elegís si el cambio es sólo para ese o para los siguientes.'],
		['grupo', 'Participantes', 'Agregá personas por nombre o correo. Reciben la invitación por correo si ese envío está configurado, y ves quién confirma.'],
		['campana', 'Recordatorios', 'Un aviso antes del evento, como notificación o por correo.'],
	]); ?>
	<?php $nota('Ejemplo: la reunión de equipo de todos los lunes a las 9. La creás una vez con repetición semanal y aparece sola cada semana.'); ?>
	<?php $sub('Calendarios y vistas', 'tablero'); ?>
	<?php $tabla('Función', 'Qué hace', [
		['calendario', 'Varios calendarios', 'Creá uno por tema, cada uno con su color, desde el menú izquierdo.'],
		['compartir', 'Compartir', 'Con personas o grupos para verlo o editarlo, o con un enlace público para alguien externo.'],
		['ojo', 'Vistas', 'Día, semana, mes o lista. Con las flechas avanzás y con el botón de hoy volvés a la fecha actual.'],
		['repetir', 'Sincronizar', 'Con aplicaciones de calendario compatibles con CalDAV. También podés importar o exportar archivos .ics.'],
	]); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['correo'])) { /* ============================ CORREO ============================ */ ?>
<?php $abrirSec('correo'); ?>
	<p>Leé y enviá los mensajes de tu casilla sin salir de Nube InSSA.</p>
	<?php $sub('Tu cuenta', 'correo'); ?>
	<p>La primera vez, Correo te pide los datos de tu casilla. Si ya ves tus mensajes al entrar, la cuenta está configurada y no tenés que hacer nada. Podés agregar más de una cuenta; cada una aparece en el menú izquierdo con sus carpetas.</p>
	<?php $sub('Carpetas', 'carpeta'); ?>
	<?php $tabla('Carpeta', 'Qué guarda', [
		['correo', 'Bandeja de entrada', 'Los mensajes que recibís.'],
		['responder', 'Enviados', 'Los mensajes que mandaste.'],
		['lapiz', 'Borradores', 'Los que empezaste y no enviaste. Si cerrás un mensaje sin enviar, queda acá.'],
		['papelera', 'Papelera', 'Los que borraste.'],
	]); ?>
	<?php $sub('Mensajes', 'responder'); ?>
	<?php $tabla('Acción', 'Cómo', [
		['mas', 'Redactar', 'Con el botón de mensaje nuevo. Al escribir el destinatario te sugiere tus contactos.'],
		['responder', 'Responder y reenviar', 'Arriba del mensaje abierto: responder, responder a todos y reenviar.'],
		['adjunto', 'Adjuntar', 'Subí un archivo de tu equipo o elegí uno de tus Archivos de Nube InSSA.'],
		['bajar', 'Adjuntos recibidos', 'Descargalos o guardalos directamente en tus Archivos.'],
		['buscar', 'Buscar', 'Con el buscador de la lista, por remitente, asunto o texto.'],
		['mover', 'Organizar', 'Mové mensajes a otras carpetas, marcalos como importantes, leídos o no leídos.'],
	]); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['formularios'])) { /* ============================ FORMULARIOS ============================ */ ?>
<?php $abrirSec('formularios'); ?>
	<p>Creá encuestas, inscripciones o relevamientos en pocos minutos. Compartís un enlace, la gente responde y vos ves los resultados ordenados.</p>
	<?php $tabla('Paso', 'Cómo', [
		['mas', 'Crear', 'Con el botón para crear un formulario. Poné título, una descripción y agregá las preguntas.'],
		['lista', 'Preguntas', 'Opción única, varias opciones, lista desplegable, respuesta corta o larga, fecha u hora. Cada una puede ser obligatoria.'],
		['compartir', 'Compartir', 'Con personas o grupos de Nube InSSA, o con un enlace para que responda cualquiera.'],
		['engranaje', 'Opciones', 'Respuestas anónimas, una sola respuesta por persona o fecha límite, entre otras.'],
		['grafico', 'Resultados', 'Un resumen con gráficos por pregunta y el detalle de cada respuesta.'],
		['bajar', 'Exportar', 'Pasá las respuestas a una planilla, en tus Archivos o descargada.'],
	]); ?>
	<?php $nota('Ejemplo: para una capacitación, armás el formulario de inscripción, compartís el enlace y al cerrar exportás la lista de personas anotadas.'); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['tableros'])) { /* ============================ TABLEROS ============================ */ ?>
<?php $abrirSec('tableros'); ?>
	<p>Organizá tareas y proyectos con tarjetas que movés entre columnas, por ejemplo "Pendiente", "En curso" y "Terminado".</p>
	<?php $tabla('Elemento', 'Qué hace', [
		['tablero', 'Tablero', 'Un proyecto o tema. Lo creás desde el menú izquierdo.'],
		['lista', 'Listas', 'Las columnas del tablero. Representan etapas.'],
		['documento', 'Tarjetas', 'Cada tarea. Tienen descripción, vencimiento, etiquetas, personas asignadas, comentarios y adjuntos. Se arrastran de una lista a otra.'],
		['compartir', 'Compartir', 'Con personas o grupos, para que editen o sólo vean.'],
	]); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['actividad'])) { /* ============================ ACTIVIDAD ============================ */ ?>
<?php $abrirSec('actividad'); ?>
	<p>Un registro de todo lo que pasó con tus archivos y tu cuenta. Responde preguntas como "¿quién cambió este documento?" o "¿qué me compartieron esta semana?".</p>
	<?php $sub('Qué muestra', 'actividad'); ?>
	<ul>
		<li>Archivos creados, modificados, movidos o eliminados.</li>
		<li>Lo que compartieron con vos y lo que compartiste, incluido lo de las carpetas de tu área.</li>
		<li>Comentarios en tus archivos.</li>
<?php if ($app('calendar')) { ?>
		<li>Cambios en calendarios y eventos.</li>
<?php } ?>
		<li>Eventos de seguridad de tu cuenta, como un cambio de contraseña.</li>
	</ul>
	<?php $sub('Filtros', 'filtro'); ?>
	<p>En el menú izquierdo elegís qué ver: todo, sólo lo tuyo, sólo lo de otras personas, favoritos, comparticiones, comentarios y otras categorías. Para ver la historia de un solo archivo, abrí sus detalles en Archivos y entrá a la pestaña <b>Actividad</b>.</p>
	<?php $sub('Resumen por correo', 'correo'); ?>
	<p>En tu configuración podés elegir qué movimientos querés recibir por correo y cada cuánto.</p>
<?php $cerrarSec(); ?>
<?php } ?>

<?php /* ============================ NOTIFICACIONES ============================ */ ?>
<?php $abrirSec('notificaciones'); ?>
	<p>Las notificaciones aparecen en la campana de la barra superior. Un punto sobre la campana indica que tenés avisos sin leer. Tocá un aviso para ir a lo que se refiere, o descartalo.</p>
	<?php $tabla('Aviso', 'Cuándo llega', array_values(array_filter([
		['compartir', 'Archivo compartido', 'Alguien compartió un archivo o una carpeta con vos.'],
		['comentario', 'Mención', 'Alguien escribió <code>@</code> con tu nombre en un comentario.'],
		$app('calendar') ? ['calendario', 'Eventos', 'Recordatorios y respuestas a tus invitaciones.'] : null,
		['alerta', 'Avisos de la plataforma', 'Mensajes de la administración o de tu cuenta, como poco espacio disponible.'],
	]))); ?>
	<p>Elegí qué avisos recibís, en la plataforma o por correo, desde la sección de notificaciones de tu <?php print_unescaped($enlace('configuracion', 'configuración')); ?>.</p>
<?php if ($app('user_status')) { ?>
	<?php $nota('Desde el menú de tu foto podés poner tu estado en "No molestar" para dejar de recibir avisos emergentes un rato.'); ?>
<?php } ?>
<?php $cerrarSec(); ?>

<?php /* ============================ BÚSQUEDA ============================ */ ?>
<?php $abrirSec('busqueda'); ?>
	<p>La lupa de la barra superior busca en todas las aplicaciones al mismo tiempo. Escribí parte de un nombre y los resultados aparecen agrupados por aplicación.</p>
	<?php $tabla('Qué encontrás', 'Dónde aparece', array_values(array_filter([
		['carpeta', 'Archivos y carpetas', 'Por su nombre.'],
		$app('photos') ? ['foto', 'Fotos y videos', 'Junto con los archivos.'] : null,
		$app('contacts') ? ['contacto', 'Contactos', 'Por nombre, correo o teléfono.'] : null,
		$app('calendar') ? ['calendario', 'Eventos', 'Por título.'] : null,
		$app('mail') ? ['correo', 'Mensajes', 'Por asunto o remitente.'] : null,
		$app('comments') ? ['comentario', 'Comentarios', 'Por el texto del comentario.'] : null,
	]))); ?>
	<p>Debajo del cuadro de búsqueda aparecen filtros para acotar por aplicación, fecha o persona, según la versión de la plataforma. Dentro de Archivos también podés filtrar la carpeta abierta escribiendo el nombre.</p>
<?php $cerrarSec(); ?>

<?php /* ============================ PAPELERA ============================ */ ?>
<?php $abrirSec('papelera'); ?>
	<p>Lo que eliminás no se borra enseguida: pasa a la papelera, al pie del menú izquierdo de Archivos, donde lo podés recuperar.</p>
	<?php $tabla('Acción', 'Qué pasa', [
		['restaurar', 'Restaurar', 'Vuelve a la carpeta donde estaba. Si esa carpeta ya no existe, aparece en la raíz de tus Archivos.'],
		['papelera', 'Eliminar definitivamente', 'Se borra para siempre. <b>No se puede deshacer.</b>'],
		['reloj', 'Borrado automático', 'Después de un tiempo definido por la administración, o si te quedás sin espacio, se borran solos los más viejos.'],
	]); ?>
	<?php $nota('Vaciar la papelera no se puede deshacer. Revisá bien antes de confirmar.', 'alerta'); ?>
	<p>Si borrás algo que te compartieron, sólo lo quitás de tu lista: el original sigue en la cuenta de quien lo compartió.<?php if ($app('files_versions')) { ?> Si el archivo no está borrado pero se arruinó, restaurá una versión anterior desde la pestaña <b>Versiones</b>.<?php } ?></p>
<?php $cerrarSec(); ?>

<?php if (isset($S['cursos'])) { /* ============================ CURSOS ============================ */ ?>
<?php $abrirSec('cursos'); ?>
	<p>Nube InSSA también se usa para la formación. Los cursos reúnen los materiales de estudio, documentos de apoyo y recursos educativos de cada propuesta.</p>
	<?php $sub('Si participás de un curso', 'persona'); ?>
	<ul>
		<li>Entrás desde su acceso en la barra superior<?php if (!empty($sitioCursos)) { ?> o con el botón <b>Abrir</b> de esta sección<?php } ?>.</li>
		<li>Ves los cursos disponibles para tu usuario y sus materiales.</li>
		<li>Abrís los documentos en el navegador y, si está permitido, los descargás.</li>
	</ul>
<?php if ($ayudaEsAdmin) { ?>
	<?php $sub('Si administrás cursos', 'llave', true); ?>
	<ul>
		<li>Cargás los materiales y los ordenás por curso, unidad o tema.</li>
		<li>Mientras un material se prepara, es un archivo guardado. Se vuelve visible para el curso cuando se publica. Mirá <?php print_unescaped($enlace('publicacion', 'Publicación')); ?>.</li>
		<li>El acceso depende de los usuarios y grupos de Nube InSSA.</li>
		<li>Para seguir el movimiento de los materiales, usá la <?php print_unescaped($enlace('actividad', 'actividad')); ?> y las <?php print_unescaped($enlace('estadisticas', 'estadísticas')); ?>.</li>
	</ul>
<?php } ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['editor'])) { /* ============================ EDITOR DE SITIO ============================ */ ?>
<?php $abrirSec('editor'); ?>
	<p>El Editor de sitio sirve para mantener actualizada la información institucional que se muestra al público. Lo usan las personas con permiso de edición.</p>
	<?php $tabla('Tarea', 'Para qué', [
		['editor', 'Editar contenidos', 'Modificar textos e información institucional.'],
		['enlace', 'Administrar enlaces', 'Agregar, corregir o quitar enlaces.'],
		['publicar', 'Publicar novedades', 'Dar a conocer noticias y avisos.'],
	]); ?>
	<?php $nota('Lo que se publica lo ve cualquier persona. Revisá el texto y probá cada enlace antes de guardar.', 'alerta'); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['publicacion'])) { /* ============================ PUBLICACIÓN ============================ */ ?>
<?php $abrirSec('publicacion'); ?>
	<p>En Nube InSSA se cargan documentos, libros, artículos, publicaciones, materiales educativos, novedades y recursos institucionales. Antes de empezar conviene tener clara una diferencia.</p>
	<?php $tabla('Acción', 'Qué significa', [
		['carpeta', 'Guardar un archivo', 'Queda en tu cuenta, en Archivos. Es privado hasta que lo compartís. Ideal para borradores y documentos de trabajo.'],
		['publicar', 'Publicar un contenido', 'Pasa a estar disponible para su público: un curso, el personal o el sitio. Lo hace una persona con permiso y tiene que ser la versión final.'],
	]); ?>
	<?php $sub('Cómo se hace', 'lista'); ?>
	<ol>
		<li>Subí el archivo a <?php print_unescaped($enlace('archivos', 'Archivos')); ?>, en la carpeta que corresponda y con un nombre claro.</li>
		<li>Compartilo con quien lo tenga que revisar.</li>
		<li>Cuando está listo, la persona responsable lo publica desde la herramienta que corresponde: <?php print_unescaped($enlace('cursos', 'Cursos')); ?> para materiales educativos, <?php print_unescaped($enlace('editor', 'Editor de sitio')); ?> para novedades e información institucional.</li>
	</ol>
	<?php $nota('Usá nombres que describan el contenido: "Protocolo de atención 2026.pdf" en lugar de "doc final (2).pdf". Preferí PDF para lo que sólo se lee.'); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['estadisticas'])) { /* ============================ ESTADÍSTICAS ============================ */ ?>
<?php $abrirSec('estadisticas'); ?>
	<p>Nube InSSA ofrece información sobre cómo se usa la plataforma. Lo que ves depende de tu perfil.</p>
	<?php $tabla('Dato', 'Dónde se consulta', array_values(array_filter([
		$app('activity') ? ['actividad', 'Tu actividad', 'En ' . $enlace('actividad', 'Actividad') . ': qué se creó, modificó o compartió y quién lo hizo.'] : null,
		['carpeta', 'Tu espacio', 'Al pie del menú izquierdo de Archivos.'],
		['historial', 'Movimiento de un documento', 'En la pestaña Actividad de ese archivo.'],
		!empty($sitioEstad) ? ['grafico', 'Estadísticas institucionales', 'En el módulo de estadísticas, con el botón <b>Abrir</b> de esta sección. Qué datos muestra y quién los ve lo define la administración.'] : null,
		($ayudaEsAdmin && $app('serverinfo')) ? ['llave', 'Información del sistema', 'Sólo administración. Cantidad de cuentas, cuentas activas, archivos, espacio ocupado, recursos compartidos y estado del servidor.'] : null,
	]))); ?>
<?php $cerrarSec(); ?>
<?php } ?>

<?php if (isset($S['herramientas'])) { /* ============================ HERRAMIENTAS ============================ */ ?>
<?php $abrirSec('herramientas'); ?>
	<p>Además de las aplicaciones de la nube, en la barra superior tenés estas herramientas propias de InSSA. Cada una tiene su propia sección de ayuda adentro.</p>
	<nav class="ayuda-accesos">
	<?php foreach ($ayudaSitios as $sitio) { ?>
		<a href="<?php print_unescaped($h($sitio['enlace'])); ?>"><?php print_unescaped($ico($sitio['icono'], 'web')); ?><?php print_unescaped($h($sitio['nombre'])); ?></a>
	<?php } ?>
	</nav>
<?php $cerrarSec(); ?>
<?php } ?>

<?php /* ============================ USUARIOS Y GRUPOS ============================ */ ?>
<?php $abrirSec('usuarios'); ?>
	<p>Cada persona tiene su propia cuenta. Las cuentas se agrupan por área, sector o función, para compartir fácil con todo un equipo.</p>
	<?php $tabla('Qué', 'Cómo funciona', [
		['persona', 'Tu cuenta', 'Es personal. Tus archivos son privados salvo que los compartas.'],
		['grupo', 'Grupos', 'Conjuntos de cuentas. Si compartís con un grupo, todas sus personas reciben acceso, también las que se sumen después.'],
		['ojo', 'Tus grupos', 'Los ves en tu información personal, dentro de tu configuración.'],
		['ayuda', 'Sumarte o salir', 'No se hace desde tu cuenta. Pedíselo a la administración.'],
	]); ?>
<?php if ($ayudaEsAdmin) { ?>
	<?php $sub('Gestión de cuentas', 'llave', true); ?>
	<p>Desde la página de cuentas se crean usuarios, se asignan grupos y espacio, se restablecen contraseñas y se desactivan cuentas. También se pueden designar administradores de grupo, que gestionan sólo las cuentas de su grupo.</p>
	<?php $nota('Desactivar una cuenta le impide entrar pero conserva sus archivos. Eliminarla borra sus datos y deja de compartirlos. Antes de eliminar, respaldá lo que haga falta.', 'alerta'); ?>
<?php } ?>
<?php $cerrarSec(); ?>

<?php /* ============================ CONFIGURACIÓN ============================ */ ?>
<?php $abrirSec('configuracion'); ?>
	<p>Entrás tocando tu foto o tus iniciales, arriba a la derecha, y eligiendo la opción de configuración.</p>
	<?php $tabla('Sección', 'Qué ajustás', array_values(array_filter([
		['persona', 'Información personal', 'Nombre, foto, correo, teléfono y organización. Al lado de algunos datos elegís quién puede verlos. Algunos los carga la administración y no se pueden cambiar.'],
		['web', 'Idioma y región', 'En tu información personal: el idioma y el formato de fechas. La zona horaria se toma de tu navegador' . ($app('calendar') ? '; en Calendario podés elegir otra para tus eventos' : '') . '.'],
		['escudo', 'Seguridad', 'Contraseña, dispositivos y sesiones, y contraseñas de aplicación. Mirá ' . $enlace('seguridad', 'Privacidad y seguridad') . '.'],
		['campana', 'Notificaciones', 'Qué avisos recibís y cuáles también por correo.'],
		$app('activity') ? ['actividad', 'Actividad', 'Qué movimientos te llegan en el resumen por correo y cada cuánto.'] : null,
		['compartir', 'Compartir', 'Preferencias al recibir archivos compartidos.'],
		$app('privacy') ? ['candado', 'Privacidad', 'Quién tiene acceso a tus datos y dónde se almacenan.'] : null,
		$app('theming') ? ['ojo', 'Apariencia y accesibilidad', 'Tema claro u oscuro, alto contraste y tipografía más legible.'] : null,
	]))); ?>
<?php $cerrarSec(); ?>

<?php /* ============================ PRIVACIDAD Y SEGURIDAD ============================ */ ?>
<?php $abrirSec('seguridad'); ?>
	<p>Nube InSSA está pensada para guardar y gestionar la información de la institución de forma segura. Una parte la cuida la plataforma y otra depende de cómo la usamos.</p>
	<?php $sub('Lo que hace la plataforma', 'escudo'); ?>
	<?php $tabla('Medida', 'Qué significa', array_values(array_filter([
		['candado', 'Conexión segura', 'Se accede por HTTPS: la información viaja cifrada entre tu dispositivo y Nube InSSA.'],
		['persona', 'Cuentas personales', 'Cada persona entra con su usuario. Nadie ve tus archivos salvo que los compartas.'],
		['compartir', 'Control de acceso', 'Vos decidís quién ve cada cosa y con qué permisos, y podés quitar el acceso cuando quieras.'],
		['historial', 'Registro de actividad', 'Los movimientos quedan registrados y los podés revisar.'],
		$app('password_policy') ? ['llave', 'Reglas de contraseña', 'La plataforma exige contraseñas que cumplan reglas mínimas.'] : null,
		$app('encryption') ? ['candado', 'Cifrado de archivos', 'La plataforma dispone de un módulo de cifrado de los archivos almacenados. Si está activo y cómo se aplica lo decide la administración.'] : null,
	]))); ?>
	<?php $sub('Lo que podés hacer vos', 'persona'); ?>
	<?php $tabla('Acción', 'Dónde', array_values(array_filter([
		['llave', 'Cambiar tu contraseña', 'En la sección de seguridad de tu configuración. Usá una larga y que no uses en otros sitios.'],
		$app('twofactor_totp') ? ['movil', 'Verificación en dos pasos', 'En la misma sección. Además de la contraseña se pide un código de una aplicación de tu teléfono.' . ($app('twofactor_backupcodes') ? ' Generá también los códigos de respaldo y guardalos.' : '')] : null,
		['pantalla', 'Revisar sesiones', 'En "Dispositivos y sesiones" ves dónde está abierta tu cuenta. Cerrá lo que no reconozcas.'],
		['enlace', 'Revisar lo compartido', 'En la sección Compartidos de Archivos. Quitá lo que ya no haga falta.'],
	]))); ?>
	<?php $nota('Nadie de la institución te va a pedir tu contraseña. Desconfiá de cualquier correo o mensaje que lo haga.', 'alerta'); ?>
<?php if ($ayudaEsAdmin) { ?>
	<?php $sub('Seguridad de la plataforma', 'llave', true); ?>
	<p>Desde la configuración de administración se gestionan la política de contraseñas, las reglas de compartición y la verificación en dos pasos. La página de información general muestra advertencias de seguridad y configuración cuando detecta algo para revisar.</p>
<?php } ?>
<?php $cerrarSec(); ?>

<?php /* ============================ MÓVILES ============================ */ ?>
<?php $abrirSec('moviles'); ?>
	<p>Podés usar Nube InSSA desde el teléfono o la tablet. La forma más simple es el navegador: la plataforma se adapta sola a la pantalla.</p>
	<?php $tabla('Forma', 'Cómo', array_values(array_filter([
		['web', 'Navegador', 'Entrá a <code>' . $dominio . '</code> desde el celular. Tocá el botón de tres rayas para ver el menú de cada aplicación. Agregala a la pantalla de inicio desde el menú del navegador para abrirla con un toque.'],
		['carpeta', 'Archivos (WebDAV)', 'Aplicaciones compatibles con WebDAV pueden conectarse a tus archivos.'],
		$app('calendar') ? ['calendario', 'Calendario (CalDAV)', 'Sincronizá tus calendarios con aplicaciones compatibles.'] : null,
		$app('contacts') ? ['contacto', 'Contactos (CardDAV)', 'Sincronizá tu agenda con aplicaciones compatibles.'] : null,
	]))); ?>
	<p>La dirección para conectar aplicaciones es <code><?php print_unescaped($dominio); ?>remote.php/dav</code>. Consultá con la administración qué aplicaciones se recomiendan.</p>
	<?php $nota('Para conectar una aplicación, creá una contraseña de aplicación en tu configuración de seguridad. Si perdés el teléfono, revocás sólo esa contraseña desde "Dispositivos y sesiones".'); ?>
<?php $cerrarSec(); ?>

<?php if (isset($S['administracion'])) { /* ============================ ADMINISTRACIÓN ============================ */ ?>
<?php $abrirSec('administracion'); ?>
	<p>Esta sección sólo la ven las cuentas administradoras. La administración se hace desde la configuración, en las páginas de administración.</p>
	<?php $tabla('Área', 'Qué se hace', array_values(array_filter([
		['grupo', 'Cuentas y grupos', 'Crear y editar usuarios, asignar grupos y espacio, restablecer contraseñas, desactivar cuentas y designar administradores de grupo. <a href="' . $h($urlBase('index.php/settings/users')) . '">Abrir cuentas</a>.'],
		['lista', 'Aplicaciones', 'Ver las instaladas, activarlas o desactivarlas y limitarlas a ciertos grupos. Desactivar una la quita a todas las cuentas.'],
		['ayuda', 'Información general', 'Estado de la plataforma y advertencias de configuración o seguridad.'],
		['engranaje', 'Configuración básica', 'Servidor de correo para los envíos de la plataforma, tareas en segundo plano y ajustes generales.'],
		['compartir', 'Compartir', 'Enlaces públicos, contraseñas y vencimientos obligatorios, y con quién se puede compartir.'],
		['escudo', 'Seguridad', 'Política de contraseñas y verificación en dos pasos.'],
		$app('theming') ? ['ojo', 'Apariencia', 'Nombre, logotipo y colores institucionales.'] : null,
		$app('serverinfo') ? ['grafico', 'Información del sistema', 'Uso de la plataforma y estado del servidor.'] : null,
		$app('external') ? ['web', 'Sitios externos', 'Los accesos del menú superior a las herramientas de InSSA, con su ícono y los grupos que los ven.'] : null,
		['carpeta', 'Almacenamiento', 'El espacio de cada cuenta se define en la página de cuentas. Papelera y versiones se limpian solas según la configuración.'],
	]))); ?>
	<?php $nota('Las tareas de mantenimiento técnico, como actualizaciones y copias de seguridad, las realiza el equipo técnico responsable.'); ?>
<?php $cerrarSec(); ?>
<?php } ?>

</div>
</div>

<?php if ($nonce !== '') { ?>
<script nonce="<?php p($nonce); ?>">
(function () {
	'use strict';
	var raiz = document.querySelector('.ayuda-contenido');
	if (!raiz) { return; }
	document.documentElement.classList.add('ayuda-js');

	var paneles = Array.prototype.slice.call(raiz.querySelectorAll('.ayuda-panel'));
	var accesos = raiz.querySelector('.ayuda-accesos');
	var sinRes  = document.getElementById('ayuda-sin-resultados');
	var campo   = document.getElementById('ayuda-buscar');
	var links   = Array.prototype.slice.call(document.querySelectorAll('#app-navigation .ayuda-nav__link'));

	function normalizar(t) {
		return (t || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
	}
	var VARIANTES = { a: 'aáàâä', e: 'eéèêë', i: 'iíìîï', o: 'oóòôö', u: 'uúùûü', n: 'nñ' };
	function patron(t) {
		return t.split('').map(function (c) {
			return VARIANTES[c] ? '[' + VARIANTES[c] + ']' : c.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
		}).join('');
	}

	paneles.forEach(function (p) { p._texto = normalizar(p.textContent); });

	function quitarMarcas() {
		Array.prototype.forEach.call(raiz.querySelectorAll('mark[data-ayuda]'), function (m) {
			var padre = m.parentNode;
			padre.replaceChild(document.createTextNode(m.textContent), m);
			padre.normalize();
		});
	}

	function marcar(nodo, regex) {
		var w = document.createTreeWalker(nodo, NodeFilter.SHOW_TEXT, null);
		var lista = [];
		while (w.nextNode()) {
			var n = w.currentNode;
			if (n.nodeValue.trim() && !n.parentNode.closest('svg, mark')) { lista.push(n); }
		}
		lista.forEach(function (n) {
			regex.lastIndex = 0;
			if (!regex.test(n.nodeValue)) { return; }
			regex.lastIndex = 0;
			var frag = document.createDocumentFragment(), texto = n.nodeValue, ultimo = 0, m;
			while ((m = regex.exec(texto)) !== null) {
				frag.appendChild(document.createTextNode(texto.slice(ultimo, m.index)));
				var mk = document.createElement('mark');
				mk.setAttribute('data-ayuda', '');
				mk.textContent = m[0];
				frag.appendChild(mk);
				ultimo = m.index + m[0].length;
			}
			frag.appendChild(document.createTextNode(texto.slice(ultimo)));
			n.parentNode.replaceChild(frag, n);
		});
	}

	function buscar() {
		quitarMarcas();
		var terminos = normalizar(campo.value.trim()).split(/\s+/).filter(function (t) { return t.length >= 2; });
		var visibles = 0;
		paneles.forEach(function (p) {
			var ok = terminos.every(function (t) { return p._texto.indexOf(t) !== -1; });
			p.classList.toggle('ayuda-oculto', !ok);
			if (ok) { visibles++; }
		});
		links.forEach(function (a) {
			var p = document.getElementById(a.getAttribute('data-destino'));
			a.parentNode.classList.toggle('ayuda-oculto', !!p && p.classList.contains('ayuda-oculto'));
		});
		accesos.classList.toggle('ayuda-oculto', terminos.length > 0);
		sinRes.classList.toggle('ayuda-oculto', visibles > 0);
		if (terminos.length) {
			var regex = new RegExp(terminos.map(patron).join('|'), 'gi');
			paneles.forEach(function (p) { if (!p.classList.contains('ayuda-oculto')) { marcar(p, regex); } });
		}
	}

	var espera;
	campo.addEventListener('input', function () { clearTimeout(espera); espera = setTimeout(buscar, 150); });
	campo.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { campo.value = ''; buscar(); }
	});

	// Marca en el menú la sección que se está leyendo.
	function marcarActivo(id) {
		links.forEach(function (a) { a.classList.toggle('active', a.getAttribute('data-destino') === id); });
	}
	var bloqueo = 0;
	links.forEach(function (a) {
		a.addEventListener('click', function () { marcarActivo(a.getAttribute('data-destino')); bloqueo = Date.now() + 800; });
	});
	var pendiente = false;
	document.addEventListener('scroll', function () {
		if (pendiente || Date.now() < bloqueo) { return; }
		pendiente = true;
		requestAnimationFrame(function () {
			pendiente = false;
			var actual = '';
			paneles.forEach(function (p) {
				if (!p.classList.contains('ayuda-oculto') && p.getBoundingClientRect().top <= 140) { actual = p.id; }
			});
			marcarActivo(actual);
		});
	}, true);
})();
</script>
<?php } ?>
