<?php
if(!isset($_SESSION)){
	session_start();
}
require_once "../../Connection/connection_string.php";

	$company = $_SESSION['companyid'];
	$cpono = $_POST['x'];
	
  $rowPOresult = array();
	$sql = mysqli_query($con,"SELECT a.*,b.Fname,b.Minit,b.Lname,b.cemailadd FROM `quote_trans_approvals` a left join users b on a.userid=b.Userid where a.compcode='$company' and a.ctranno='$cpono' order by a.nlevel");
	
  while($rowxcv=mysqli_fetch_array($sql, MYSQLI_ASSOC)){
		$rowPOresult[] = $rowxcv;
	}

  //get ung mga levels
  $templvl = "";
  @$dlevels = array();
  foreach($rowPOresult as $rs){
    if($templvl!=$rs['nlevel']){
      $templvl=$rs['nlevel'];

      @$dlevels[] = $rs['nlevel'];
    }   
  }

?>
<style>

  .timeline {
      list-style: none;
      padding: 20px 0 20px;
      position: relative;
  }

  .timeline:before {
    top: 0;
    bottom: 0;
    position: absolute;
    content: " ";
    width: 3px;
    background-color: #eeeeee;
    left: 5%;
    margin-left: -1.5px;
  }

  .timeline > li {
    margin-bottom: 20px;
    position: relative;
  }

  .timeline > li:before,
  .timeline > li:after {
    content: " ";
    display: table;
  }

  .timeline > li:after {
    clear: both;
  }

  .timeline > li:before,
  .timeline > li:after {
    content: " ";
    display: table;
  }

  .timeline > li:after {
    clear: both;
  }

  .timeline > li > .timeline-panel {
    width: 80%;
    float: left;
    border: 1px solid #fdf5f5 ;
    border-radius: 2px;
    padding: 20px;
    position: relative;
    -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
    background-color: #fdf5f5 ;
  }

  .timeline > li > .timeline-panel:before {
    position: absolute;
    top: 26px;
    right: -15px;
    display: inline-block;
    border-top: 15px solid transparent;
    border-left: 15px solid #fdf5f5  ;
    border-right: 0 solid #fdf5f5  ;
    border-bottom: 15px solid transparent;
    content: " ";


  }

  .timeline > li > .timeline-badge {
    color: #fff;
    width: 80px;
    height: 30px;
    line-height: 30px;
    font-size: 10px;
    text-align: center;
    position: absolute;
    top: 25px;
    left: 3%;
    margin-left: -25px;
    background-color: #999999;
    z-index: 100;
  }

  .timeline > li.timeline-inverted > .timeline-panel {
    left: 15%;
  }

  .timeline > li.timeline-inverted > .timeline-panel:before {
    border-left-width: 0;
    border-right-width: 15px;
    left: -15px;
    right: auto;
  }

  .timeline > li.timeline-inverted > .timeline-panel:after {
    border-left-width: 0;
    border-right-width: 14px;
    left: -14px;
    right: auto;
  }

  .timeline-badge.primary {
      background-color: #2e6da4 !important;
  }

  .timeline-title {
      margin-top: 0;
      color: inherit;
  }

  .timeline-body > p,
  .timeline-body > ul {
      margin-bottom: 0;
  }

  .timeline-body > p + p {
    margin-top: 5px;
  }

  .box {
    float: left;
    height: 8px;
    width: 8px;
    margin-top: 6px;
    margin-right: 5px;
    clear: both;
  }

  .red {
    background-color: red;
    border: 1px solid red;
  }

  .yellow {
    background-color: #3374ff;
    border: 1px solid #3374ff;
  }

  .green {
    background-color: #4dfc06 ;
    border: 1px solid #4dfc06 ;
  }

</style>

    <ul class="timeline">
      <?php
      $cnntr = 0;
        foreach(@$dlevels as $xrs){
          $cnntr++;
      ?>
        <li class="timeline-inverted">
          <div class="timeline-badge primary">APPROVAL <?=$cnntr?></i></div>
          <div class="timeline-panel">
            <div class="timeline-body">

              <div class="col-xs-12 border-bottom border-top" style="padding: 5px !important; background-color:  #f5f3f3 !important;">
                <div class="col-xs-5 text-center"> Approver Name</div>
                <div class="col-xs-4 text-center"> Email Address </div>
                <div class="col-xs-3 text-center"> Status </div>
              </div>

              <?php
                foreach($rowPOresult as $rs){
                  if($xrs==$rs['nlevel']){


                    $cpreparedBy = $rs['Fname']." ".$rs['Minit'].(($rs['Minit']!=="" && $rs['Minit']!==null) ? " " : "").$rs['Lname'];
              ?>

              <div class="col-xs-12 border-bottom"  style="padding: 5px !important; background-color:  #ffffff !important;">

                <div class="col-xs-12">
                  <div class="col-xs-5"> 
                    <?=$cpreparedBy?>
                  </div>
                  <div class="col-xs-4"> <?=$rs['cemailadd']?> </div>
                  <div class="col-xs-3" style="padding-left: 40px !important;">
                    <?php
                      if($rs['lapproved']==0 && $rs['lreject']==0){
                        echo "<div class='box yellow'></div>Pending";
                      }else{
                        if($rs['lapproved']==1 && $rs['lreject']==0){
                          echo "<div class='box green'></div>Approved";
                        }elseif($rs['lapproved']==0 && $rs['lreject']==1){
                          echo "<div class='box red'></div>Rejected";
                        }
                      }
                    ?>
                  </div>
                </div>


                <?php
                  if($rs['lapproved']==1){
                    echo "<br><div class='col-xs-12' style='padding-left: 30px !important; background-color: #ffffff !important; font-size:12px; color:#9fa09f '><i>Date/Time Approved: ".date_format(date_create($rs['ddatetimeapp']), "M d, Y H:i:s")."</i></div>";
                  }

                  if($rs['lreject']==1){
                    echo "<br><div class='col-xs-12' style='padding-left: 30px !important; background-color: #ffffff !important; font-size:12px; color:#9fa09f '><i>Date/Time Rejected: ".date_format(date_create($rs['ddatetimereject']), "M d, Y H:i:s")."</i></div>";
                  }
                ?>

              </div>

              <?php
                  }   
                }
              ?>
              

              
            </div>
          </div>
        </li>
      <?php
        }
      ?>
    </ul>

