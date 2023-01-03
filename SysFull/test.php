    <!DOCTYPE html>
    <html>
    <head>


    <script type="text/javascript">
    function testit() {

    var x = alertBox('Enter your firstname','prompt','John Smith')

    alert(x)

    }

    function alertBox(text, type, ptext) {

        var button  =   '<div id="alertBox_button_div" ><input id="alertBox_button" class="button" style="margin: 7px;" type="button" value="Close" onclick="alertBox_hide()"></div>'

        var field   =   '<div><input id="ptext" class="field" type="text"></div>'

        if (type == "err") {
            document.getElementById('alertBox_text').innerHTML = text + button
            document.getElementById('alertBox_text').style.color = "#FF0000"
            document.getElementById('alertBox_text').style.top = "50%"
        }
        else if (type == "ok") {
            document.getElementById('alertBox_text').innerHTML = text + button
            document.getElementById('alertBox_text').style.top = "50%"
        }
        else if (type == "prompt") {
            document.getElementById('alertBox_text').innerHTML = text + field + button
            document.getElementById('alertBox_text').style.top = "25%"
            document.getElementById('alertBox_button').value = "OK"
            if (ptext) { document.getElementById('ptext').value = ptext }

        }
        else {
            document.getElementById('alertBox_text').innerHTML = text
        }

        document.getElementById('alertBox_container').style.visibility = 'visible'

    }//end function

    function alertBox_hide() {

        document.getElementById('alertBox_container').style.visibility = 'hidden'

    }
    </script>



    <style type="text/css">
    .field {
        border: 1px solid #808080;
        width: 475px;
        font-family: Arial;
        font-size: 9pt;
        padding-left: 3px;
        font-weight: bold;
        margin: 1px;
    }
    #alertBox {
        height: 100px;
        width: 500px;
        bottom: 50%;
        right: 50%;
        position: absolute;
        font-family: Arial;
        font-size: 9pt;
        visibility: hidden;
    }

    #alertBox_container {
        border: 1px solid #808080;
        left: 50%;
        padding: 10px;
        top: 50%;
        margin: 0;
        padding: 0;
        height: 100%;
        border: 1px solid rgb(128,128,128);
        height: 100%;
        position: relative;
        color: rgb(11,63,113);
    }

    #alertBox_titlebar {
        cursor: pointer;
        height: 22px;
        width: 100%;
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff", endColorstr="#cdcdcd");
        line-height:22px;
        font-weight: bold;
    }
    #alertBox_close {
        line-height: 10px;
        width: 17px;

        margin-top: 2px;
        margin-right: 2px;
        padding: 1px;

        position:absolute;
        top:0;
        right:0;

        font-size: 10px;
        font-family: tahoma;
        font-weight: bold;

        color: #464646;
        border: 1px solid;
        border-color: #999 #666 #666 #999;
        background-color:#ccc;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ffffff', EndColorStr='#E7E7E7'); 
    }
    #alertBox_close:hover {
        background-color: #ddd;        
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#fafafa', EndColorStr='#dddddd');
        color: #000000;
    }
    #alertBox_text {
        position: absolute;
        width: 100%;
        height: auto;
        top: 50%;
        text-align: center;
    }
    .button {
        color:              #464646;
        font-family:        Arial;
        font-size:          9pt;
        height:             23px;
        border:             1px solid;
        border-color:       #999 #666 #666 #999;
        background-color:   #ccc;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ffffff', EndColorStr='#E7E7E7');
        width: 67px;
    }

    }
    .button:hover {
        background-color: #ddd;        
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#fafafa', EndColorStr='#dddddd');
        color: #000000;
    }
    </style>
    </head>
    <body>

    <input type="button" value="testme" onclick="testit()">
    <br>
        <div id="alertBox">
            <div id="alertBox_container">
                <div id="alertBox_titlebar"><span style="padding-left: 3px;">IMTS</span></div>
                <div><input id="alertBox_close" type="button" value="X" onclick="alertBox_hide()"></div>
                <div id="alertBox_text"></div>
            </div>
        </div>

    </body>
    </html>

Reply With Quote Reply With Quote
10-23-2013, 08:21 AM #2
