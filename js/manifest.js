$(document).ready(function() {

	$("select[name='park_map_id'], #t_date").change(function() {
		getBookedvehiclees($('#t_date').val());
	});

	/*** Get manifest ***/
	$('#view').click(function() {
		if ($('select[name=vehicle] option:selected').text() == "") {
			alert("Choose a vehicle to continue...");
			return false;
		}

		var boarding_vehicle_id = $('select[name=vehicle] option:selected').val();

		if (boarding_vehicle_id == null) {
			alert("You must select a vehicle to continue");
			return false;
		}

		$.post('ajax/manifest.php', {'op':'get-manifest', 'boarding_vehicle_id':boarding_vehicle_id}, function(d) {
			$('#manifest-div').html(d);
			$('button#print-manifest, button#print-waybill').data('boarding_vehicle_id', boarding_vehicle_id);
			$('button#print-manifest').prop("disabled", false);
		});
		$("#boarding_vehicle_id").val(boarding_vehicle_id);
		getAuditDetails(boarding_vehicle_id);
	});

	/*** Cancel sold ticket ***/
	$('#manifest-div').on('click', '.cancel-ticket', function(e) {
		e.preventDefault();
		var $this = $(this);
		var ticket_id = $this.attr('id');
		if (confirm("Are you sure you want to cancel this ticket?")) {
			$.post('ajax/manifest.php', {'op':'cancel-ticket', 'ticket_id':ticket_id}, function(d) {
				if (d.trim() == 'Done') {
					$this.parents("tr").fadeOut('fast');
				}
			});
			$.post('ajax/synch.php', {'op': 'cancel-ticket', 'ticket_id': ticket_id});
		}
	});

	// Print manifest
	$('#print-manifest').click(function() {
		var boarding_vehicle_id = $(this).data('boarding_vehicle_id');

		$.get('ajax/manifest.php', {'op': 'print_manifest', 'boarding_vehicle_id': boarding_vehicle_id}, function(d) {

			/*** Print manifest ***/
			var iframe_body = $("#manifest-frame").contents().find("body");
			iframe_body.html(d);
			window.frames['manifest'].print();
			iframe_body.html("");
		});
	});

	// Print waybill
	$('#print-waybill').click(function() {
		var boarding_vehicle_id = $(this).data('boarding_vehicle_id');

		$.get('ajax/manifest.php', {'op': 'print-waybill', 'boarding_vehicle_id': boarding_vehicle_id}, function(d) {

			/*** Print manifest ***/
			var iframe_body = $("#waybill").contents().find("body");
			iframe_body.html(d);
			window.frames['waybill'].print();
			iframe_body.html("");
		});
	});


	/*** Print manifest template ***/
	/*$('#print-manifest-template').click(function() {
		var operator = $('#operator').val();
		var html = "<div class='head'>" + operator + " Transport Limited<br />"
			+"<span>Tel: 070 0400 0000</span></div>"
			+"<p style='line-height:22px'>Route: <br />"
			+"Driver's name: <br />"
			+"Driver's phone no: <br />"
			+"vehicle number: <br />"
			+"Date of travel: <br />";

			html += "<table cellpadding='10' cellspacing='10' style='border-collapse:collapse; width:73%; float:left; font-size:12px' border='1'>"
			+"<thead><tr><th width='10'>S/NO</th><th width='250'>Traveler's name</th><th>Phone number</th><th>Next of Kin no</th><th>Cost</th></tr></thead><tbody>";

			for (var i = 1; i < 16; i++) {
				html += "<tr><td>" + i + "<td></td><td></td><td></td><td></td></tr>";
			}
			html += "</tbody></table>"
				+"<div class='audit-pane' style='line-height:22px'><div><b>Balance Sheet</b></div><hr style='margin:8px 0px' />"
				+"Tickets sold: <br />"
				+"Transport income: <br />"
				+"Expenses/Driver: <br />"
				+"Service Charge: <br /><hr />"
				+"Balance: <br /></div>"
				+"<div id='signature'><span><hr />Driver's Signature</span><span style='float:right'><hr />Manager's Signature</span></div>";

		/!*** Print manifest ***!/
		var iframe_body = $("#manifest").contents().find("body");
		iframe_body.html(html);
		window.frames['manifest'].print();
		iframe_body.html("");
	});*/


	/*** Submit manifest balance sheet ***/
	$('#form-audit').submit(function(e) {
		e.preventDefault();

		$.post('ajax/manifest.php', $(this).serialize() + '&op=balance-sheet', function(d) {
			$('#auditModal .alert-success').html("Action successful...").show().fadeOut(6000);
		});
		$.post('ajax/synch.php', $(this).serialize() + '&op=synch-manifest-account');
	});

	// Reprint ticket
	$('#manifest-div').on('click', '.print-ticket', function(e) {
		e.preventDefault()
		var ticket_id = $(this).attr('id');

		$.post('seat_booking.php', {'ticket_id':ticket_id, 'op':'print-ticket'}, function(d) {

			/*** Print ticket ***/
			var iframe_body = $("#receipt").contents().find("#details");
			iframe_body.html(d);
			window.frames['receipt'].print();
			iframe_body.html("");
		});
	});


/*** Reopen the current vehicle ***/
	$('#audit_pane').on('click', '#reopen', function() {
		var boarding_vehicle_id = $(this).data('boarding_vehicle_id');
		$.post('ajax/manifest.php', {'op':'reopen-vehicle', 'boarding_vehicle_id':boarding_vehicle_id}, function(d) {
			if (d.trim() == "Done") {
				alert("The vehicle has been reopen");
				// open online
				$.post('ajax/synch.php', {'op': 'reopen-vehicle', 'boarding_vehicle_id': boarding_vehicle_id});
				$("#print-waybill").prop("disabled", true);
				$(this).hide();
			} else
				alert(d);
		});
	});

/*** Edit customer information ***/
	$('#manifest-div').on('click', '.edit-ticket', function(e) {
		e.preventDefault();
		var $parentTr = $(this).parents("tr");
		var customer_id = $parentTr.attr("id");
		var c_name = $parentTr.find('td:nth-child(2)').text();
		var phone = $parentTr.find('td:nth-child(3)').text();
		var next_of_kin = $parentTr.find('td:nth-child(4)').text();
		//var cid = $(this).data('cid');

		$("#c_name").val(c_name);
		$("#next_of_kin").val(next_of_kin);
		$("#c_phone").val(phone);
		$("#customer_id").val(customer_id);
		//});
	});


/*** Update customer's information ***/
	$('#customer_details').on('submit', function(e) {
		e.preventDefault();
		var bln_validate = true;

		// Validate form inputs
		$.each($(this).serializeArray(), function(i, val) {
			if (val.value.length == 0 && val.name != 'cid') {
				bln_validate = false;
				$('#customerModal .alert').html("Form not completely filled out").fadeIn().fadeOut(10000);
				$("[name=' + val.name + ']").focus();
				return false;
			}
		});

		if (bln_validate === false) return false;
		var customer_id = $("#customer_id").val();

		$.post('ajax/manifest.php', $(this).serialize() + '&op=update_customer_info', function(d) {
			if (d.trim() == 'Done') {
				$("#tbl-ticket tr#" + customer_id).find('td:nth-child(2)').text($("#c_name").val());
				$("#tbl-ticket tr#" + customer_id).find('td:nth-child(3)').text($("#c_phone").val());
				$("#tbl-ticket tr#" + customer_id).find('td:nth-child(4)').text($("#next_of_kin").val());
				$('#customerModal .alert').addClass('alert-success').html("Operation successful...").fadeIn().fadeOut(9000);
			}
		});
	});
});

function getBookedvehiclees(_date) {
	var park_map_id = $('select[name=park_map_id] option:selected').val();
	if (park_map_id.length < 1) return;

	$.post('ajax/manifest.php', {'op':'get-boarding-vehicles', 'travel_date':_date, 'park_map_id':park_map_id}, function(d) {
		$("select[name='vehicle']").html(d);
	});
}

function getAuditDetails(boarding_vehicle_id) {
	$.post('ajax/manifest.php', {"op": "get-audit", "boarding_vehicle_id": boarding_vehicle_id}, function(d) {
		if (d.trim()== "false") {
			$("#audit_pane").html("<div class='audit_pane'>No details found</div>");
		} else {
			$("#print-waybill").prop("disabled", false);
			$("#audit_pane").html(d);
		}
	});
}

function IsNumeric(num) {
	var num_exp = "/^[0-9]+$/";
	if (num.match(num_exp)) {
		return true;
	}
}
