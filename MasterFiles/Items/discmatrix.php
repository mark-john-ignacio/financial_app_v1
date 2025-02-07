<?php
    if(!isset($_SESSION)){
    session_start();
    }
    $_SESSION['pageid'] = "DISC";

    include('../../Connection/connection_string.php');
    include('../../include/accessinner.php');
    $company = $_SESSION['companyid'];

	$poststat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'DISC_post'");
	if(mysqli_num_rows($sql) == 0){
		$poststat = "False";
	}

	$cancstat = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'DISC_cancel'");
	if(mysqli_num_rows($sql) == 0){
		$cancstat = "False";
	}

	$discount = [];
	$pm = [];

    // $sql = "select * from DiscountMatrix where compcode='$company' order by label";
	$sql = "select * from discountmatrix where compcode='$company' order by label";
    $result=mysqli_query($con,$sql);
					
    if (!mysqli_query($con, $sql)) {
        printf("Errormessage: %s\n", mysqli_error($con));
    } 
    
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        array_push($discount, $row);
    }

	// $sql = "select * from groupings where compcode='$company' and ctype='ITMPMVER' and cstatus='ACTIVE' order by cdesc";	
	// $query = mysqli_query($con, $sql);
	// while($row = $query -> fetch_assoc()){
	// 	array_push($pm, $row);
	// }
