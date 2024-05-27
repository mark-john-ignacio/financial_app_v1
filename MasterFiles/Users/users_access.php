<?php
  if(!isset($_SESSION)){
    session_start();
  }
  $_SESSION['pageid'] = "users_Edit";

  include('../../Connection/connection_string.php');
  include('../../include/denied.php');
  include('../../include/access2.php');

  $company = $_SESSION['companyid'];
  $employeeid = $_REQUEST['empedit'];
  $arrpgist = array();
  $sql = mysqli_query($con,"select * from users_access where userid = '$employeeid'");
	if (mysqli_num_rows($sql)!=0) {
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			$arrpgist[] = $row['pageid']; 
		}
	}

  @$arrseclist[] = "";
  $sql = mysqli_query($con,"select * from users_sections where userid = '$employeeid'");
	if (mysqli_num_rows($sql)!=0) {
		while($row = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
			@$arrseclist[] = $row['section_nid']; 
		}
	}

  $lallowMRP = 0;
	$result=mysqli_query($con,"select * From company");								
  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
  {
    if($row['compcode'] == $company){
      $lallowMRP =  $row['lmrpmodules'];
    }
  } 

  $navmenu = array();
  $navmain = array();
  $navsubmain = array();
  $navitems = array();
  $navrpts = array();

  $result=mysqli_query($con,"select * From nav_menu WHERE cstatus ='ACTIVE'");								
  while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
  {
    $navmenu[] =  $row;
    if($row['main']==1){
      $navmain[] =  $row;
    }

    if($row['main']==2){
      $navsubmain[] =  $row;
    }

    if($row['main']==0){
      $navitems[] =  $row;
    }

    if($row['main']==3){
      $navrpts[] =  $row;
    }
  } 

  function getItems($main_id, $xyes){
    global $navitems;
    global $arrpgist;

    foreach($navitems as $row){
      if($row['main_id']==$main_id){
        $xroles = $row['roles'];
        if($row['roles']==null || $row['roles']==""){

          echo "<div style=\"padding-left: 20px\"><b><u><i>".$row['title']."</i></u></b><div class=\"row\">";
           getReports($row['id']);
          echo "</div></div>";
          
        }else{

          $xaar = explode(',', $xroles);

          if($xyes=="yes"){
            echo "<div style=\"padding-top: 20px\"><span style=\"font-size: 12px; color: blue\"><b>".$row['title']."</b></span><div class=\"row\">";
          }else{
            
            echo "<div style=\"padding-left: 20px\"><b><u><i>".$row['title']."</i></u></b><div class=\"row\">";
          }

          foreach($xaar as $xc){
            $xtitle = explode(':', $xc);

            $xstat = (in_array($xtitle[1],$arrpgist)) ? "checked" : "";
            $xdataval = ($xtitle[0]=="view" || $xtitle[0]=="update") ? $main_id : 0;
            echo "<div class=\"col-xs-2\" style=\"padding-left: 50px !important;\"><label><input type=\"checkbox\" name=\"chkAcc[]\" value=\"".$xtitle[1]."|".$row['id']."|".$xdataval."\" ".$xstat.">&nbsp;".$xtitle[0]."</label></div>";
          }

          echo "</div></div>";

        }

      }
    }
  }

  function getReports($main_id){
    global $navrpts;
    global $arrpgist;

    foreach($navrpts as $row){
      if($row['main_id']==$main_id){
        $xtitle = explode(':', $row['roles']);

        $xstat = (in_array($xtitle[1],$arrpgist)) ? "checked" : "";
        echo "<div class=\"col-xs-12\" style=\"padding-left: 50px !important;\"><label><input type=\"checkbox\" name=\"chkAcc[]\" data-val=\"\" value=\"".$xtitle[1]."|".$row['id']."|".$main_id."\" ".$xstat.">&nbsp;".$row['title']."</label></div>";
      }
    }
  }
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Myx Financials</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" type="text/css" href="../../Bootstrap/css/bootstrap.css"> 
  <link href="../../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>   
  <script src="../../Bootstrap/js/jquery-3.2.1.min.js"></script>
  <script src="../../Bootstrap/js/bootstrap.js"></script>
  
  <script type="text/javascript">
    function checkAll(field){
      for (var i=0;i<field.length;i++){
        var e = field[i];
        if (e.name == 'chkAcc[]'){
          if (e.disabled != true){
            e.checked = field.allbox.checked;
          }
        }
      }
    }

    function atleast_onecheckbox(e) {
      if ($("input[type=checkbox]:checked").length === 0) {
          e.preventDefault();
          alert('Atleast one checkbox or access is required!');
          return false;
      }
    }

  </script>
