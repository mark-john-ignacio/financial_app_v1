<?php
if(!isset($_SESSION)){
	session_start();
}

include('../../Connection/connection_string.php');

$result = mysqli_query ($con, "select cacctno as id, cacctdesc as title, IFNULL(mainacct,0) as parent_id from accounts WHERE compcode='".$_SESSION['companyid']."' and ccategory = '".$_REQUEST["Id"]."' and ctype='General' order by cacctno"); 
	
	//echo("select cacctno as id, cacctdesc as title, IFNULL(mainacct,0) as parent_id from accounts WHERE ccategory = '".$_POST["Id"]."' order by cacctno <br>");
	
	if(mysqli_num_rows($result)!=0){
	
	
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			//$myarr = array("id" => $row["id"], "title" => $row["title"], "parent_id" => $row["parent_id"]);
			$items[] = $row;
		}
		
		//$items = array($myarr);
	}



//index elements by id
foreach ($items as $item) {
    $item['subs'] = array();
    $indexedItems[$item['id']] = (object) $item;
}


//assign to parent
$topLevel = array();
foreach ($indexedItems as $item) {
    if ($item->parent_id == 0) {
        $topLevel[] = $item;
    } else {
        $indexedItems[$item->parent_id]->subs[] = $item;
    }
}

//recursive function
function renderMenu($items, $cntr) {
    if($cntr==1){
		$render = '<ul class="dropdown-menu">';
	}
	else{
		$render = '<ul>';
	}

    foreach ($items as $item) {
		$cntr = $cntr + 1;
        $render .= '<li>' . "<label><input name='selmain' id='selmain' value='".$item->id. ": " . $item->title . "' type='radio' class='btnsel'>" . $item->title . "</label>";
        if (!empty($item->subs)) {
            $render .= renderMenu($item->subs, $cntr);
        }
        $render .= '</li>';
    }

    return $render . '</ul>';
}


?>

 <div class="dropdown bts_dropdown">
   <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
   	<span id='btnlogo'>Select General Account</span> <i class="caret"></i>
   </button>


<?php
$varcntr = 1;
echo renderMenu($topLevel,$varcntr);

?>

</div>