?>
<!DOCTYPE html>
<html>
<head>

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css?t=<?php echo time();?>">
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap-datetimepicker.css">
        
    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/jquery.numeric.js"></script>

    <script src="../../Bootstrap/js/bootstrap.js"></script>
    <script src="../../Bootstrap/js/moment.js"></script>
    <script src="../../Bootstrap/js/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>MYX Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
            <div>
                <div style="float:left; width:50%">
                    <font size="+2"><u>Discount Matrix List</u></font>	
                </div>               
            </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-md" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
            <button type="button" class="btn btn-warning btn-md" id="btnmass" name="btnmass"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Mass Upload</button>
            <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">Discount Code</th>
						<th>Description</th>
                        <th width="80">Label</th>
						<th width="100">Effect Date</th>
                        <th width="80">Due</th>
                        <th width="100"  style="text-align: center">Status</th>
					</tr>
				</thead>

				<tbody>
              	<?php foreach($discount as $row):?>
					<!-- "editgrp('< ?php echo $row['ctranno'];?>','< ?php echo $row['cdescription'];?>','< ?php echo $row['clabel'];?>','< ?php echo $row['nvalue'];?>','< ?php echo date_format(date_create($row['deffectdate']),"m/d/Y");?>')" -->
 					<tr>
						<td width="100"><a href="javascript:;" onclick="update('<?= $row['tranno'] ?>')"><?= $row['tranno'] ?></a></td>
                        <td><?php echo $row['remarks'];?>
                        	<div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['tranno'];?>" style="display: inline"></div>
                        </td>
                        <td><?php echo $row['label'];?></td>
                        <td><?php echo $row['deffective'];?></td>
                        <td><?php echo $row['ddue'];?></td>
						<td style="text-align: center">
							<div id="msg<?php echo $row['tranno'];?>">
								<?php 
									if(intval($row['cancelled'])==intval(0) && intval($row['approved'])==intval(0)){
									?>

										<a href="javascript:;" onClick="trans('POST','<?php echo $row['tranno'];?>', 'Posted')" class="btn btn-xs btn-default<?=($poststat!="True") ? " disabled" : ""?>"><i class="fa fa-thumbs-up" style="font-size:20px;color:Green ;" title="Approve transaction"></i></a> 

										<a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['tranno'];?>','Cancelled')" class="btn btn-xs btn-default<?=($cancstat!="True") ? " disabled" : ""?>"><i class="fa fa-thumbs-down" style="font-size:20px;color:Red ;" title="Cancel transaction"></i></a> 

									<?php
									}
									else{
										if(intval($row['approved'])==intval(1)){
								?>			
								<div id="itmstat<?php echo $row['tranno'];?>">
								<?php 
									if($row['status']=="ACTIVE"){
										echo "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['tranno'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>";
									}
									else{
										echo "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['tranno'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>";
									}
								?>
								</div>
								<?php			
                                        }
                                        if(intval($row['cancelled'])==intval(1)){
                                            echo "Cancelled";
                                        }
                                    }
								?>
							</div>
                        </td>

					</tr>
                <?php endforeach;?>
               
				</tbody>
			</table>

		</section>
	</div>		


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="myModalLabel"><b>Add New Discount</b></h5>        
			</div>

			<div class="modal-body" style="height: 70vh;"> 

				<div class="col-xs-12">
					<div class="cgroup col-xs-3 nopadwtop2x">
						<b>Label</b>
					</div>
					
					<div class="col-xs-9 nopadwtop">
						<input type="text" class="form-control input-sm" id="txtlabel" name="txtlabel"  placeholder="Enter Label.." required>
						<input type="text" class="form-control input-sm" id="tranno" name="tranno" style="display: none;"  placeholder="transaction Number">
					</div>
				</div>   

				<div class="col-xs-12">
					<div class="cgroup col-xs-3 nopadwtop2x">
						<b>Description</b>
					</div>
					
					<div class="col-xs-9 nopadwtop">
						<input type="text" class="form-control input-sm" id="txtdesc" name="txtdesc"  placeholder="Enter Description.." required>
					</div>
				</div>   
				
				<div class="col-xs-12">
					<div class="cgroup col-xs-3 nopadwtop2x">
						<b>Effectivity Date</b>
					</div>

					<div class="col-xs-3 nopadwtop">
						<input type="text" class="datepicker form-control input-sm" id="effect_date" name="effect_date" value="">
					</div>
					
					<div class="col-xs-1 nopadwtop2x text-right">
						<b>Due Date: </b>
					</div>
					
					<div class="col-xs-3 "  style='padding-top: 3px !important; padding-bottom: 0 !important; margin: 0 !important;'>
						<input type="text" class="datepicker form-control input-sm" id="duedate" name="duedate">
					</div>
				</div> 

				<div class="col-xs-12">
					<div class="cgroup col-xs-3 nopadwtop2x">
						<b>Sales Debit Acct</b>
					</div>
					
					<div class="col-xs-9 nopadwtop">
						<div class="col-xs-3 nopadding">
							<input type="text" class="form-control input-sm" id="salesdracct" name="salesdracct" value='' readonly placeholder="Account Code">
						</div>
						<div class="col-xs-9 nopadwleft">
							<input type="text" class="form-control input-sm" id="salesdracctnme" name="salesdracctnme" value='' placeholder="Account Title">
						</div>
					</div>
				</div> 

				<div class="col-xs-12">
					<div class="cgroup col-xs-3 nopadwtop2x">
						<b>Search Item: </b>
					</div>
					
					<div class="col-xs-9 nopadwtop">
						<input type="text" class="form-control input-sm" id="searchitem" name="searchitem" autocomplete="FALSE"/>
					</div>
				</div> 

				<div class="col-xs-12" id="divalert" style="display: none; text-align: center; padding-top: 5px">

				</div> 
				<div class='col-xs-12' id='itemlist' style='height: 40vh; overflow: auto; border: 1px solid grey; margin-top: 10px'>
					<table class='table' style='width: 100%;'>
						<thead>
							<tr>
								<th style='width: 15%'>Item No.</th>
								<th style='width: 70%'>Item</th>
								<th>Unit</th>
								<th>Type</th>
								<th>Discount</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				
				<div class="alert alert-danger nopadding" id="add_err"></div>         

			</div>
			
			<div class="modal-footer">
				<input type="hidden" id="txtcode" name="txtcode" value=''>
				<button type="button" id="btnSave" name="Save" class="btn btn-primary btn-sm">Add Detail</button>
				<button type="button" id="btnUpdate" name="Update" class="btn btn-success btn-sm">Update Detail</button>
				<button type="button" id='btnCancel' class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
			</div>
		
		</div>
	</div>
