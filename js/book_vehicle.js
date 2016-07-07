$(document).ready(function() {

	// Verfiy vehicle number
	$('#verify').click(function(e) {
		e.preventDefault();
		var vehicle_no = $('#vehicle_no').val();
		if (vehicle_no.length > 1) {
			$.post('ajax/vehicle.php', {'op': 'verify-vehicle', 'vehicle_no': vehicle_no}, function(d) {
				if (d.trim() == "Found") {
					// Display controls for vehicle booking
					$('#book_vehicle_controls').fadeIn();
				} else {
					// Display modal for adding vehicle
					$('#vehicleModal').modal();
					$('#vehicleModal input[name=vehicle_no]').val(vehicle_no);
				}
			});
		}
	});

	/*** Book vehicle ***/
	$('#book_vehicle').submit(function(e) {
		e.preventDefault();
		var bln_validate = true;
		var data = $(this).serialize();
		var status = false;

		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0 && val.name != "departure_order") {
				$('#b_side > .alert-error').fadeIn();
				$('[name=' + val.name + ']').focus();
				bln_validate = false;
				return false;
			}
		});

		if (bln_validate === false) return false;

		$.post('ajax/vehicle.php', data + '&op=book-vehicle', function(d) {
			if (d.trim() == "Done") {
				$('#b_side > .alert').removeClass('alert-error').addClass('alert-success')
				.html('vehicle booking successful.').fadeIn('fast').fadeOut(6000);
				$('#book_vehicle_controls').fadeOut();
				$('#book_vehicle #reset').click();
			} else if (d.trim() == "05") { // Invalid route fare
				var err = "This route's fare is invalid, please check it now";
				$('#b_side > .alert').removeClass('alert-success').addClass('alert-error').html(err).fadeIn('fast');
			} else if (d.trim() == "06") {
				var err = "This vehicle is already booked for the selected day";
				$('#b_side > .alert').removeClass('alert-success').addClass('alert-error').html(err).fadeIn('fast');
			}
			else
				$('#b_side > .alert').removeClass('alert-success').addClass('alert-error').html(d).fadeIn('fast');
		});
	});

	/*** Remove a booked vehicle ***/
	$('.remove-vehicle').click(function(e) {
		e.preventDefault();
		var $parentTr = $(this).parents("tr");

		var bbv_id     = $parentTr.attr('id');
		var boarding_vehicle_id = $parentTr.data('boarding-vehicle-id');

		if (confirm("Are you sure you want to remove this vehicle?")) {
			$.post('ajax/vehicle.php', {'op':'remove-vehicle', 'bbv_id':bbv_id, 'boarding_vehicle_id': boarding_vehicle_id}, function(d) {
				if (d.trim() == "Done")
					$parentTr.fadeOut();
			});
		}
	});


	/*** Edit  vehicle ***/
	$('#report').on('click', '.edit-vehicle', function() {
		var $parentTr = $(this).parents("tr");
		var bbv_id = $parentTr.attr("id");
		var vehicle_id = $parentTr.data("vehicle_id");
		var vehicle_type = $parentTr.find('td:nth-child(2)').text();
		var driver_name = $parentTr.find('td:nth-child(3)').text();
		var driver_phone = $parentTr.find('td:nth-child(4)').text();
		var vehicle_no = $parentTr.find('td:nth-child(5)').text();

		$("#_vehicle_no").val(vehicle_no);
		$("#vehicle_id").val(vehicle_id);
		$('#_vehicle_type_id').find("option").filter(function(i) {
			return $.trim(vehicle_type) === $(this).text().split("(")[0].trim();
		}).attr("selected", "selected");
		$("#driver_name").val(driver_name);
		$("#driver_phone").val(driver_phone);
		$("#bbv_id").val(bbv_id);
		$("#action").val("edit-vehicle");
	});


	/*** Save/edited vehicle information ***/
	$('#vehicle_details').submit( function(e) {
		e.preventDefault();
		var bln_validate = true;

		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0) {
				//alert(val.name)
				bln_validate = false;
				$('#vehicleModal .alert').html("Form not completely filled out").fadeIn().fadeOut(10000);
				$("[name=' + val.name + ']").focus();
				return false;
			}
		});

		if (bln_validate === false) return false;
		var action = $("#action").val();

		$.post('ajax/vehicle.php', $(this).serialize() + '&op=' + action, function(d) {
			if (d.trim() == 'Saved') {
				$('#vehicleModal .alert').addClass('alert-success').html("Operation successful...").fadeIn().fadeOut(9000);
				$('#vehicleModal').find('form').reset();
				$("#vehicleModal #close-modal").click();
				$("#action").val("add-vehicle");
			}
		});
	});
});
