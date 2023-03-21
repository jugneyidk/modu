<?php require_once('comunes/head.php'); ?>

<body class="bg-light">
	<?php require_once("comunes/carga.php"); ?>
	<?php require_once("comunes/modal.php"); ?>
	<?php require_once('comunes/menu.php'); ?>
	<div class="container-lg bg-white p-2 p-sm-4 p-md-5 mb-5">
		<?php require_once('comunes/cabecera_modulos.php'); ?>
		<div>
			<h2 class="text-center h2 text-primary">Deuda del condominio</h2>
			<hr />
		</div>
		<form method="post" action="" id="f">
			<input type="text" name="accion" id="accion" style="display:none" />
			<input autocomplete="off" type="text" class="d-none" name="id_deuda" id="id_deuda">
			<div class="container">
				<div class="row mb-3">
					<div class="col-8 col-md-8">
						<label for="fecha">Fecha de la deuda</label>
						<input autocomplete="off" type="date" class="form-control" name="fecha" id="fecha" style="-webkit-appearance: none;-moz-appearance: none;">
						<span id="sfecha" class="text-danger"></span>
					</div>
					<div class="col-4 col-md-4">
						<label for="monto">Monto</label>
						<input autocomplete="off" type="text" class="form-control" name="monto" id="monto">
						<span id="smonto" class="text-danger"></span>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col">
						<label for="concepto">Concepto</label>
						<input autocomplete="off" type="text" class="form-control" name="concepto" id="concepto">
						<span id="sconcepto" class="text-danger"></span>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<hr />
					</div>
				</div>
				<div class="row justify-content-center">
					
						<div class="col-12 col-sm-6 col-md-3 d-flex justify-content-center mb-3">
							<button type="button" class="btn btn-primary w-100 small-width" id="incluir" name="incluir">INCLUIR<span class="fa fa-plus-circle ml-2"></span></button>
						</div>
					
						<div class="col-12 col-sm-6 col-md-3 d-flex justify-content-center mb-3">
							<button type="button" class="btn btn-info w-100 small-width" id="consultar" data-toggle="modal" data-target="#modal1" name="consultar">CONSULTAR<span class="fa fa-table ml-2"></span></button>
						</div>
				
				
						<div class="col-12 col-sm-6 col-md-3 d-flex justify-content-center mb-3">
							<button type="button" class="btn btn-warning w-100 small-width" id="modificar" name="modificar" disabled>MODIFICAR<span class="fa fa-pencil-square-o ml-2"></span></button>
						</div>
					
						<div class="col-12 col-sm-6 col-md-3 d-flex justify-content-center mb-3">
							<button type="button" class="btn btn-danger w-100 small-width" id="eliminar" name="eliminar" disabled>ELIMINAR<span class="fa fa-trash ml-2"></span></button>
						</div>
			
				</div>
			</div>
		</form>
	</div>
	<div class="modal fade" tabindex="-1" role="dialog" id="modal1">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header text-light bg-info">
					<h5 class="modal-title">Listado de Deudas</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="table-responsive">
					<table class="table table-striped table-hover" id="tabladeudas">
						<thead>
							<tr>
								<th class="d-none"></th>
								<th>Fecha</th>
								<th>Monto</th>
								<th>Concepto</th>
								<th>Registrada por</th>
								<th>Estado</th>
							</tr>
						</thead>
						<tbody id="listadodeudas">

						</tbody>
					</table>
				</div>
				<div class="modal-footer bg-light">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

		<?php require_once('comunes/modalconfirmacion.php'); ?>

	<script src="js/carga.js"></script>
	<script src="js/deuda-condominio.js"></script>
	<?php require_once('comunes/foot.php'); ?>
</body>

</html>