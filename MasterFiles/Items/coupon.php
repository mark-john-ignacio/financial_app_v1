<?php
    if(!isset($_SESSION)){
    session_start();
    }
    $_SESSION['pageid'] = "DISC.php";

    include('../../Connection/connection_string.php');
    // include('../../include/accessinner.php');
    $company = $_SESSION['companyid'];
	$coupon = [];


	$sql = "select * from coupon where compcode='$company' order by label";
    $result=mysqli_query($con,$sql);
					
    if (!mysqli_query($con, $sql)) {
        printf("Errormessage: %s\n", mysqli_error($con));
    } 
    
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
        array_push($coupon, $row);
    }
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
                    <font size="+2"><u>Coupon List</u></font>	
                </div>
                
            </div>
			<br><br>
            <button type="button" class="btn btn-primary btn-md" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
			<button type="button" class="btn btn-warning btn-md" id="btnmass" name="btnmass"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Mass Upload</button>
            
            <br><br>
			
			<table id="example" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">Coupon Code</th>
						<th>Description</th>
                        <th width="80">Label</th>
						<th width="100">Effect Date</th>
                        <th width="80">Expired Date</th>
                        <th width="80">Status</th>
                        <th width='100'>Action</th>
					</tr>
				</thead>

				<tbody>
              	<?php foreach($coupon as $row):?>
					<!-- "editgrp('< ?php echo $row['ctranno'];?>','< ?php echo $row['cdescription'];?>','< ?php echo $row['clabel'];?>','< ?php echo $row['nvalue'];?>','< ?php echo date_format(date_create($row['deffectdate']),"m/d/Y");?>')" -->
 					<tr>
						<td width="100"><a href="javascript:;" onclick="update('<?= $row['CouponNo'] ?>')"><?= $row['CouponNo'] ?></a></td>
                        <td><?php echo $row['remarks'];?>
                        	<div class="itmalert alert alert-danger nopadding" id="itm<?php echo $row['CouponNo'];?>" style="display: inline"></div>
                        </td>
                        <td><?php echo $row['label'];?></td>
                        <td><?php echo $row['effective'];?></td>
                        <td><?php echo $row['expired'];?></td>
						<td>
                            <div class='nopadwdtop' id="itmstat<?= $row['CouponNo'] ?>">
								<?php 
									echo match($row['status']){
										"ACTIVE" => "<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['CouponNo'] ."','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>",
										"INACTIVE" => "<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('". $row['CouponNo'] ."','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>",
										"CLAIMED" =>"<span class='label label-danger'>Claimed</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>"
									}
								?>
								</div>
                        </td>
                        <td>
                            <div class='nopadwdtop' id="msg<?php echo $row['CouponNo'];?>">
								<?php 
									if(intval($row['cancelled'])==intval(0) && intval($row['approved'])==intval(0)){
									?>
										<a href="javascript:;" onClick="trans('POST','<?php echo $row['CouponNo'];?>', 'Posted')">POST</a> | <a href="javascript:;" onClick="trans('CANCEL','<?php echo $row['CouponNo'];?>','Cancelled')">CANCEL</a>
									<?php
									}
									else{
										if(intval($row['approved'])==intval(1)){
                                            echo "Posted";
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

	  <div class="modal-body" style="height: 30vh;"> 

        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Label</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="txtlabel" name="txtlabel"  placeholder="Enter Label.." required>
				<input type="text" class="form-control input-sm" id="tranno" name="tranno" style="display: none;"  placeholder="transaction Number">
            </div>
        </div>   

        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Remarks</b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="remarks" name="remarks"  placeholder="Enter Description.." required>
            </div>
        </div>   

		<div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Price: </b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="number" class="form-control input-sm" id="Price" name="Price" placeholder="Enter Price..." autocomplete="FALSE"/>
            </div>
        </div> 

        <div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Barcode: </b>
            </div>
            
            <div class="col-xs-9 nopadwtop">
                <input type="text" class="form-control input-sm" id="barcode" name="barcode" placeholder="Enter Barcode..." autocomplete="FALSE"/>
            </div>
        </div>

		<div class="col-xs-12">
            <div class="cgroup col-xs-3 nopadwtop">
                <b>Sales Debit Account: </b>
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
			<div class="col-xs-3 nopadwtop" style='padding-top: 3px !important; padding-bottom: 0 !important; margin: 0 !important;'>
                <b>Coupon Days: <br><i>/* after activation of the coupon expiration should be applied</i></b>
            </div>
            
            <div class="col-xs-9 nopadwtop"  style='padding-top: 3px !important; padding-bottom: 0 !important; margin: 0 !important;'>
                <input type="number" class="form-control input-sm" id="days" name="days" placeholder="Enter Days after expire...">
            </div>
        </div> 
         
      <div class="alert alert-danger nopadding" id="add_err"></div>         

	</div>
    
 	<div class="modal-footer">
    			<input type="hidden" id="txtcode" name="txtcode" value=''>
                <button type="button" id="btnSave" name="Save" class="btn btn-primary btn-sm">Add Detail</button>
                <button type="button" id="btnUpdate" name="Update" class="btn btn-success btn-sm">Update Detail</button>
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Cancel</button>
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
	$(function(){
		$('#example').DataTable();

		$("#add_err").hide();
		$(".itmalert").hide();

        //effect date format
        $('.datepicker').datetimepicker({
                 format: 'MM/DD/YYYY',
				 minDate: new Date(),
        });

		$('#btnmass').click(function(){
			window.location="../../Coupon/mass_upload.php"
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
				month = "0"+month
			}
			let inDay = year + "/" + month + "/" + day;

			if(access.trim() == "True"){
				$("#btnSave").show();
                $("#btnUpdate").hide();
				$("#itemlist tbody").empty();

				$('.modal-body input').val("");

				$("#tranno").attr("readonly", false);
				$("#remarks").attr("readonly", false);
				$('#txtlabel').attr("readonly", false);	
				$('#Price').attr("readonly", false);
				$('#days').attr("readonly", false);
				$('#barcode').attr("readonly", false);

				$('#expired').val(inDay);
				$('#myModalLabel').html("<b>Create New Coupon</b>");
                $('#myModal').modal('show');
			} else {
				$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
                $("#AlertModal").modal('show');
			}
		})

		$("#btnSave").click(function(){
			let label = $('#txtlabel').val();
			let remarks = $('#remarks').val();
			let barcode = $('#barcode').val();
			let price = $("#Price").val();
			let days = $('#days').val();
			let acctcode = $('#salesdracct').val();

			$.ajax({
				url: "th_couponsave.php",
				data: {
					label: label,
					remarks: remarks,
					barcode: barcode,
					priced: price,
                    days: days,
					acctcode: acctcode
				},
				dataType: 'json',
				async: false,
				success: function(res){
					if(res.valid){
						isProcceed = res.valid
                        alert(res.msg)
					} else {
                        alert(res.msg)
                    }
                    location.reload()
				},
				error: function(res){
					console.log(res)
				}
			})
		})
		
		$('#btnUpdate').click(function(){
			let transaction = $('#tranno').val();
			let label = $('#txtlabel').val();
			let remarks = $('#remarks').val();
            let barcode = $('#barcode').val();
            let price = $('#Price').val();
			let days = $('#days').val();
			let acctcode = $('#salesdracct').val();

			$.ajax({
				url: "th_couponupdate.php",
				data: {
					tranno: transaction,
					remarks : remarks,
					label: label,
					barcode: barcode,
					priced: price,
                    days: days,
					acctcode: acctcode
				},
				dataType: 'json',
				async: false,
				success: function(res){
					if(res.valid){
						alert(res.msg)
					} else{
						alert(res.msg)
					}
					location.reload()
				},
				error: function(res){
					console.log(res)
				}
			})
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
		if(access.trim() == "True"){
			console.log(data)
			$.ajax({
				url: "th_couponlist.php",
				data: {coupon : data},
				dataType: "json",
				async: false,
				success: function(res){
					if(res.valid){
						res.data.map((item, index)=>{
							$("#btnSave").hide();
							$("#searchitem").hide();
							$("#btnUpdate").show();
							console.log(item)

							var isddfc ="";
							if(item.approved != 0){
								//return alert("Discount has been approved")
								isposted = 1;
								isddfc = "disabled";
								xcblabelz = "<font style=\"color: red\">(POSTED)</font>";
							}

							if(item.cancelled != 0){
								//return alert("Discount has been cancelled")
								isposted = 1;
								isddfc = "disabled";
								xcblabelz = "<font style=\"color: red\">(CANCELLED)</font>";
							}

							$("#tranno").val(item.CouponNo)
							$('#remarks').val(item.remarks);	
							$('#txtlabel').val(item.label);
							$('#Price').val(item.price);
							$("#days ").val(item.days);
							$('#barcode').val(item.barcode);
							$('#salesdracctnme').val(item.cacctdesc);
							$('#salesdracct').val(item.cacctcode);

							if(isposted==1){
								$("#tranno").attr("readonly", true);
								$("#remarks").attr("readonly", true);
								$('#txtlabel').attr("readonly", true);	
								$('#Price').attr("readonly", true);
								$('#days').attr("readonly", true);
								$('#barcode').attr("readonly", true);
								$('#salesdracctnme').attr("readonly", true);
							}else{
								$("#tranno").attr("readonly", false);
								$("#remarks").attr("readonly", false);
								$('#txtlabel').attr("readonly", false);	
								$('#Price').attr("readonly", false);
								$('#days').attr("readonly", false);
								$('#barcode').attr("readonly", false);
								$('#salesdracctnme').attr("readonly", false);
							}

							$('#myModalLabel').html("<b>Update Coupon Detail</b> "+xcblabelz);
							$('#myModal').modal('show');
                        })

						if(isposted==1){
							$("#btnUpdate").attr("disabled", true);
						}
					} else {
						console.log(res.msg)
					}
				},
				error: function(res){
					console.log(res)
				}
			})

		} else {
			$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			$("#AlertModal").modal('show');
		}
	}

	
	function setStat(code, stat){
			$.ajax ({
				url: "th_couponstat.php",
				data: { code: code,  stat: stat },
				dataType: 'json',
				async: false,
				success: function( data ) {
					if(data.valid){
						if(stat=="ACTIVE"){
							$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','INACTIVE')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
						}else{
							$("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"setStat('"+code+"','ACTIVE')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
						}
						
						$("#itm"+code).html("<b>SUCCESS: </b> Status changed to "+stat);
						$("#itm"+code).attr("class", "itmalert alert alert-success nopadding")
						$("#itm"+code).show();
					}
					else{
						$("#itm"+code).html("<b>Error: </b>"+ data.msg);
						$("#itm"+code).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+code).show();
					}
					
				}, error: function(res){
					console.log(res)
				}
			
			});

	}

	function showAlert(msg){
		$("#AlertModal").modal("show")
		$("#AlertMsg").html(msg) 
	}

	function trans(x,num,msg){
	
		$.ajax ({
			url: "th_coupontran.php",
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