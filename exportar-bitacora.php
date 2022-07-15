<?php
// Require database connection and PHPWord library
require './config/db_connect.php';
require './lib/phpword/vendor/autoload.php';
require './includes/functions.php';

check_login();

$fmt = set_date_format();

$bitacora = [
    'acreditado_nombre' => '',
    'acreditado_folio' => '',
    'acreditado_municipio' => '',
    'acreditado_garantia' => '',
    'acreditado_telefono' => '',
    'acreditado_email' => '',
    'acreditado_direccion_negocio' => '',
    'acreditado_direccion_particular' => '',
    'aval_nombre' => '',
    'aval_telefono' => '',
    'aval_email' => '',
    'aval_direccion' => '',
    'gestion_fecha1' => '',
    'gestion_via1' => '',
    'gestion_comentarios1' => '',
    'gestion_contador' => '',
    'evidencia_fecha1' => '',
    'evidencia_fotografia1' => '',
];
$errores = [
    'acreditado_nombre' => '',
    'acreditado_folio' => '',
    'acreditado_municipio' => '',
    'acreditado_garantia' => '',
    'acreditado_telefono' => '',
    'acreditado_email' => '',
    'acreditado_direccion_negocio' => '',
    'acreditado_direccion_particular' => '',
    'aval_nombre' => '',
    'aval_telefono' => '',
    'aval_email' => '',
    'aval_direccion' => '',
    'gestion_fecha1' => '',
    'gestion_via1' => '',
    'gestion_comentarios1' => '',
    'evidencia_fecha1' => '',
    'evidencia_fotografia1' => '',
];

$tipos_gestion = ['Correo electrónico', 'Llamada telefónica', 'Visita'];

$filtros = [];