</head>
<body>
<form action="users_access_save.php" name="frmuser" id="frmuser" method="post" onsubmit="return atleast_onecheckbox(event)">

  <div class="row nopadding">
    <div class="col-xs-4 nopadding">
      <h3>User's Access (<?php echo $_REQUEST['empedit'];?>)     </h3>
    </div>
    <div class="col-xs-6 nopadding">
      <h3><input name="allbox" type="checkbox" value="Check All" onclick="javascript:checkAll(document.frmuser)" /> CHECK ALL </h3>
    </div>
    <div class="col-xs-2 nopadwtop2x text-right">
      <button type="submit" class="btn btn-block btn-success btn-sm">Save (F2)</button>
    </div>
  </div>

  <hr>
  <ul class="nav nav-tabs">
    <?php
      $cnt = 0;
      foreach($navmain as $rx){
          $cnt++;
          if($cnt==1){
            $style = " class=\"active\"";
          }else{
            $style = "";
          }

          echo "<li".$style."><a href=\"#menu".$rx['id']."\">".$rx['title']."</a></li>";
      }
    ?>
  </ul>


  <div class="alt2" dir="ltr" style="margin: 0px;padding: 3px;border: 0px;width: 100%;height: 90vh;text-align: left;overflow: auto">
    <div class="tab-content">

      <?php
        $cnt = 0;
        foreach($navmain as $rx){
          if($rx['id']==1){
            $xstat = (in_array("dashboard",$arrpgist)) ? "checked" : "";
            ?>  
              <div id="menu<?=$rx['id']?>" class="tab-pane fade in active" style="padding-left:10px; padding-top: 20px">
                <div class="col-xs-12" style="padding-left: 20px !important;"><label><input type="checkbox" name="chkAcc[]" data-val="" value="dashboard|1|0" <?=$xstat?>>&nbsp;Display Dashboard</label></div>
              </div>
            <?php
          }elseif($rx['id']==103){
            $xstat = (in_array("audittrail",$arrpgist)) ? "checked" : "";
            ?>  
            <div id="menu<?=$rx['id']?>" class="tab-pane fade" style="padding-left:10px; padding-top: 20px">
              <div class="col-xs-12" style="padding-left: 20px !important;"><label><input type="checkbox" name="chkAcc[]" data-val="" value="audittrail|103|0" <?=$xstat?>>&nbsp;Audit Trail</label></div>
            </div>
          <?php  
          }else{

      ?>
        <div id="menu<?=$rx['id']?>" class="tab-pane fade" style="padding-left:10px; padding-top: 20px">
          
          <?php
            $cnt2nd = 0;
            foreach($navsubmain as $rz){
              if($rz['main_id']==$rx['id']){
                $cnt2nd++;

                if($cnt2nd>1){
                  echo "<br>&nbsp;<br>";
                }
          ?>             
                  <span style="font-size: 12px; color: blue"><b><?=$rz['title']?></b></span>

                    <?php
                      getItems($rz['id'],"no");
                    ?>
          <?php

              }
            }
          ?>

         
            <?php
               getItems($rx['id'],"yes");
            ?>          
           
        </div>
      <?php
          }
        }
      ?>
    </div>
  </div>
   
  <input type="hidden" name="userid" id="userid" value="<?php echo $employeeid;?>">

</form>
</body>
</html>

<script>  
    $(document).ready(function(){
      $(".nav-tabs a").click(function(){
          $(this).tab('show');
      });

    });

  </script>
