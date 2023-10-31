<?php
if(!isset($_SESSION)){
session_start();
}
$_SESSION['pageid'] = "Items.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];  

?>
<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../../Bootstrap/DataTable/DataTable.css">    
<link rel="stylesheet" type="text/css" href="../../global/plugins/font-awesome/css/font-awesome.min.css"/> 
<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/alert-modal.css"> 

<script type="text/javascript">
function editfrm(x,y){
	document.getElementById("txtcitemno").value = x;
	document.getElementById("frmedit").action = y;
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
        <div>
        	<div style="float:left; width:50%">
						<font size="+2"><u>Items Master List</u></font>	
          </div>
            
          <div style="float:right; width:30%; text-align:right">
            	<!--<font size="+1"><a href="javascript:;" onClick="paramchnge('ITEMTYP')">Type</a> | <a href="javascript:;" onClick="paramchnge('ITEMCLS')">Classification</a> | <a href="javascript:;" onClick="paramchnge('ITMUNIT')">UOM</a></font>	-->

						<div class="itmalert alert alert-danger text-center" style="padding: 2px !important; display: none" id="itmerr" >WRONG ERROR</div>
							
          </div>
          
        </div>
			<br><br>

			<div class="col-xs-12 nopadding">
				<div class="col-xs-5 nopadding">
						<button type="button" class="btn btn-primary btn-sm"  onClick="location.href='Items_new.php'" id="btnNew" name="btnNew"><i class="fa fa-file-text-o" aria-hidden="true"></i> &nbsp; Create New (F1)</button>

						<a href="Items_xls.php" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> &nbsp; Export To Excel</a>
				</div>

        <div class="col-xs-1 nopadwtop" style="height:30px !important;">
          <b> Search Item: </b>
        </div>
				<div class="col-xs-3 text-right nopadding">
					<input type="text" name="searchByName" id="searchByName" value="" class="form-control input-sm" placeholder="Enter Code or Desc...">
				</div>

				<div class="col-xs-3 text-right nopadwleft">
					<select id="seltype" name="seltype" class="form-control input-sm selectpicker"  tabindex="4">
							<option value="">ALL</option>

                    <?php
                        $sql = "select * from groupings where ctype='ITEMTYP' order by cdesc";
                        $result=mysqli_query($con,$sql);
                        if (!mysqli_query($con, $sql)) {
                            printf("Errormessage: %s\n", mysqli_error($con));
                        }			
            
                        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
                            {
                    ?>   
                        <option value="<?php echo $row['ccode'];?>"><?php echo $row['cdesc']?></option>
                    <?php
                        }                        
                    ?>     
                    </select>
				</div>

			</div>


            
            
            
			
			<table id="MyTable" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="100">Item Code</th>
						<th>Description</th>
                        <th width="70">Main UOM</th>
						<th width="120" class="text-center">Price History</th>
						<th width="70">Status</th>
					</tr>
				</thead>

			</table>

		</section>
	</div>		


<!-- PURCH COST VIEW MODAL-->

<div class="modal fade" id="myPurchModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModalLabel"><b>Purchase Cost History</b></h5>        
      </div>

	  <div class="modal-body" style="height: 40vh" id="modBody">
      	
	  </div>
      
      <div class="modal-footer">
                <button type="button" class="btn btn-danger  btn-sm" data-dismiss="modal">Close</button>
	  </div>

    </div>
  </div>
</div>

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

<form name="frmedit" id="frmedit" method="post" action="Items_edit.php">
	<input type="hidden" name="txtcitemno" id="txtcitemno" />
</form>		

	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" language="javascript" src="../../Bootstrap/js/bootstrap.js"></script>		
	<script type="text/javascript" language="javascript" src="../../Bootstrap/DataTable/jquery.dataTables.min.js"></script>
	
	<script>
	$(document).ready(function() {
		
		fill_datatable();	
		$("#searchByName").keyup(function(){
		   var searchByName = $('#searchByName').val();
			 var searchByType = $('#seltype').val();
		  // if(searchByName != '')
		  // {
		    $('#MyTable').DataTable().destroy();
		    fill_datatable(searchByName,searchByType);
		 //  }
		});

		$("#seltype").on("change", function(){
			var searchByName = $('#searchByName').val();
			 var searchByType = $('#seltype').val();

		    $('#MyTable').DataTable().destroy();
		    fill_datatable(searchByName,searchByType);

		});

	});

	$(document).keydown(function(e) {
		if(e.keyCode == 112){//F1
			if(document.getElementById("btnNew").className=="btn btn-primary btn-sm"){
				e.preventDefault();
				window.location.href='Items_new.php';
			}
		}
	});
	
	$(document).on("click", ".viewCost", function() {
		var myId = $(this).data('id');
		var myLabel = $(this).data('label');
		var myVal = $(this).data('val');
		var myPTyp = $(this).data('ptyp');
		
		$("#myModalLabel").html("<b>Top 10 (Latest) "+myLabel+" History ("+myId+")</b>");
		
			$.ajax({
				type: "GET", 
				url: "Items_getHistory.php",
				data: "id="+myVal+"&itm="+myId+"&ptyp="+myPTyp,
				success: function(html) {
					$("#modBody").html(html);
				}
			});

	});
	

	function trans(code,stat,msg){

		$("#typ").val(stat);
		$("#modzx").val(code);

		$("#AlertMsg").html("");
									
		$("#AlertMsg").html("Are you sure you want to "+msg+" Item Code: "+code);
		$("#alertbtnOK").hide();
		$("#OK").show();
		$("#Cancel").show();
		$("#AlertModal").modal('show');

	}

	function setStat(dstat){

		if(dstat=="OK"){
			code = $("#modzx").val();
			stat = $("#typ").val();

			$.ajax ({
				url: "th_itmsetstat.php",
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
						$("#itmerr").css({'display':'inline', 'padding':'8px'});

					}
					else{

						$("#itmerr").html("<b>Error: </b>"+ data);
						$("#itmerr").attr("class", "itmalert alert alert-danger")
						$("#itmerr").css({'display':'inline', 'padding':'8px'});

					}
				}
			
			});
		}
		
		$("#AlertModal").modal('hide');
		

	}


  
		  function fill_datatable(searchByName = '', searchByType = '')
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
		      searchByName:searchByName, searchByType:searchByType
		     }
		    },
		    "columns": [
				{ "data": null,
					"render": function (data, type, full, row) {
							
								return "<a href=\"javascript:;\" onClick=\"editfrm('"+full[0]+"','Items_edit.php');\">"+full[0]+"</a>";
							
					}
						
				},
				{ "data": 1 },
				{ "data": 2 },
				{ "data": null,
					"render": function (data, type, full, row) {
							
								return "<div class=\"col-sm-12 nopadding\"><div class=\"col-sm-6 nopadding\"><a href=\"javascript:;\" data-toggle=\"modal\" data-target=\"#myPurchModal\" data-id=\""+full[0]+"\" data-label=\"Purchase Cost\" data-val=\"Purch\" class=\"viewCost\"><span class='label label-primary'>Purchase</span></a></div><div class=\"col-sm-6 nopadwleft\"><a href=\"javascript:;\" data-toggle=\"modal\" data-target=\"#myPurchModal\" data-id=\""+full[0]+"\" data-label=\"Sales Price\" data-val=\"Sales\" class=\"viewCost\"><span class='label label-info'>&nbsp;&nbsp;&nbsp;Sales&nbsp;&nbsp;&nbsp;</span></a>";
							
					},
				},
				{ "data": null,
					"render": function (data, type, full, row){

						
						if(full[3]=="ACTIVE"){
						 	return "<div id=\"itmstat"+full[0]+"\"><span class='label label-success'>&nbsp;Active&nbsp;</span>&nbsp;&nbsp;<a id=\"popoverData1\" href=\"#\" data-content=\"Set as Inactive\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"trans('"+full[0]+"','INACTIVE', 'Inactivate')\" ><i class=\"fa fa-refresh\" style=\"color: #f0ad4e\"></i></a></div>";
						}
						else{
							return "<div id=\"itmstat"+full[0]+"\"><span class='label label-warning'>Inactive</span>&nbsp;&nbsp;<a id=\"popoverData2\" href=\"#\" data-content=\"Set as Active\" rel=\"popover\" data-placement=\"bottom\" data-trigger=\"hover\" onClick=\"trans('"+full[0]+"','ACTIVE','Activate')\"><i class=\"fa fa-refresh\" style=\"color: #5cb85c\"></i></a></div>";
						}

					}
				}
				//,
				//{ "data": null,
				// 	"render": function(data, type, full, row){
						
				//		return "<input class='btn btn-danger btn-xs' type='button' id='row_"+full[0]+"_delete' value='delete' onClick=\"deleteRow('"+full[0]+"');\"/>";
				//	}
					
				//}
				
        	],
					"columnDefs": [ 
						{
							"targets": 3,
							"orderable": false
						} 
					]
		   });
		  }
		  
		
		function deleteRow(xid){
			$.ajax ({
				url: "../th_delete.php",
				data: { code: xid,  id: "item" },
				async: false,
				dataType: "text",
				success: function( data ) {
					//alert(jQuery.type(data));
					if(data.trim() != "True"){
						$("#itm"+xid).html("<b>Error: </b>"+ data);
						$("#itm"+xid).attr("class", "itmalert alert alert-danger nopadding")
						$("#itm"+xid).css('display', 'inline');
					}
					else{
					  	location.reload();
					}
				}
			
			});
		}
	</script>


</body>
</html>