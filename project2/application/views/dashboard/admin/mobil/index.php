<?php

	include './application/views/dashboard/components/header.php';
	include './application/views/dashboard/components/sidebar.php';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Data Mobil</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Data Mobil</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<!-- /.card-header -->
						<div class="card-header">
							<button type="button" class="btn btn-info btn-sm add-data" id="add-data" data-toggle="modal"
								data-target="#modal-create">
								<i class="fas fa-plus mr-2"></i>
								Add Mobil
							</button>
						</div>
						<div class="card-body">
							<table id="tableDatatable" class="table table-bordered table-hover">
								<thead>
									<tr>
										<th>No</th>
										<th>Merk</th>
										<th>Produk</th>
										<th>Nopol</th>
										<th>Warna</th>
										<th>Kapasitas & Bagasi</th>
										<th>Biaya Sewa</th>
										<th>CC</th>
										<th>Tahun</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?= $no = 1; foreach ($data as $value): ?>
									<tr>
										<td><?= $no; ?></td>
										<td><?= $value['merk'];  ?></td>
										<td><?= $value['produk'];  ?></td>
										<td><?= $value['nopol'];  ?></td>
										<td><?= $value['warna'];  ?></td>
										<td>
											<?= $value['kapasitas'];  ?>
											&
											<?= $value['bagasi'];  ?>
										</td>
										<td>Rp. <?= $value['biaya_sewa'];  ?></td>
										<td><?= $value['cc'];  ?></td>
										<td><?= $value['tahun'];  ?></td>
										<td>
											<button type="button" class="btn btn-info btn-sm edit-data" id="edit-data"
												data-id="<?= $value['id'] ?>"
												data-url="<?= site_url('dashboard/mobil/edit/:did'); ?>">
												Edit
											</button>
											<button type="button" class="btn btn-danger btn-sm delete-data"
												id="delete-data" data-id="<?= $value['id'] ?>"
												data-url="<?= site_url('dashboard/mobil/delete/:did'); ?>">
												Delete
											</button>
										</td>
									</tr>
									<?= $no++; endforeach; ?>
								</tbody>
							</table>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<!-- /.col -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /.container-fluid -->
	</section>
	<!-- /.content -->

</div>
<!-- /.content-wrapper -->

<?php
	include './application/views/dashboard/admin/mobil/modal.php';
	include './application/views/dashboard/components/footer.php';
?>



<script>
	$('#tableDatatable').DataTable({
		"paging": true,
		"lengthChange": false,
		"searching": false,
		"ordering": true,
		"info": true,
		"autoWidth": false,
		"responsive": true,
	});

	// Start : Submit Form Create
	$('#create-data-form').on('submit', function (event) {

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		event.preventDefault();

		Swal.fire({
			text: "Mohon menunggu..."
		});

		Swal.showLoading();

		$.ajax({
			url: $(this).attr("action"),
			method: "POST",
			data: new FormData(this),
			contentType: false,
			cache: false,
			processData: false,
			success: (data) => {
				if (data.status == "200" && data.response == "success" && data.success == true) {
					Swal.close();
					Swal.fire({
						icon: 'success',
						title: 'Sukses',
						text: data.success_message,
					});
					setTimeout(function () {
						Swal.close();
						$('#modal-create').modal('hide');
						location.reload();
					}, 2000);
				}
			},
			error: (data) => {
				if (data.responseJSON.status == "400" && data.responseJSON.response == "fail" && data.responseJSON.error == true) {
					Swal.fire({
						title: 'Perhatian!',
						html: data.responseJSON.error_message,
						icon: 'error',
						confirmButtonText: 'Oke'
					});
				}
			}
		});
	});
	// End : Submit Form Create

	$(document).on('click', '#edit-data', function () {
		let dataId = $(this).data("id");

		let routeUrl = $(this).data("url");
		routeUrl = routeUrl.replace(':did', dataId);

		Swal.showLoading();

		$.ajax({
			type: "GET",
			url: routeUrl,
			success: function (response) {

				Swal.close();
				
				let mobil = response.data;

				let formAction = $('#edit-data-form').attr('action');
				formAction = formAction.replace(':did', mobil.id);
				$('#edit-data-form').attr('action', formAction);

				$('#merk_id-edit').val(mobil.merk_id);
				$('#nopol-edit').val(mobil.nopol);
				$('#warna-edit').val(mobil.warna);
				$('#kapasitas-edit').val(mobil.kapasitas);
				$('#bagasi-edit').val(mobil.bagasi);
				$('#biaya_sewa-edit').val(mobil.biaya_sewa);
				$('#deskripsi-edit').val(mobil.deskripsi);
				$('#cc-edit').val(mobil.cc);
				$('#tahun-edit').val(mobil.tahun);
				
				$("#modal-edit").modal('show');

			},
			error: (data) => {
				if (data.responseJSON.status == "400" && data.responseJSON.response == "fail" && data.responseJSON.error == true) {
					Swal.fire({
						title: 'Perhatian!',
						text: data.responseJSON.error_message,
						icon: 'error',
						confirmButtonText: 'Oke'
					});
				}
			}
		});
	});

	// Start : Submit Form Edit
	$('#edit-data-form').on('submit', function (event) {

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		event.preventDefault();

		Swal.fire({
			text: "Mohon menunggu..."
		});

		Swal.showLoading();

		$.ajax({
			url: $(this).attr("action"),
			method: "POST",
			data: new FormData(this),
			dataType: 'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success: (data) => {
				if (data.status == "200" && data.response == "success" && data.success == true) {
					Swal.close();
					Swal.fire({
						icon: 'success',
						title: 'Sukses',
						text: data.success_message,
					});
					setTimeout(function () {
						Swal.close();
						$('#modal-edit').modal('hide');
						location.reload();
					}, 2000);
				}
			},
			error: (data) => {
				if (data.responseJSON.status == "400" && data.responseJSON.response == "fail" && data
					.responseJSON.error == true) {
					Swal.fire({
						title: 'Perhatian!',
						html: data.responseJSON.error_message,
						icon: 'error',
						confirmButtonText: 'Oke'
					});
				}
			}
		});
	});
	// End : Submit Form Edit

	// Start : Delete Data
	$(document).on('click', '#delete-data', function() {
		let dataId = $(this).data("id");

		let routeUrl = $(this).data("url");
		routeUrl = routeUrl.replace(':did', dataId);

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		event.preventDefault();
		Swal.fire({
			title: "Apakah yakin akan menghapus ini!?",
			// type: "info",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: "Hapus!",
			cancelButtonText: "Batal",
			confirmButtonColor: "#28a745",
			cancelButtonColor: "#dc3545",
			// reverseButtons: true,
			focusConfirm: true,
			focusCancel: false
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.close();
				Swal.fire({
					text: "Mohon menunggu..."
				});

				Swal.showLoading();
				$.ajax({
					type: 'DELETE',
					url: routeUrl,
					success: function(data) {
						if (data.status == "200" && data.response == "success" && data.success == true) {
							Swal.close();
							Swal.fire({
								icon: 'success',
								title: 'Sukses',
								text: data.success_message,
							});
							setTimeout(function() {
								Swal.close();
								location.reload();
							}, 1500);
						}
					},
					error: function(data) {
						if (data.responseJSON.status == "400" && data.responseJSON.response == "fail" && data.responseJSON.error == true) {
							Swal.fire({
								title: 'Perhatian!',
								text: data.responseJSON.error_message,
								icon: 'error',
								confirmButtonText: 'Oke'
							});
						}
					}
				});
			}
		})
	});
	// End : Delete Data

</script>
