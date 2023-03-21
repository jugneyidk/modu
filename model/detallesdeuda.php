<?php

require_once('model/datos.php');
class detallesdeuda extends datos
{
    function listadodeudas()
    {
        $co = $this->conecta();
        $co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $usuario = $_SESSION['id_habitante'];
        try {
            $resultado = $co->query("SELECT DISTINCT dp.*, a.num_letra_apartamento, a.torre, a.piso FROM deuda_pendiente dp JOIN apartamento a ON dp.id_apartamento = a.id_apartamento JOIN habitantes h ON a.propietario = h.id LEFT JOIN pago p ON dp.id = p.deuda WHERE a.propietario = '$usuario' AND (dp.id NOT IN (SELECT deuda from pago WHERE estado = 'confirmado' OR estado = 'pendiente')) ORDER BY dp.id DESC;");
            $respuesta = '';
            if ($resultado) {
                foreach ($resultado as $r) {
                    $respuesta = $respuesta . "<tr>";
                    $respuesta = $respuesta . "<td style='display:none'>";
                    $respuesta = $respuesta . $r[0];
                    $respuesta = $respuesta . "</td>";
                    $respuesta = $respuesta . "<td class='align-middle font-weight-bold'>";
                    $respuesta = $respuesta . $r[5];
                    $respuesta = $respuesta . "</td>";
                    $respuesta = $respuesta . "<td class='d-none d-md-table-cell align-middle'>";
                    $respuesta = $respuesta . $r[6];
                    $respuesta = $respuesta . "</td>";
                    $respuesta = $respuesta . "<td class='align-middle'>";
					$fecha_original = $r[3];
                    $nuevo_formato = "d-m-Y";
                    $fecha_cambiada = date($nuevo_formato, strtotime($fecha_original));
                    $respuesta = $respuesta . $fecha_cambiada;
                    $respuesta = $respuesta . "</td>";
                    $respuesta = $respuesta . "<td class='align-middle'>";
                    $respuesta = $respuesta . $r[4];
                    $respuesta = $respuesta . "$</td>";
                    $respuesta = $respuesta . "<td class='align-middle'>";
                    $respuesta = $respuesta . "<button class='btn btn-success' style='font-size: 13px;' onclick='mostrar_registrar_pago(this)'>Pagar</button>";
                    $respuesta = $respuesta . "</td>";
                    $respuesta = $respuesta . "</tr>";
                }
                $r['resultado'] = 'listadodeudas';
                $r['mensaje'] =  $respuesta;
            }
        } catch (Exception $e) {
            $r['resultado'] = 'error';
            $r['mensaje'] =  $e->getMessage();
        }
        return $r;
    }
    function historialpagos()
	{
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$usuario = $_SESSION['id_habitante'];
		$r = array();
		try {
			$resultado = $co->query("SELECT DISTINCT p.id_pago, a.num_letra_apartamento, a.torre, a.piso, p.* FROM deuda_pendiente dp JOIN apartamento a ON dp.id_apartamento = a.id_apartamento JOIN habitantes h ON a.propietario = h.id LEFT JOIN pago p ON dp.id = p.deuda WHERE a.propietario = '$usuario' AND (dp.id IN (SELECT deuda from pago WHERE estado = 'confirmado' OR estado = 'pendiente' OR estado = 'declinado')) ORDER BY p.id_pago DESC;");
			$respuesta = '';
			if ($resultado) {
				foreach ($resultado as $r) {
					$respuesta = $respuesta . "<tr>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$respuesta = $respuesta . $r[0];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle font-weight-bold'>";
					$respuesta = $respuesta . $r[1];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='d-none d-lg-table-cell align-middle'>";
					$respuesta = $respuesta . $r[2];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='d-none d-lg-table-cell align-middle'>";
					$respuesta = $respuesta . $r[3];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$fecha_original = $r[6];
                    $nuevo_formato = "d-m-Y";
                    $fecha_cambiada = date($nuevo_formato, strtotime($fecha_original));
					$respuesta = $respuesta . $fecha_cambiada;
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$respuesta = $respuesta . $r[5];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$respuesta = $respuesta . $r[7];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$respuesta = $respuesta . $r[8];
					$respuesta = $respuesta . "$</td>";
					$respuesta = $respuesta . "<td class='align-middle text-capitalize'>";
					$respuesta = $respuesta . $r[11];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "</tr>";
				}
			}
			$r['resultado'] = 'historialpagos';
			$r['mensaje'] =  $respuesta;
		} catch (Exception $e) {
			$r['resultado'] = 'error';
			$r['mensaje'] =  $e->getMessage();
		}
		return $r;
	}
    function registrarpago($id_deuda,$monto,$referencia,$tipo_pago){
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$r = array();
		$fecha = date('Y-m-d');
		$monto = trim($monto,'$');
		try {
			$guarda = $co->query("insert into pago(referencia,fecha_entrega,tipo_pago,total,deuda,id_usuario,estado) 
		   values ('$referencia','$fecha','$tipo_pago','$monto',$id_deuda,NULL,'pendiente')");
			$r['resultado'] = 'registrado';
			$r['mensaje'] =  "Pago Registrado";
		} catch (Exception $e) {
			$r['resultado'] = 'error';
			$r['mensaje'] =  $e->getMessage();
		}
		return $r;
	}
}
