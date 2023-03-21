<?php

require_once('model/datos.php');

class deudacondominio extends datos
{
	function chequearpermisos()
	{
		$id_rol = $_SESSION['rol'];
		$modulo = $_GET['p'];
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$guarda = $co->query("SELECT * FROM `roles_modulos` inner join `modulos` on roles_modulos.id_modulo = modulos.id inner join `roles` on roles_modulos.id_rol = roles.id where modulos.nombre = '$modulo' and roles_modulos.id_rol = '$id_rol'");
		$guarda->execute();
		$fila = array();
		$fila = $guarda->fetch(PDO::FETCH_NUM);
		return $fila;
	}
	function incluir($monto, $concepto, $fecha)
	{
		$usuario = $_SESSION['id_usuario'];
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$r = array();
		try {
			$guarda = $co->query("insert into deuda_condominio(fecha_generada,monto,concepto,usuario) 
		   values ('$fecha','$monto','$concepto',$usuario)");
			$r['resultado'] = 'incluir';
			$r['mensaje'] =  "Registro Incluido";
		} catch (Exception $e) {
			$r['resultado'] = 'error';
			$r['mensaje'] =  $e->getMessage();
		}
		return $r;
	}
	function listadodeudas()
	{
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$r = array();
		try {
			$resultado = $co->query("Select DISTINCT deuda_condominio.id, fecha_generada, monto, concepto, razon_social, dp.id_deuda_condominio
			from 
			deuda_condominio
            LEFT JOIN deuda_pendiente dp ON dp.id_deuda_condominio = deuda_condominio.id
			inner join datos_usuarios where deuda_condominio.usuario=datos_usuarios.id ORDER BY deuda_condominio.id DESC;");
			$respuesta = '';
			if ($resultado) {
				foreach ($resultado as $r) {
					if (!$r[5]) {
						$respuesta = $respuesta . "<tr style='cursor:pointer' onclick='colocadeuda(this);'>";
					} else {
						$respuesta = $respuesta . "<tr data-toggle='tooltip' data-placement='top' title='' data-original-title='Las deudas distribuidas no pueden ser modificadas ni eliminadas'>";
					}
					$respuesta = $respuesta . "<td style='display:none'>";
					$respuesta = $respuesta . $r[0];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$fecha_original = $r[1];
					$nuevo_formato = "d-m-Y";
					$fecha_cambiada = date($nuevo_formato, strtotime($fecha_original));
					$respuesta = $respuesta . $fecha_cambiada;
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$respuesta = $respuesta . $r[2];
					$respuesta = $respuesta . "</td>";
					$respuesta = $respuesta . "<td class='align-middle'>";
					$respuesta = $respuesta . $r[3];
					$respuesta = $respuesta . "</td>";
					if (!$r[5]) {
						$respuesta = $respuesta . "<td class='align-middle'>";
						$respuesta = $respuesta . $r[4];
						$respuesta = $respuesta . "</td>";
						$respuesta = $respuesta . "<td align='center' class='align-middle'>";
						$respuesta = $respuesta . "0";
						$respuesta = $respuesta . "</td>";
					} else {
						$respuesta = $respuesta . "<td class='align-middle'>";
						$respuesta = $respuesta . $r[4];
						$respuesta = $respuesta . "</td>";
						$respuesta = $respuesta . "<td align='center' class='align-middle'>1</td>";
					}
					$respuesta = $respuesta . "</tr>";
				}
			}
			$r['resultado'] = 'listado_deudas';
			$r['mensaje'] =  $respuesta;
		} catch (Exception $e) {
			$r['resultado'] = 'error';
			$r['mensaje'] =  $e->getMessage();
		}
		return $r;
	}
	function modificar($id_deuda, $monto, $concepto, $fecha)
	{
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if ($this->existe($id_deuda, 1)) {
			try {
				$co->query("Update deuda_condominio set 
						fecha_generada = '$fecha',
						monto = '$monto',
						concepto = '$concepto'
						where
						id = '$id_deuda'
						");
				$r['resultado'] = 'modificar';
				$r['mensaje'] =  "Registro modificado correctamente";
			} catch (Exception $e) {
				$r['resultado'] = 'error';
				$r['mensaje'] =   $e->getMessage();
				return $r;
			}
		} else {
			$r['resultado'] = 'error';
			$r['mensaje'] =  "La deuda especificada no existe";
		}
		return $r;
	}
	function calcular_deuda($id_deuda_condominio)
	{
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$r = array();
		try {
			$resultado = $co->query("Select t.id_tipo_apartamento,t.alicuota, t.descripcion
			from 
			tipo_apartamento t
			");
			$consulta = $co->query("Select d.monto, d.fecha_generada
			from 
			deuda_condominio d
			where id = '$id_deuda_condominio'
			");
			$consulta->execute();
			$deuda = $consulta->fetch(PDO::FETCH_NUM);
			$fecha = $deuda[1];
			$nueva_fecha = date('d-m-Y', strtotime($fecha . ' + 1 month'));
			$respuesta = '';
			$total = 0;
			if ($resultado) {
				$respuesta =  "¿Está seguro que desea distribuir esta deuda?<br>";
				$respuesta =  $respuesta . "Distribuida de la siguiente forma:<br>";
				$respuesta =  $respuesta . "Fecha tope para el pago: <b>" . $nueva_fecha . "</b><br>";
				foreach ($resultado as $r) {
					$total = $deuda[0] * ($r[1] / 100);
					$apartamentos = $co->query("Select id_apartamento
					from 
					apartamento where tipo_apartamento = '$r[0]'
					");
					if ($apartamentos) {
						$num_rows = $apartamentos->rowCount();
						$monto_por_apto = round($total / $num_rows, 2);
						$respuesta = $respuesta . "<b>" . $monto_por_apto . "$</b> por Apartamento (" . $num_rows . ") tipo " . $r[2] . "<br>";
					}
				}
				$respuesta =  $respuesta . "<span class='text-danger font-weight-bold'>LA DEUDA NO PODRÁ SER MODIFICADA NI ELIMINADA LUEGO DE SER DISTRIBUIDA</span>";
			}
			$r['resultado'] = 'calculo_deuda';
			$r['id'] = $id_deuda_condominio;
			$r['mensaje'] =  $respuesta;
		} catch (Exception $e) {
			$r['resultado'] = 'error';
			$r['mensaje'] =  $e->getMessage();
		}
		return $r;
	}
	function distribuir_deuda($id_deuda_condominio)
	{
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$r = array();
		try {
			$resultado = $co->query("Select t.id_tipo_apartamento,t.alicuota
			from 
			tipo_apartamento t
			");
			$consulta = $co->query("Select d.monto, d.fecha_generada
			from 
			deuda_condominio d
			where id = '$id_deuda_condominio'
			");
			$consulta->execute();
			$deuda = $consulta->fetch(PDO::FETCH_NUM);
			$fecha = $deuda[1];
			$nueva_fecha = date('Y-m-d', strtotime($fecha . ' + 1 month'));
			if ($resultado && !$this->existe($id_deuda_condominio, 2)) {
				foreach ($resultado as $r) {
					$total = $deuda[0] * ($r[1] / 100);
					$apartamentos = $co->query("Select id_apartamento
					from 
					apartamento where tipo_apartamento = '$r[0]'
					");
					if ($apartamentos) {
						$num_rows = $apartamentos->rowCount();
						$monto_por_apto = round($total / $num_rows, 2);
						foreach ($apartamentos as $a) {
							$guarda = $co->query("insert into deuda_pendiente(id_apartamento,id_deuda_condominio,fecha_a_cancelar,total) 
		   					values ('$a[0]','$id_deuda_condominio','$nueva_fecha','$monto_por_apto')");
						}
					}
				}
				$r['resultado'] = 'distribuir_deuda';
				$r['mensaje'] =  'La deuda se ha distruibuido exitosamente';
			} else if ($this->existe($id_deuda_condominio, 2)) {
				$r['resultado'] = 'distribuir_deuda';
				$r['mensaje'] =  'La deuda ya fue distribuida';
			}
		} catch (Exception $e) {
			$r['resultado'] = 'error';
			$r['mensaje'] =  $e->getMessage();
		}
		return $r;
	}
	function eliminar($id_deuda)
	{
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if ($this->existe($id_deuda, 1)) {
			try {
				$co->query("delete from deuda_condominio 
						where
						id = '$id_deuda'
						");
				$r['resultado'] = 'eliminar';
				$r['mensaje'] =  "Registro Eliminado";
			} catch (Exception $e) {
				$r['resultado'] = 'error';
				if ($e->getCode()=='23000') {
					$r['mensaje'] =  "Esta deuda no puede ser eliminada.";
				}else{
					$r['mensaje'] =  $e->getMessage();					
				}
			}
		} else {
			$r['resultado'] = 'error';
			$r['mensaje'] =  "Debe seleccionar una deuda para eliminarla";
		}
		return $r;
	}
	private function existe($id_deuda, $caso)
	{
		$co = $this->conecta();
		$co->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		try {
			switch ($caso) {
				case 1:
					$resultado = $co->query("Select * from deuda_condominio where id='$id_deuda'");
					$fila = $resultado->fetchAll(PDO::FETCH_BOTH);
					if ($fila) {
						return true;
					} else {
						return false;
					}
					break;
				case 2:
					$resultado = $co->query("SELECT dp.id_deuda_condominio FROM deuda_condominio dc INNER JOIN deuda_pendiente dp WHERE '$id_deuda' = dp.id_deuda_condominio;");
					$fila = $resultado->fetchAll(PDO::FETCH_BOTH);
					if ($fila) {
						return true;
					} else {
						return false;
					}
					break;
				default:
					break;
			}
		} catch (Exception $e) {
			return false;
		}
	}
}
