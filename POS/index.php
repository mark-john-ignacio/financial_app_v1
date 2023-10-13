<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    $company = $_SESSION['companyid'];

    include('../Connection/connection_string.php');
    include('../include/denied.php');
    include('../include/access2.php');

    $category = [];
    $items = [];
    $date = date('Y-m-d');

    $query = mysqli_query($con,"select * from company where compcode='$company'");
    if(mysqli_num_rows($query) !== 0 ){
        while($row = $query -> fetch_assoc()){
            $companyName = $row['compname'];
            $companyAddress  = $row['compadd'];
            $companyTin = $row['comptin'];
        }
    }


    $sql =  "SELECT * FROM groupings WHERE ctype='ITEMCLS' AND ccode in (select cclass From items where compcode='$company' and cstatus = 'ACTIVE' and ctradetype='Trade') order by cdesc";
    $query = mysqli_query($con, $sql);
    while($row = $query -> fetch_assoc()){
        array_push($category, $row);
    }

    $sql = "select a.cpartno, a.cpartno as cscancode, a.citemdesc, 0 as nretailcost, 0 as npurchcost, a.cunit, a.cstatus, 0 as ltaxinc, a.cclass, 1 as nqty, a.cuserpic
            from items a 
            left join
                (
                    select a.citemno, COALESCE((Sum(nqtyin)-sum(nqtyout)),0) as nqty
                    From tblinventory a
                    right join items d on a.citemno=d.cpartno and a.compcode=d.compcode
                    where a.compcode='$company' and  a.dcutdate <= '$date' and d.cstatus = 'ACTIVE'
                    group by a.citemno
                ) c on a.cpartno=c.citemno
            WHERE a.compcode='$company' and a.cstatus = 'ACTIVE' and a.ctradetype='Trade' order by a.cclass, a.citemdesc";

    $query = mysqli_query($con, $sql);
    if(mysqli_num_rows($query) != 0) {
        while($row = $query -> fetch_assoc()){
            array_push($items, $row);
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Myx Financials</title>
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/bootstrap2.css?v=<?php echo time();?>">
	<link href="../global/css/googleapis.css" rel="stylesheet" type="text/css"/>
	<link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/slick/slicksize.css">
    <link rel="stylesheet" type="text/css" href="../Bootstrap/css/keypadz.css?v=<?php echo time();?>">

    <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
    <script src="../Bootstrap/js/bootstrap3-typeahead.js"></script>
    <script src="../include/autoNumeric.js"></script>

    <script src="../Bootstrap/js/bootstrap.js"></script>
    <script src="../Bootstrap/js/moment.js"></script>
    <script src="../Bootstrap/slick/slick.js" type="text/javascript" charset="utf-8"></script>


    <style>
        #filter {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        #filter > div{
            padding: 5px;
        }

        #item-wrapper {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            overflow: auto;
            text-align: center;
        }
        
        #category-wrapper {
            display: grid;
            padding-top: 10px;
            text-align: center;
            grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
            grid-template-rows: 1fr;
            max-width: 5fr;
            overflow: hidden; 
        }
        
        #button-wrapper {
            display: grid;
            padding-top: 10px;
            text-align: center;
            grid-gap: 4px;
            grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
            grid-template-rows: 1fr;
            max-width: 4fr;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div stlye="display: fixed">
            <div class='row nopadwtop2x' id='header' style="background-color: #2d5f8b; height:65px; margin-bottom: 5px !important">
                <div  style="float: left;display: block;width: 235px;height: 57px;padding-left: 20px;padding-right: 20px;">
                    <img src="../images/LOGOTOP.png" width="150" height="50"/>
                </div>
            </div>

            <div class='container nopadding' id='POSBody' style='display: flex; width: 100%;'>
                <div class="col" style="width: 50%; padding: 5px;">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <div class="digi col-lg-6 nopadding text-left">
                                    <span class="date">
                                        Cashier: <?php echo $_SESSION['employeename']; ?>
                                    </span>    
                                </div>
                            </td>
                            <td align='right'>
                                <div>
                                    <span class="date"><?=date("F d, Y");?></span>
                                    <span class="digital-clock time"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class='input-group margin-bottom-sm'>
                                    <input type="text" name='barcode' id='barcode' class='form-control input-sm' autocomplete="off">
                                    <span class='input-group-addon'><i class='fa fa-barcode fa-fw'></i></span>
                                </div>
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style='padding-top: 20px'>
                                <div style='height: 3.5in; max-height: 3.5in; overflow: auto;'>
                                    <table class='table' id='listItem' style="width: 100%; ">
                                        <thead style='background-color: #019aca'>
                                            <tr>
                                                <th style="width: 60%;">Item</th>
                                                <th style="text-align: center;">UOM</th>
                                                <th style="text-align: center;">Quantity</th>
                                                <th style="text-align: center;">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>


                <div class='col' style='width: 50%; padding: 10px;'>
                    <table class='table' style="width: 100%;">
                        <tr>
                            <td>
                                <div id='filter'>
                                    <div class='input-group'>
                                        <span class='input-group-addon'><i class='fa fa-user'></i></span><input class='form-control input-sm' type="text" name='types' id='types' autocomplete="off">
                                    </div>
                                    <div class='input-group'>
                                        <select name="orderType" id="orderType" class='form-control input-sm'>
                                            <option value="" selected disabled>--- Select Order Type  ---</option>
                                            <option value="Dine">Dine-In</option>
                                            <option value="Out">Take-Out</option>
                                            <option value="Delivery">Delivery</option>
                                        </select>
                                    </div>
                                    <div class='input-group'>
                                        <select name="table" id="table"  class='form-control input-sm'>
                                            <option value="" selected disabled>--- Select Table ---</option>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style='height: 300px; overflow: auto;'>
                                    <div id='item-wrapper'>
                                        <?php foreach($items as $list):?>
                                        
                                            <div id='items' name="<?= $list['cscancode'] ?>" style='height: 50px'><font size='-2'><?php echo $list["citemdesc"]; ?></font></div>
                                        
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div class='col-lg-12 '>          
                        <section style='width: 90%; padding: 10px' class="regular slider">
                            <?php foreach($category as $list):?>
                                <div style="height:63px; 
                                    word-wrap:break-word;
                                    background-color:#019aca; 
                                    border:solid 1px #036;
                                    padding:3px;
                                    text-align:center;" class="itmclass" data-clscode="<?= $list['ccode'] ?>">
                                        <font size="-2"><?= $list['cdesc'] ?></font>
                                </div>

                            <?php endforeach; ?>
                        </section>
                    </div>


                    <div id='button-wrapper' class='col-lg-12 nopadwtop'>
                            <button class="form-control btn btn-sm btn-success" name="btnPay" id="btnPay" type="button">
                                <i class="fa fa-money fa-fw fa-lg" aria-hidden="true"></i>&nbsp; PAYMENT (F2)
                            </button>
                            <button class="form-control btn btn-sm btn-primary" name="btnHold" id="btnHold" type="button">
                               <i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true"></i>&nbsp; HOLD (INS)
                            </button>
                            <button class="form-control btn btn-sm btn-warning" name="btnRetrieve" id="btnRetrieve1" type="button">
                               <i class="fa fa-bar-chart fa-fw fa-lg" aria-hidden="true"></i>&nbsp; RETRIEVE (F4)
                            </button>
                            <button class="form-control btn btn-sm btn-danger" name="btnVoid" id="btnVoid" type="button">
                                <i class="fa fa-plus fa-fw fa-lg" aria-hidden="true"></i>&nbsp;VOID (DEL)
                            </button>
                        </div>
                </div>
            </div>
    </div>

    <div class='modal fade' id='mymodal' role="dialog">
        <div class="modal-dialog" role="document">
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="invheader">Void Item</h3>
                </div>
                <div class='modal-body' style='height: 4in;'>
                    <table class='table' id='VoidList' style="width: 100%; ">
                        <thead style='background-color: #019aca'>
                            <tr>
                                <th>&nbsp;</th>
                                <th style="width: 60%;">Item</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">Quantity</th>
                                <th style="text-align: center;">Amount</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class='modal-footer' style='display: Relative; width: 100%;'>
                    <div style='right: 0px'>
                        <button class='btn btn-danger' id='VoidSubmit' style='padding: 5px; width: 1in;'>Void</button>
                    </div>
                </div>
            </div>     
        </div>
    </div>