</div>
<!-- Modal -->	 	

<!-- 1) Alert Modal -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
                <p>
                    <center>
                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
                    </center>
                </p>
               </div>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_close($con);
?>

</body>
</html>
<script>
	var itemStored = [];
	var itemStoredxli = [];
	$(function(){

		$("#add_err").hide();
		$(".itmalert").hide();

		$('#example').DataTable();

        //effect date format
        $('.datepicker').datetimepicker({
            format: 'MM/DD/YYYY',
			//minDate: new Date(),
        });

		$('#searchitem').typeahead({
			autoSelect: true,
			source: function(request, response){
				$.ajax({
					url: "th_itemgroup.php",
					data: { item: $('#searchitem').val() },
					dataType: 'json',
					async: false,
					success: function(res){
						if(res.valid){
							response(res.data)
						}
						
					},
					error: function(res){
						console.log(res)
					}
				})
			},
			displayText: function (item) {
                return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.cpartno + '</span><br><small>' + item.citemdesc + "</small></div>";
            },
            highlighter: Object,
            afterSelect: function(items) { 
				$('#searchitem').val("").change()

				if(jQuery.inArray(items.cpartno, itemStoredxli) == -1){
					itemStored.push(items);
					itemStoredxli.push(items.cpartno);

					//$("#itemlist tbody").empty();
					//itemStored.map((item, index) => {
						$("<tr>").append(
							$("<td>").html(items.cpartno+"<input type='hidden' id='cpartno' name='cpartno' value='"+items.cpartno+"'/> "),
							$("<td>").html(items.citemdesc),
							$("<td>").html(items.cunit+"<input type='hidden' id='cunit' name='cunit' value='"+items.cunit+"'/> "),
							$("<td>").html("<select id='type' name='type'> <option value='PERCENT'>PERCENT</option> <option value='PRICE'>PRICE</option> </select>"),
							$("<td>").html("<input type='text' id='discountAmt' name='discountAmt' autocomplete='false'/> "),
							$("<td>").html("<button type='button' class='btn btn-xs btn-danger' id='deleteItem' name='deleteitem' onclick='deleteList.call(this)'>delete</buton>")
						).appendTo("#itemlist tbody")
					//})

					$("#divalert").html("");
					$("#divalert").hide();
				}else{
					$("#divalert").html("Item Already Added!");
					$("#divalert").show();
				}
				
			}
		})

		$('#btnmass').click(function(){
			let access = chkAccess("DISC_New")
			if(access.trim() == "True"){
				window.location="../../MassUpload/DiscountMatrix.php";
			} else {
				$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
                $("#AlertModal").modal('show');
			}
		})

		$("#btnCancel").click(function(){
			itemStored = [];
		})

		$("#btnadd").click(function(){
			let today = new Date();
			let day = today.getDate();
			let month = today.getMonth();
			let year = today.getFullYear();

			let access = chkAccess("DISC_New")

			if(day < 10){
				day = "0" + day;
			}
			if(month < 10){
				month = "0"+(month+1)
			}
			let inDay =  month + "/" + day + "/" +year;

			if(access.trim() == "True"){
				$("#btnSave").show();
				$("#searchitem").show();
                $("#btnUpdate").hide();
				$("#itemlist tbody").empty();

				$('.modal-body input').val("");
				$('#effect_date').val(inDay);
				$('#duedate').val(inDay);
				$('#myModalLabel').html("<b>Add New Discount</b>");

				$("#tranno").attr("readonly", false);
				$("#txtcode").attr("readonly", false);
				$('#txtdesc').attr("readonly", false);	
				$('#txtlabel').attr("readonly", false);
				$('#duedate').attr("readonly", false);
				$('#effect_date').attr("readonly", false);

                $('#myModal').modal('show');
			} else {
				$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
                $("#AlertModal").modal('show');
			}
		})

		
				
		// $("#btnSave, #btnUpdate").on("click", function() {
		// 	let label = $('#txtlabel').val();
		// 	let desc = $('#txtdesc').val();
		// 	let val = $('#txtvalue').val();
		// 	let effect = $('#effect_date').val();
		// 	let code = $('#txtcode').val();
						
		// 	$.ajax ({
		// 		url: "th_savedm.php",
		// 		data: { tranno: code, effect: effect, remarks: desc, label: label },
		// 		async: false,
		// 		success: function( data ) {
		// 			if(data.trim()=="True"){
						
		// 				$('#myModal').modal('hide');
		// 				location.reload();
				
		// 			}
		// 			else {
		// 				$("#add_err").html("<b>ERROR: </b>"+data);
		// 				$("#add_err").show();
		// 			}
		// 		}
		// 	});
		// });

		$("#btnSave").click(function(){
			let itemno = [], discounts = [], unit = [], types = []
			let isProcceed = false;
			let label = $('#txtlabel').val();
			let desc = $('#txtdesc').val();
			let due = $('#duedate').val();
			let discount = $("#discount").val();
			let effect = $('#effect_date').val();
			var acctcode = $("#salesdracct").val();
			let tranno = '';

			/*itemStored.map((item, index) => {
				itemno.push(item.cpartno);
				unit.push(item.cunit)
				console.log(item.cunit)
			})*/

			$("input[id='cpartno").each(function(){
				itemno.push($(this).val())
			});

			$("input[id='cunit").each(function(){
				unit.push($(this).val())
			});

			$("select[id='type']").each(function() {
				types.push($(this).find(":selected").val());
			});

			$("input[id='discountAmt").each(function(){
				discounts.push($(this).val())
			});

			$.ajax({
				url: "th_savedm.php",
				data: {
					label: label,
					remarks: desc,
					due: due,
					effect: effect,
					acctcode:acctcode
				},
				dataType: 'json',
				async: false,
				success: function(res){
					if(res.valid){
						isProcceed = res.valid
						tranno = res.tranno
						console.log(res.tranno)
					}
					
				},
				error: function(res){
					console.log(res)
				}
			})

			if(isProcceed){
				$.ajax({
					url: "th_savedm_t.php",
					data: {
						item: JSON.stringify(itemno),
						unit: JSON.stringify(unit),
						discount: JSON.stringify(discounts),
						types: JSON.stringify(types),
						tranno: tranno
					},
					dataType: 'json',
					async: false,
					success: function(res){
						if(res.valid){
							console.log(res.msg)
						}else {
							console.log(res.msg)
						}
						location.reload();
					},
					error: function(res){
						console.log(res)
					}
				})
			}
		})
		
		$('#btnUpdate').click(function(){
			let itemno = [], discounts = [], types = [], unit = []
			let transaction = $('#tranno').val();
			let label = $('#txtlabel').val();
			let desc = $('#txtdesc').val();
			let due = $('#duedate').val();
			let effect = $('#effect_date').val();
			let tranno = $('#tranno').val();
			var acctcode = $("#salesdracct").val();

			let isProcceed = false;

			/*itemStored.map((item, index) => {
				itemno.push(item.itemno);
			})*/

			
			$("input[id='cpartno").each(function(){
				itemno.push($(this).val())
			});

			$("input[id='cunit").each(function(){
				unit.push($(this).val())
			});


			$("select[id='type']").each(function() {
				types.push($(this).find(":selected").val());
			});

			$("input[id='discountAmt']").each(function(){
				discounts.push($(this).val())
			})

			$.ajax({
				url: "th_updatedm.php",
				data: {
					tranno: transaction,
					remarks : desc,
					label: label,
					effective: effect,
					due: due,
					acctcode:acctcode
				},
				dataType: 'json',
				async: false,
				success: function(res){
					if(res.valid){
						isProcceed = res.valid
					}
				},
				error: function(res){
					console.log(res)
				}
			})


			if(isProcceed){
				$.ajax({
					url: "th_savedm_t.php",
					data: {
						item: JSON.stringify(itemno),
						unit: JSON.stringify(unit),
						discount: JSON.stringify(discounts),
						types: JSON.stringify(types),
						tranno: transaction
					},
					dataType: 'json',
					async: false,
					success: function(res){
						if(res.valid){
							console.log(res.msg)
						}else {
							console.log(res.msg)
						}
						location.reload();
					},
					error: function(res){
						console.log(res)
					}
				})
			}
		})

		$("#salesdracctnme").typeahead({
			items: 10,
			source: function(request, response) {
				$.ajax({
					url: "../../Sales/th_accounts.php",
					dataType: "json",
					data: {
						query: $("#salesdracctnme").val()
					},
					success: function (data) {
						console.log(data);
						response(data);
					}
				});
			},
			autoSelect: true,
			displayText: function (item) {
				return '<div style="border-top:1px solid gray; width: 300px"><span>' + item.acct + '</span><br><small>' + item.name + '</small></div>';
			},
			highlighter: Object,
			afterSelect: function(item) { 

				$('#salesdracctnme').val(item.name).change(); 
				$("#salesdracct").val(item.acct);

			}
		});
	});


	function update(data){

		var isposted = 0;
		var xcblabelz = "";
		var access = chkAccess('DISC_Edit');

		console.log(data)
		$.ajax({
			url: "th_discmatrixlist.php",
			data: {tranno : data},
			dataType: "json",
			async: false,
			success: function(res){
				if(res.valid){
					$("#itemlist tbody").empty();
					res.data.map((item, index)=>{
						$("#btnSave").hide();
						
						$("#btnUpdate").show();

						var isddfc ="";
						if(item.approved != 0){
						//	return alert("Discount has been approved")
							isposted = 1;
							isddfc = "disabled";
							xcblabelz = "<font style=\"color: red\">(POSTED)</font>";
						}

						if(item.cancelled != 0){
						//	return alert("Discount has been cancelled")
							isposted = 1;
							isddfc = "disabled";
							xcblabelz = "<font style=\"color: red\">(CANCELLED)</font>";
						}	

						if(access.trim() != "True"){
							isddfc = "disabled";
						}

						itemStored.push(item)
						// $("#pricematrix").each(function(){
						//     $(this).children('option').each(function(){
						//         if(item.matrix == $(this).val()) $(this).prop('selected', true)
						//     })
						// })

						$("#tranno").val(data)
						$("#txtcode").val(item.compcode);
						$('#txtdesc').val(item.remarks);	
						$('#txtlabel').val(item.label);
						$('#duedate').val(item.ddue);
						$('#effect_date').val(item.deffective);
						$('#salesdracctnme').val(item.cacctdesc);
						$('#salesdracct').val(item.cacctcode);

						if(isposted==1){
							$("#tranno").attr("readonly", true);
							$("#txtcode").attr("readonly", true);
							$('#txtdesc').attr("readonly", true);	
							$('#txtlabel').attr("readonly", true);
							$('#duedate').attr("readonly", true);
							$('#effect_date').attr("readonly", true);
							$('#salesdracctnme').attr("readonly", true);
						}else{
							$("#tranno").attr("readonly", false);
							$("#txtcode").attr("readonly", false);
							$('#txtdesc').attr("readonly", false);	
							$('#txtlabel').attr("readonly", false);
							$('#duedate').attr("readonly", false);
							$('#effect_date').attr("readonly", false);
							$('#salesdracctnme').attr("readonly", false);
						}

						if(isposted==1){
							mlctypxz = item.type;
						}else{
							mlctypxz = "<select id='type' name='type'> <option "+(item.type == "PERCENT" ? "selected" : null)+" value='PERCENT'>PERCENT</option> <option "+(item.type == "PRICE" ? "selected" : null)+" value='PRICE'>PRICE</option> </select>";
						}
						
						$("<tr>").append(
							$("<td>").html(item.itemno+"<input type='hidden' id='cpartno' name='cpartno' value='"+item.itemno+"'/>"),
							$("<td>").text(item.citemdesc),
							$("<td>").html(item.unit+"<input type='hidden' id='cunit' name='cunit' value='"+item.unit+"'/>"),
							$("<td>").html(mlctypxz),
							$("<td>").html("<input type='text' id='discountAmt' name='discountAmt' value='"+item.discount+"' autocomplete='false' "+isddfc+"/> "),
							$("<td>").html("<button type='button' class='btn btn-xs btn-danger' id='deleteItem' name='deleteitem' value='"+item.id+"' onclick='deleteList.call(this)' "+isddfc+">delete</buton>")
						).appendTo("#itemlist tbody")

						$('#myModalLabel').html("<b>Update Discounts Detail</b> "+xcblabelz);
						$('#myModal').modal('show');
					})

						if(isposted==1){
							$("#btnUpdate").attr("disabled", true);
							$("#searchitem").hide();
						}else{
							$("#searchitem").show();
						}
						console.log(itemStored);
					
				} else {
					console.log(res.msg)
				}
			},
			error: function(res){
				console.log(res)
			}
		})

		if(access.trim() != "True"){
			$("#btnUpdate").hide();

			$("#tranno").attr("readonly", true);
			$("#txtcode").attr("readonly", true);
			$('#txtdesc').attr("readonly", true);	
			$('#txtlabel').attr("readonly", true);
			$('#duedate').attr("readonly", true);
			$('#effect_date').attr("readonly", true);
			$('#salesdracctnme').attr("readonly", true);
			$('#searchitem').attr("readonly", true);
		}
	}

	
	function setStat(code, stat){
		var access = chkAccess('DISC_Edit');
		if(access.trim() == "True"){

			$.ajax ({
				url: "th_itemdmstat.php",
				data: { code: code,  stat: stat },
				async: false,
				success: function( data ) {
					if(data.trim()!="True"){
						$("#itm"+code).html("<b>Error: </b>"+ data);
						$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+code).show();
					}
					else{
						if(stat=="ACTIVE"){
						$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
						}else{
							$("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
						}
						
						$("#itm"+code).html("<b>SUCCESS: </b> Status changed to "+stat);
						$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
						$("#itm"+code).show();

					}
				}
			
			});
		} else {
			$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			$("#AlertModal").modal('show');
		}
	}

	function deleteList(){
		console.log($(this).val())
		let id = $(this).val();
		let row = $(this).closest("tr");

		row.remove();


		/*$.ajax({
			url: "th_deletedm.php", 
			data: {
				id: id
			},
			type: 'post',
			dataType: 'json',
			async: false,
			success: function(res){
				if(res.valid){
					console.log(res.msg)
					row.remove();
				} else {
					console.log(res.msg)
					row.remove();
				}
			},
			error: function(res){
				console.log(res)
			}
		})*/
	}

	function trans(x,num,msg){
	
		$.ajax ({
			url: "discmatrix_tran.php",
			data: { tranno: num, typ: x },
			async: false,
			success: function( data ) {
				$("#AlertMsg").html(data);
				$("#AlertModal").modal('show');
				
				$("#msg"+num).html(msg);
			}
		});
	
	}

	function chkAccess(id){
		var result;
		
		$.ajax ({
			url: "chkAccess.php",
			data: { id: id },
			async: false,
			success: function( data ) {
					result = data;
			}
		});
		
		return result;
	}


</script>