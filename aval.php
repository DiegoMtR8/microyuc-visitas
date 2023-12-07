<?php
require './config/db_connect.php';
require './includes/functions.php';

$sidebar_active = 'carta aval';
$header_title = 'Panel de cartas de Avales';

require './includes/header.php';

check_login();

// Write query for all acreditados
$sql = 'SELECT id, nombre_cliente, nombre_aval, numero_expediente, fecha_creacion, fecha_visita, monto_inicial, mensualidades_vencidas, adeudo_total, nombre_archivo FROM aval ORDER BY id DESC;';

// make query and & get result
$result = mysqli_query($conn, $sql);

// Fetch the resulting rows as an array
$aval = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free result from memory
mysqli_free_result($result);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $resultado = mysqli_query($conn, "SELECT nombre_archivo FROM aval WHERE id = '$id';");
    $filename = $resultado->fetch_array()['nombre_archivo'] ?? '';
    $delete = mysqli_query($conn, "DELETE FROM aval WHERE id = '$id';");
    unlink('./files/aval/' . $filename);
    header('Location: aval.php');
}
?>
<div class="main__app">
    <div class="main__header">
        <div>
            <h1 class="main__title">Cartas de Avales</h1>
            <span class="main__subtitle"><?php
                $dash_aval_query = "SELECT * FROM aval";
                $dash_aval_query_run = mysqli_query($conn, $dash_aval_query);

                if ($aval_total = mysqli_num_rows($dash_aval_query_run)) {
                    echo $aval_total . ' aval';
                } else {
                    echo "Sin datos";
                }
                ?></span>
        </div>
       <div class="main__btnContainer">
        
            <a href="generador-carta-aval.php" class="main__btn main__btn--main">
                <svg xmlns="http://www.w3.org/2000/svg" class="main__icon" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Nueva carta
            </a>
        </div>
    </div>
    <table class="table">
        <thead class="table__head">
        <tr class="table__row--head">
            <th scope="col" class="table__head">
                Acreditado
            </th>
            <th scope="col" class="table__head table__data--left">
                Folio
            </th>
            <th scope="col" class="table__head table__data--left">
                Monto inicial
            </th>
            <th scope="col" class="table__head table__head--width">
                Mensualidades vencidas
            </th>
            <th scope="col" class="table__head table__data--left">
                Adeudo total
            </th>
            <th scope="col" class="table__head">
                Fecha de creación
            </th>
            <th scope="col" class="table__head">
                Fecha de visita
            </th>
            <th scope="col" colspan="3" class="table__head">
                Acciones
            </th>
        </tr>
        </thead>
        <tbody class="table__body">
        <?php foreach ($aval as $aval): ?>
            <tr class="table__row--body">
                <td class="table__data table__data--bold"><?= $aval['nombre_cliente'] ?></td>
                <td class="table__data table__data--left"><?= $aval['numero_expediente'] ?></td>
                <td class="table__data table__data--left"><?= '$' . number_format($aval['monto_inicial'], 2); ?></td>
                <td class="table__data"><?= $aval['mensualidades_vencidas']; ?></td>
                <td class="table__data table__data--left"><?= '$' . number_format($aval['adeudo_total'], 2); ?></td>
                <td class="table__data"><?= date("d-m-Y", strtotime($aval['fecha_creacion'])); ?></td>
                <td class="table__data"><?= $aval['fecha_visita'] ? date("d-m-Y", strtotime($aval['fecha_visita'])) : ''; ?></td>
                <?php if (file_exists('./files/aval/' . $aval['nombre_archivo'])): ?>
                    <td class="table__data"><a class="table__data--link"
                                               href="./files/aval/<?= $aval['nombre_archivo'] ?>">Descargar</a>
                    </td>
                <?php else: ?>
                    <td class="table__data"><a class="table__data--nolink">Descargar</a>
                    </td>
                <?php endif; ?>
                <td class="table__data"><a class="table__data--green"
                                           href="agregar-fecha-aval.php?id=<?= $aval['id'] ?>">Agregar fecha</a>
                </td>
                <td class="table__data"><a class="table__data--red"
                                           href="aval.php?id=<?= $aval['id'] ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>
</div>
</body>
</html>
