<?php
	if(!isset($_SESSION)){
		session_start();
	}

	$_SESSION['pageid'] = "Salesman";

	include('../../Connection/connection_string.php');
	include('../../include/denied.php');
	include('../../include/access2.php');

	$posedit = "True";
	$sql = mysqli_query($con,"select * from users_access where userid = '$employeeid' and pageid = 'Salesman_Edit'");
	if(mysqli_num_rows($sql) == 0){
		$posedit = "False";
	}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">   
    <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/> 
	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css">

    <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../../Bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript">
		function editfrm(x){
			document.getElementById("txtcitemno").value = x;
			document.getElementById("frmedit").submit();
		}
	</script>

	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

</head>

<body style="padding:5px">
	<div>
		<section>
			<div class="col-xs-12 nopadding">
				<font size="+2"><u>Salesman Master List</u></font>	
			</div>
					
			<br><br>
          
          	<div class="col-xs-12 nopadding">
				<div class="col-xs-2 nopadding">
					<button type="button" class="btn btn-primary btn-sm" onClick="location.href='Salesman_new.php'" id="btnNew" name="btnNew"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
				</div>
                <div class="col-xs-5" style="padding-top: 6px !important;">
					<span id="itmerr" style="padding: 5px !important;"> </span>
				</div>
                <div class="col-xs-2 text-right nopadwtop" style="height:30px !important;">
                	<b> Search Salesman: &nbsp;</b>
                </div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Enter Code or Desc...">
				</div>

			</div>
			
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="150">Salesman Code</th>
						<th>Salesman Name</th>
						<th>Tel. No.</th>
                        <th>Email</th>
						<th width="80">Status</th>
                       <!-- <th width="80">Delete</th>-->
					</tr>
				</thead>

			</table>

		</section>
	</div>		


<form name="frmedit" id="frmedit" method="post" action="Salesman_edit.php">
	<input type="hidden" name="txtcitemno" id="txtcitemno" />
</form>	

	<!-- 1) Alert Modal -->
	<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
		<div class="vertical-alignment-helper">
			<div class="modal-dialog vertical-align-top">
				<div class="modal-content">
				<div class="alert-modal-danger">
					<p id="AlertMsg"></p>
					<p>
						<center>
							<button type="button" class="btn btn-primary btn-sm" id="OK" onclick="setStat('OK')">Ok</button>
							<button type="button" class="btn btn-danger btn-sm" id="Cancel" onclick="setStat('Cancel')">Cancel</button>
							
							
							<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="alertbtnOK">Ok</button>
							
							<input type="hidden" id="typ" name="typ" value = "">
							<input type="hidden" id="modzx" name="modzx" value = "">
						</center>
					</p>
				</div> 
				</div>
			</div>
		</div>
	</div>
		

    <link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css"> 
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>

	$(document).ready(function() {

        fill_datatable();   
        $("#searchByName").keyup(function(){
           var searchByName = $('#searchByName').val();
           if(searchByName != '')
           {
            $('#MyTable').DataTable().destroy();
            fill_datatable(searchByName);
           }
        });			
		
	} );

	$(document).keydown(function(e) {
		if(e.keyCode == 112){//F1
				if(document.getElementById("btnNew").className=="btn btn-primary btn-sm"){
					e.preventDefault();
					window.location.href='Salesman_new.php';
				}
		}
	});

	function trans(code,stat,msg){
		var x = "<?=$posedit;?>";
			
		if(x.trim()=="True"){

			$("#typ").val(stat);
			$("#modzx").val(code);

			$("#AlertMsg").html("");
										
			$("#AlertMsg").html("Are you sure you want to "+msg+" Supplier Code: "+code);
			$("#alertbtnOK").hide();
			$("#OK").show();
			$("#Cancel").show();
			$("#AlertModal").modal('show');
		}else {
			$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			$("#alertbtnOK").show();
			$("#OK").hide();
			$("#Cancel").hide();
			$("#AlertModal").modal('show');

		}
	}

	function setStat(dstat){
	var x = "<?=$posedit;?>";
		
		if(x.trim()=="True"){
			if(dstat=="OK"){
				code = $("#modzx").val();
				stat = $("#typ").val();

				$.ajax ({
					url: "th_cussetstat.php",
					data: { code: code,  stat: stat },
					async: false,
					dataType: "text",
					success: function( data ) {
						//alert(jQuery.type(data));
						if(data.trim() == "True"){
						if(stat=="ACTIVE"){
							
							$("#itmstat"+code).html("<span class='label label-success'>Active</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"trans('"+code+"','INACTIVE','Inactivate')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a>");
						}else{
							$("#itmstat"+code).html("<span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"trans('"+code+"','ACTIVE','Activate')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a>");
						}
							
							$("#itmerr").html("<b>SUCCESS: </b> "+code+" Status changed to <b><u>"+stat+"</u></b>");
							$("#itmerr").attr("class", "itmalert alert alert-success");
							//$("#itmerr").css({'display':'inline', 'padding':'8px'});
							
						}
						else{
							
							$("#itmerr").html("<b>Error: </b>"+ data);
							$("#itmerr").attr("class", "itmalert alert alert-danger");
							//$("#itmerr").css({'display':'inline', 'padding':'8px'});
							
						}
					}
				
				});
			}
			$("#AlertModal").modal('hide');
		}else {
			$("#AlertMsg").html("<center><b>ACCESS DENIED!</b></center>");
			$("#AlertModal").modal('show');

		}
	}
		
	/*function deleteRow(xid){
		var Yx = confirm("Are you sure you want to delete this customer?");

		if (Yx==true){
			$.ajax ({
				url: "../th_delete.php",
				data: { code: xid,  id: "salesman" },
				async: false,
				dataType: "text",
				success: function( data ) {

					if(data.trim() != "True"){
						$("#itmerr").html("<b>Error: </b>"+ data);
						$("#itmerr").attr("class", "itmalert alert alert-danger nopadding")
						$("#itmerr").css('display', 'inline');
					}
					else{
						location.reload();
					}
				}
			
			});
		}
	}*/

        function fill_datatable(searchByName = '')
          {
           var dataTable = $('#MyTable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "lengthChange": false,
            "order" : [],
            "searching" : false,
            "ajax" : {
             url:"th_datatable.php",
             type:"POST",
             data:{
              searchByName:searchByName
             }
            },
            "columns": [
                { "data": null,
                    "render": function (data, type, full, row) {
                            
                                return "<a href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"');\">"+full[0]+"</a>";
                            
                    }
                        
                },
                { "data": 1 },
                { "data": 2 },
                { "data": 3 },
                { "data": null,
                    "render": function (data, type, full, row){

                        
                        if(full[4]=="ACTIVE"){
                            return "<div id=\"itmstat"+full[0]+"\"><span class='label label-success'>&nbsp;Active&nbsp;</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"trans('"+full[0]+"','INACTIVE','Inactivate')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a></div>";
                        }
                        else{
                            return "<div id=\"itmstat"+full[0]+"\"><span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"trans('"+full[0]+"','ACTIVE','Activate')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a></div>";
                        }

                    }
                }/*,
                { "data": null,
                  "render": function(data, type, full, row){
                        
                      return "<input class='btn btn-danger btn-xs' type='button' id='row_"+full[0]+"_delete' value='delete' onClick=\"deleteRow('"+full[0]+"');\"/>";
                  }
                    
                }*/
                
            ],
            "columnDefs": [ {
              "targets": [3,4],
              "className": "text-center"
            } ],
           });
          }

	</script>

</body>
</html>
