<?php
if (!is_file("model/" . $p . ".php")) {

  echo "Falta definir la clase " . $p;
  exit;
}

require_once("model/" . $p . ".php");

if (is_file("vista/" . $p . ".php")) {
  $o = new deudacondominio();

  if (!empty($_POST)) {
    $accion = $_POST['accion'];
    if ($accion == 'listado_deudas') {
      $respuesta = $o->listadodeudas();
      echo json_encode($respuesta);
    } elseif ($accion == 'incluir') {
      $respuesta = $o->incluir($_POST['monto'], $_POST['concepto'], $_POST['fecha']);
      echo json_encode($respuesta);
    } elseif ($accion == 'distribuir_deuda') {
      $respuesta = $o->distribuir_deuda($_POST['id_deuda']);
      echo json_encode($respuesta);
    } elseif ($accion == 'calculo_deuda') {
      $respuesta = $o->calcular_deuda($_POST['id_deuda']);
      echo json_encode($respuesta);
    } elseif ($accion == 'modificar') {
      $respuesta = $o->modificar($_POST['id_deuda'], $_POST['monto'], $_POST['concepto'], $_POST['fecha']);
      echo json_encode($respuesta);
    } elseif ($accion == 'eliminar') {
      $respuesta = $o->eliminar($_POST['id_deuda']);
      echo json_encode($respuesta);
    }
    exit;
  }
  require_once("vista/" . $p . ".php");
} else {
  echo "pagina en construccion";
}