</body>
</html>

<script type='text/javascript'>
    var itemStored = [];
    
    $(document).ready(function(){
        clockUpdate();
        setInterval(clockUpdate, 1000);
        $(".regular").slick({
            dots: false,
            infinite: true,
            slidesToShow: 6,
            slidesToScroll: 5
        });

        $(".itmclass").on("click", function() {
            const ClassID = $(this).attr("data-clscode");
            
            $('.itmslist').each(function(i, obj) {
                itmcls = $(this).attr("data-itemcls");
                
                if(itmcls==ClassID){
                    $(this).show();
                }else if(itmcls!=ClassID){
                    $(this).hide();
                }
            });		
        });


        

        $('#item-wrapper').on('click', '#items',function(){
            const name = $(this).attr("name");
            insert_item(name)
        })

        // $('#listItem').click('#delete',function(){
        //         const named = $(this).attr("name")
        //         itemStored.splice(named, 1);

        //         table_store(itemStored);
            
        // })

        $('#VoidSubmit').click(function(){
            $("input:checkbox[name=itemcheck]:checked").each(function(){
                itemStored.splice($(this).val(), 1);

                table_store(itemStored);
            });
        })

        $('#btnVoid').click(function(){
            if($('#orderType').find(":selected").val() == ""){
                return alert("Please Fillup Order Type to procceed!");
            }

            if(itemStored.length === 0) {
                return alert('Transaction is empty!')
            }

            $('#mymodal').modal("show");
            table_store(itemStored)
        })


        $('#btnHold').on('click', function(){
            //storing input values in array
            if($('#orderType').find(":selected").val() == ""){
                return alert("Please Fillup Order Type to procceed!");
            }

            if(itemStored.length === 0){
                return alert('Transaction is empty! cannot hold transaction');
            }

            const quantity = [];

            $('input[name*="qty"]').each((index, item) => {
                quantity.push($(item).val())
            })
                
            itemStored.map((item, index) => {
                
                $.ajax({
                    url: 'Function/th_holdtransaction.php',
                    data: {
                        name: item.name,
                        unit: item.unit,
                        table: ($('#table') ? $('#table').val() : null),
                        type: $('#orderType').val(),
                        quantity: item.quantity,
                    },
                    dataType: 'json',
                    async: false,
                    success: function(res){
                        console.log(res.data)
                    },
                    error: function(res){
                        console.log(res)
                    }
                })
            })
        });
    })

    function insert_item(partno){
        $.ajax({
            url: 'Function/ItemList.php',
            data: {
                code: partno
            },
            dataType: 'json',
            async: false,
            success: function(res) {
                if(res.valid){
                    var quantity = 1;
                    res.data.map((item, index) => {
                        
                        duplicate(item)
                        
                    })
                    console.log(itemStored)
                    table_store(itemStored);
                } else {
                    console.log('Item has no quantity')
                }
                
            },
            error: function(res){
                console.log(res)
            }
        })
    }

    function duplicate(data){
        const partno = data.partno
        console.log(partno)
        if(itemStored.length === 0){
            return itemStored.push({
                name: data.name,
                unit: data.unit,
                quantity: 1
            })
        }
        if(itemStored.some(item => item.partno === data.partno)){
            itemStored.push({
                name: data.name,
                unit: data.unit,
                quantity: 1
            })
        } 
       
    }

    // function duplicate(data){
    //     const quantity = 1;
    //     itemStored.map((item, index) => {
    //         if(item.name === data){
    //             quantity += 1;
    //         }
    //     });
    //     if(itemStored['name'].includes(item.name)){
    //         itemStored[] +1;
    //     }
    // }

    function table_store(items){
        $('#listItem > tbody').empty();
        $('#VoidList > tbody').empty();
        items.map((item, index) => {
            $("<tr>").append(
                $("<td>").text(item.name),
                $("<td>").text(item.unit),
                $("<td align='center'>").html("<input type='number' id='qty' name='qty[]' class='form-control input-sm' style='width:60px' value='"+item.quantity+"'/>"),
                $("<td>").text('s')
            ).appendTo("#listItem > tbody")


            $("<tr>").append(
                $("<td align='center'>").html("<input type='checkbox' name='itemcheck' value='"+item.name+"'/>"),
                $("<td>").text(item.name),
                $("<td>").text(item.unit),
                $("<td align='center'>").html("<input type='number' id='qty' name='qty[]' class='form-control input-sm' style='width:60px' value='"+item.quantity+"'/>"),
                $("<td>").text('s')
            ).appendTo("#VoidList > tbody")
        })
    }

    function clockUpdate() {
        var date = new Date();
        //$('.digital-clock').css({'color': '#fff', 'text-shadow': '0 0 6px #ff0'});
        function addZero(x) {
            if (x < 10) {
            return x = '0' + x;
            } else {
            return x;
            }
        }

        function twelveHour(x) {
            if (x > 12) {
            return x = x - 12;
            } else if (x == 0) {
            return x = 12;
            } else {
            return x;
            }
        }

        var h = addZero(twelveHour(date.getHours()));
        var m = addZero(date.getMinutes());
        var s = addZero(date.getSeconds());

        $('.digital-clock').text(h + ':' + m + ':' + s)
    }
</script>