<?php
if(!isset($_SESSION)){
	session_start();
}
$_SESSION['pageid'] = "Accounts.php";

include('../../Connection/connection_string.php');
include('../../include/denied.php');
include('../../include/access2.php');

$company = $_SESSION['companyid'];
$result = mysqli_query ($con, "select cacctno,cacctid,cacctdesc,mainacct,ccategory,nlevel,ctype from accounts WHERE compcode = '".$company."'"); 
$row = $result->fetch_all(MYSQLI_ASSOC);

$cats = [];


// store the categories in a 2-dim array with arrays of cats belonging to each parent 
foreach ($row as $r) {
	if (!isset($cats[$r['mainacct']])) {
			$cats[$r['mainacct']] = [];
	}
	$cats[$r['mainacct']][] = [ 'id' => $r['cacctid'], 'name' => $r['cacctdesc'], 'typ' => $r['ccategory'] ];
}

function subcats(&$cats, $parent=0, $level=0, $maincat)
{
    if (!isset($cats[$parent])) return;
    $subcats = $cats[$parent];
    foreach ($subcats as $sc) {

			if($sc['typ']==$maincat){
        $cls = "class='level$level'";
        echo "<li $cls>{$sc['id']} - {$sc['name']}";
        if (isset($cats[$sc['id']])) {                  // if it has subcategories, 
            echo "\n<ul>\n" ;
            subcats( $cats, $sc['id'], $level+1, $maincat);       //     re-call function to print its subcats
            echo "</ul>\n" ;
        }
        echo "</li>\n";
			}
    }
}  


//print_r($row);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">

	<title>Myx Financials</title>

	<link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css">    
	<link href="../../Bootstrap/css/jquery.bootstrap.treeselect.css" rel="stylesheet">


	<script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
	<script src="../../Bootstrap/js/bootstrap3-typeahead.js"></script>
	<script src="../../Bootstrap/js/bootstrap.js"></script>

	<style>
	

		/*  acct tab
		div.acct-tab-container{
			z-index: 10;
			background-color: #ffffff;
			padding: 0 !important;
			border-radius: 4px;
			-moz-border-radius: 4px;
			border:1px solid #ddd;
			margin-top: 20px;
			margin-left: 50px;
			-webkit-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			box-shadow: 0 6px 12px rgba(0,0,0,.175);
			-moz-box-shadow: 0 6px 12px rgba(0,0,0,.175);
			background-clip: padding-box;
			opacity: 0.97;
			filter: alpha(opacity=97);
		} */
		div.acct-tab-menu{
			padding-right: 0;
			padding-left: 0;
			padding-bottom: 0;
		}
		div.acct-tab-menu div.list-group{
			margin-bottom: 0;
		}
		div.acct-tab-menu div.list-group>a{
			margin-bottom: 0;
		}
		div.acct-tab-menu div.list-group>a .glyphicon,
		div.acct-tab-menu div.list-group>a .fa {
			color: #6798e0;
		}
		div.acct-tab-menu div.list-group>a:first-child{
			border-top-right-radius: 0;
			-moz-border-top-right-radius: 0;
		}
		div.acct-tab-menu div.list-group>a:last-child{
			border-bottom-right-radius: 0;
			-moz-border-bottom-right-radius: 0;
		}
		div.acct-tab-menu div.list-group>a.active,
		div.acct-tab-menu div.list-group>a.active .glyphicon,
		div.acct-tab-menu div.list-group>a.active .fa{
			background-color: #6798e0;
			background-image: #6798e0;
			color: #ffffff;
		}
		div.acct-tab-menu div.list-group>a.active:after{
			content: '';
			position: absolute;
			left: 100%;
			top: 50%;
			margin-top: -13px;
			border-left: 0;
			border-bottom: 13px solid transparent;
			border-top: 13px solid transparent;
			border-left: 10px solid #6798e0;
		}

		div.acct-tab-content{
			background-color: #ffffff;
			/* border: 1px solid #eeeeee; */
			padding-left: 20px;
			padding-top: 10px;
			height: 500px;
			overflow: auto;
		}

		div.acct-tab div.acct-tab-content:not(.active){
			display: none;
		}
		a.list-group-item{
			padding-top: 20px;
			padding-bottom: 20px;
		}

	</style>
