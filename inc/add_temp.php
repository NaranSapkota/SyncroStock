<?php
session_start();
if (!isset($_SESSION['temp_numbers'])) {
    $_SESSION['temp_numbers'] = [];
}

if (isset($_POST['number'])) {
    $number = $_POST['number'];
    $_SESSION['temp_numbers'][] = $number;
}

// Mostrar los inputs en filas separadas con opción de eliminar
foreach ($_SESSION['temp_numbers'] as $index => $num) {
    echo "<div class='row' style='margin-bottom: 10px;'>";
    echo "<input type='number' value='$num' name='numbers[]' id='num_$index'>";
    echo "<input type='text' value='1' name='default[]' id='default_$index' style='margin-left: 10px;'>";
    echo "<button type='button' class='btn-delete' data-index='$index'>Eliminar</button>";
    echo "</div>";
}
?>

<script>
// Script para eliminar la fila correspondiente al botón presionado
$(document).ready(function() {
    $('.btn-delete').click(function() {
        var index = $(this).data('index');
        // Eliminar la fila del DOM y también del array PHP de sesión
        $(this).closest('.row').remove();
        // Aquí podrías agregar una llamada AJAX para eliminar el registro del servidor si es necesario
    });
});
</script>