$movido = false;
$ruta_subido = './uploads/';
$tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/webp'];
$exts_permitidas = ['jpeg', 'jpg', 'jpe', 'jif', 'jfif', 'png', 'gif', 'bmp', 'tif', 'tiff', 'webp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Setting filter settings
    $filtros['acreditado_nombre']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_nombre']['options']['regexp'] = '/^[A-zÀ-ÿ ]+$/';
    $filtros['acreditado_folio']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_folio']['options']['regexp'] = '/(^IYE{1,1})([\d\-]+$)/';
    $filtros['acreditado_municipio']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_municipio']['options']['regexp'] = '/[\s\S]+/';
    $filtros['acreditado_garantia']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_garantia']['options']['regexp'] = '/[\s\S]+/';
    $filtros['acreditado_telefono']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_telefono']['options']['regexp'] = '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/';
    $filtros['acreditado_email']['filter'] = FILTER_VALIDATE_EMAIL;
    $filtros['acreditado_direccion_negocio']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['acreditado_direccion_negocio']['options']['regexp'] = '/[\s\S]+/';
    $filtros['acreditado_direccion_particular']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_nombre']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_nombre']['options']['regexp'] = '/^[A-zÀ-ÿ ]+$/';
    $filtros['aval_telefono']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_telefono']['options']['regexp'] = '/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/';
    $filtros['aval_email']['filter'] = FILTER_VALIDATE_EMAIL;
    $filtros['aval_direccion']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['aval_direccion']['options']['regexp'] = '/[\s\S]+/';
    $filtros['aval_direccion']['options']['default'] = '';
    $filtros['gestion_fecha1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['gestion_fecha1']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['gestion_via1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['gestion_via1']['options']['regexp'] = '/^(Correo electrónico|Llamada telefónica|Visita)+$/';
    $filtros['gestion_comentarios1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['gestion_comentarios1']['options']['regexp'] = '/[\s\S]+/';
    $filtros['gestion_comentarios1']['options']['default'] = '';
    $filtros['evidencia_fecha1']['filter'] = FILTER_VALIDATE_REGEXP;
    $filtros['evidencia_fecha1']['options']['regexp'] = '/^[\d\-]+$/';
    $filtros['evidencia_fecha1']['options']['default'] = '';

    $bitacoras = filter_input_array(INPUT_POST, $filtros);

    if ($_FILES['evidencia_fotografia1']['error'] === 0) {
        $tipo = mime_content_type($_FILES['evidencia_fotografia']['type']);
        $errores['evidencia_fotografia1'] .= in_array($tipo, $tipos_permitidos) ? '' : 'Formato de archivo incorrecto. ';
        $ext = strtolower(pathinfo($_FILES['evidencia_fotografia']['name'], PATHINFO_EXTENSION));
        $errores['evidencia_fotografia1'] .= in_array($ext, $exts_permitidas);

        if (!$errores) {
            $fotografia_nombre_archivo = create_filename($_FILES['evidencia_fotografia1']['name'], $ruta_subido);
            $destino = $ruta_subido . $fotografia_nombre_archivo;
            $movido = move_uploaded_file($_FILES['evidencia_fotografia1']['tmp_name'], $destino);
        }
    }

    if ($movido === true) {
        $bitacora['evidencia_fotografia1'] = $_FILES['evidencia_fotografia1']['name'] ?? '';
    }

    // Escribir mensajes de error
// Assign post received inputs to variables
    $acreditado_nombre = $_POST['acreditado_nombre'];
    $folio = $_POST['folio'];
    $municipio = $_POST['municipio'];
    $garantia = $_POST['garantia'];
    $acreditado_telefono = $_POST['acreditado_telefono'];
    $acreditado_email = $_POST['acreditado_email'];
    $direccion_negocio = $_POST['direccion_negocio'];
    $direccion_particular = $_POST['direccion_particular'];
    $aval_nombre = $_POST['aval_nombre'];
    $aval_telefono = $_POST['aval_telefono'];
    $aval_direccion = $_POST['aval_direccion'];
    $aval_email = $_POST['aval_email'];
    $gestion_fecha = $_POST['gestion_fecha'];
    $gestion_via = $_POST['gestion_via'];
    $gestion_comentarios = $_POST['gestion_comentarios'];
    $evidencia_fecha = $_POST['evidencia_fecha'];
    $evidencia_fotografia = $_FILES['evidencia_fotografia']['name'];

    $generacion_invalida = implode($errores);

    if (!$generacion_invalida && $movido) {

// Create variable with filename
        $nombre_archivo = $folio . ' ' . $acreditado_nombre . ' - Bitácora.docx';
// Encode filename so that UTF-8 characters work
        $nombre_archivo_decodificado = rawurlencode($nombre_archivo);

// Create new instance of PHPWord template processor using the required template file
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('./plantillas/plantilla-bitacora.docx');

// Set values in template with post received input variables
        $templateProcessor->setValue('acreditado_nombre', $acreditado_nombre);
        $templateProcessor->setValue('folio', $folio);
        $templateProcessor->setValue('municipio', $municipio);
        $templateProcessor->setValue('garantia', $garantia);
        $templateProcessor->setValue('acreditado_telefono', $acreditado_telefono);
        $templateProcessor->setValue('acreditado_email', $acreditado_email);
        $templateProcessor->setValue('direccion_negocio', $direccion_negocio);
        $templateProcessor->setValue('direccion_particular', $direccion_particular);
        $templateProcessor->setValue('aval_nombre', $aval_nombre);
        $templateProcessor->setValue('aval_telefono', $aval_telefono);
        $templateProcessor->setValue('aval_email', $aval_email);
        $templateProcessor->setValue('aval_direccion', $aval_direccion);
        $templateProcessor->setValue('gestion_fecha', date("d-m-Y", strtotime($gestion_fecha)));
        $templateProcessor->setValue('gestion_via', $gestion_via);
        $templateProcessor->setValue('gestion_comentarios', $gestion_comentarios);
        $templateProcessor->setValue('evidencia_fecha', datefmt_format($fmt, date_add(date_create($evidencia_fecha), date_interval_create_from_date_string('1 day'))));
        $templateProcessor->setImageValue('evidencia_fotografia', array('path' => './uploads/' . $evidencia_fotografia, 'width' => 1200));

// Escape strings to insert into the database table
        $acreditado_nombre = mysqli_real_escape_string($conn, $_POST['acreditado_nombre']);
        $folio = mysqli_real_escape_string($conn, $_POST['folio']);
        $municipio = mysqli_real_escape_string($conn, $_POST['municipio']);
        $garantia = mysqli_real_escape_string($conn, $_POST['garantia']);
        $acreditado_telefono = mysqli_real_escape_string($conn, $_POST['acreditado_telefono']);
        $acreditado_email = mysqli_real_escape_string($conn, $_POST['acreditado_email']);
        $direccion_negocio = mysqli_real_escape_string($conn, $_POST['direccion_negocio']);
        $direccion_particular = mysqli_real_escape_string($conn, $_POST['direccion_particular']);
        $aval_nombre = mysqli_real_escape_string($conn, $_POST['aval_nombre']);
        $aval_telefono = mysqli_real_escape_string($conn, $_POST['aval_telefono']);
        $aval_email = mysqli_real_escape_string($conn, $_POST['aval_email']);
        $aval_direccion = mysqli_real_escape_string($conn, $_POST['aval_direccion']);
        $gestion_fecha = mysqli_real_escape_string($conn, $_POST['gestion_fecha']);
        $gestion_via = mysqli_real_escape_string($conn, $_POST['gestion_via']);
        $gestion_comentarios = mysqli_real_escape_string($conn, $_POST['gestion_comentarios']);
        $evidencia_fecha = mysqli_real_escape_string($conn, $_POST['evidencia_fecha']);
        $evidencia_fotografia = mysqli_real_escape_string($conn, $_FILES['evidencia_fotografia']['name']);

// Query
        $sql = "INSERT INTO bitacora(acreditado_nombre, folio, municipio, garantia, acreditado_telefono, acreditado_email,
                     direccion_negocio, direccion_particular, aval_nombre, aval_telefono, aval_email, aval_direccion,
                     gestion_fecha, gestion_via, gestion_comentarios, evidencia_fecha, evidencia_fotografia,
                     nombre_archivo) VALUES('$acreditado_nombre', '$folio', '$municipio', '$garantia', '$acreditado_telefono', '$acreditado_email',
                                            '$direccion_negocio', '$direccion_particular', '$aval_nombre', '$aval_telefono', '$aval_email', '$aval_direccion', '$gestion_fecha',
                                            '$gestion_via', '$gestion_comentarios', '$evidencia_fecha', '$evidencia_fotografia', '$nombre_archivo');";

// Validation of query
        if (mysqli_query($conn, $sql)) {

            // Path where generated file is saved
            $ruta_guardado = './files/bitacoras/' . $nombre_archivo;
            $templateProcessor->saveAs($ruta_guardado);

            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . "$nombre_archivo_decodificado" . '"');
            header('Content-Transfer-Encoding: binary');

            ob_clean();
            flush();
            // Send generated file stored in the server to the browser
            readfile($ruta_guardado);
            exit;
        } else {
            echo 'Error de consulta: ' . mysqli_error($conn);
        }
    }
}