</head>

<body style="padding:5px">
	<input type="hidden" value='<?=json_encode($row)?>' id="hdnaccts">
	<div>
		<section>

				<div>
        	<div style="float:left; width:50%">
						<font size="+2"><u>Chart of Accounts</u></font>	
          </div>
        </div>
				<br><br>
        <button type="button" data-toggle="modal" class="btn btn-primary btn-md" id="btnadd" name="btnadd"><span class="glyphicon glyphicon glyphicon-file"></span>&nbsp;Create New (F1)</button>
				<br><br>	

					<div class="acct-tab-container row-xs-height">
        		<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 acct-tab-menu col-xs-height">

            	<div class="list-group"> 
								<a href="#" class="list-group-item idp-group-item active text-center">
									<div>
											<strong>Assets</strong>
									</div>
                </a>

								<a href="#" class="list-group-item idp-group-item text-center">
									<div>
										<strong>Liabilities</strong>
									</div>
                </a>

								<a href="#" class="list-group-item idp-group-item text-center">
									<div>
										<strong>Equity</strong>
									</div>
                </a>

								<a href="#" class="list-group-item idp-group-item text-center">
									<div>
										<strong>Income</strong>
									</div>
								</a>

								<a href="#" class="list-group-item idp-group-item text-center">
									<div>
										<strong>Expenses</strong>
									</div>
								</a>

            	</div>
       			</div>

        		<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 acct-tab col-xs-height col-middle">
							<!-- assets section -->
							<div class="acct-tab-content active" id="divassets">

								<ul>
										<?=subcats($cats, 0, 0, "ASSETS")?>
								</ul>
                     
                <div class="clearfix"></div>
            	</div>

							<!-- liabilities section -->
							<div class="acct-tab-content"  id="divliabilities">
								<ul>
										<?=subcats($cats, 0, 0, "LIABILITIES")?>
								</ul>
                <div class="clearfix"></div>
            	</div>

							<!-- liabilities section -->
							<div class="acct-tab-content"  id="divequity">
								<ul>
										<?=subcats($cats, 0, 0, "EQUITY")?>
								</ul>
                <div class="clearfix"></div>
            	</div>

							<!-- equity section -->
							<div class="acct-tab-content"  id="divincome">
								<ul>
										<?=subcats($cats, 0, 0, "INCOME")?>
								</ul>
                <div class="clearfix"></div>
            	</div>

							<!-- income section -->
							<div class="acct-tab-content"  id="divexpenses">
								<ul>
										<?=subcats($cats, 0, 0, "EXPENSES")?>
								</ul>
                <div class="clearfix"></div>
            	</div>

       		 	</div>
   	 			</div>

		</section>
	</div>		



	<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        
        <h5 class="modal-title" id="myModalLabel"><b>New Account</b></h5>
        
      </div>

			<form action="accounts_add.php" method="POST" name="frmnew" id="frmnew">

      	<div class="modal-body">

					<div class="col-sm-12 nopadding">
						<div class="col-sm-6 nopadding">
							<label class="radio-inline">
								<input type="radio" name="radtype" id="radtypegen" value="General"> Title
							</label>
						</div>
						<div class="col-sm-6 nopadding">
							<label class="radio-inline">
								<input type="radio" name="radtype" id="radtypedet" value="Details" checked> Detail Account
							</label>
						</div>
					</div>

					<div class="col-sm-12 nopadwtop2x">
						<div class="col-sm-4 nopadding">
							<b>Account Code:</b>
						</div>
						<div class="col-sm-6 nopadding">
							<input type='text' class="form-control input-sm" id="cacctid" name="cacctid" value=""/>
						</div>
					</div>

					<div class="col-sm-12 nopadwtop">
						<div class="col-sm-4 nopadding">
							<b>Account Description:</b>
						</div>
						<div class="col-sm-6 nopadding">
							<input type='text' class="form-control input-sm" id="cacctdesc" name="cacctdesc" value=""/>
						</div>
					</div>

					<div class="col-sm-12 nopadwtop">
						<div class="col-sm-4 nopadding">
							<b>Account Category:</b>
						</div>
						<div class="col-sm-6 nopadding">
							<select name="selcat" id="selcat" class="form-control input-sm">
								<option value="ASSETS">ASSETS</option>
								<option value="LIABILITIES">LIABILITIES</option>
								<option value="EQUITY">EQUITY</option>
								<option value="INCOME">INCOME</option>
								<option value="EXPENSES">EXPENSES</option>
							</select>
						</div>
					</div>

					<div class="col-sm-12 nopadwtop">
						<div class="col-sm-4 nopadding">
							<b>Level:</b>
						</div>
						<div class="col-sm-6 nopadding">
							<div class="col-sm-12 nopadding">
								<div class="col-sm-4 nopadding">
									<select name="selvl" id="selvl" class="form-control input-sm"> 
										<option value="1">1</option> 
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
									</select>
								</div>
								<div class="col-sm-8 text-right nopadding">
									<label class="checkbox-inline">
										<input type="checkbox" name="chkcontra" id="chkcontra" value="1"> Contra Account
									</label>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-12 nopadwtop">
						<div class="col-sm-4 nopadding">
							<b>Header Account:</b>
						</div>
						<div class="col-sm-6 nopadding">
							<select name="selhdr" id="selhdr" class="form-control input-sm disabled" disabled="disabled">
								<option value="">Select Header</option>
							</select>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="submit" id="btnSave" name="btnSave" class="btn btn-primary">Save</button>
				</div>

			</form>
		</div>
	</div>
</div>

<!-- MODAL -->


<!-- SAVING MODAL -->
<div class="modal fade" id="AlertModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-top">
            <div class="modal-content">
               <div class="alert-modal-danger">
                  <p id="AlertMsg"></p>
               </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->	

</body>
</html>

<script type="text/javascript">
	$(document).ready(function() {
			$("div.acct-tab-menu>div.list-group>a").click(function(e) {
					e.preventDefault();
					$(this).siblings('a.active').removeClass("active");
					$(this).addClass("active");
					var index = $(this).index();
					$("div.acct-tab>div.acct-tab-content").removeClass("active");
					$("div.acct-tab>div.acct-tab-content").eq(index).addClass("active");
			});


		// Adding new account
		$("#btnadd").on("click", function() {
			$("#divmainacc").html("");

			$('#frmnew').trigger("reset");


			$('#myModal').modal('show');
		});

		$("#selvl").on("change", function() {

			var html = [];


			if($(this).val()>1){
				$("#selhdr").attr("disabled", false); 

				var lvl = parseInt($(this).val()) - 1;
				var hdrmain = $("#selcat").val();

				var obj = $("#hdnaccts").val();

				$.each(jQuery.parseJSON(obj), function() {
					if(lvl==this['nlevel']  && hdrmain==this['ccategory'] && this['ctype']=="General"){
						html.push('<option value="' +this['cacctid'] + '">' + this['cacctdesc'] + '</option>');
					}
				}); 
			}

			$('#selhdr').html(html.join(''));

		});


		$("#frmnew").on('submit', function (e) {
		e.preventDefault();

			var form = $("#frmnew");
			var formdata = form.serialize();
				$.ajax({
				url: 'Accounts_add.php',
				type: 'POST',
				async: false,
				data: formdata,
				success: function(data) {
					if(data.trim()!="False"){
						$('#myModal').modal('hide');

						alert(data);
						location.reload();
					}else{
						alert("Error saving new account!");	
					}
				}
	    });							

		});

	});
</script>
