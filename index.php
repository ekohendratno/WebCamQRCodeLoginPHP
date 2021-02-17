<?php

if( !session_id() )
{
    session_start();
}

if(@$_SESSION['logged_in'] == true){
    header("Location: home.php");
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

<script type="text/javascript" src="./js/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="./js/html5-qrcode.min.js"></script>

<style>
    #reader {
        width: 640px;
        height: 480px;
    }
    @media(max-width: 640px) {
        #reader {
            width: 320px;
            height: 240px;
        }
    }
    .empty {
        display: block;
        width: 100%;
        height: 20px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <button id="start">Start Scanning</button>
        <div class="empty"></div>
        <div id="reader" style="display: inline-block;"></div>
        <div class="empty"></div>
        <div id="scanned-result"></div>
    </div>
</div>
<script>
    var scanning = false;
    var html5qrcode = new Html5Qrcode("reader", true);
    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete" || document.readyState === "interactive") {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }
    function startScanning(facingMode) {
        console.log(facingMode)
        var results = document.getElementById('scanned-result');
        var lastMessage;
        var codesFound = 0;
        function onScanSuccess(qrCodeMessage) {
            if (lastMessage !== qrCodeMessage) {
                lastMessage = qrCodeMessage;
                ++codesFound;
                results.innerHTML += `<div>[${codesFound}] - ${qrCodeMessage}</div>`;


                var dataString = { send : true , credential : qrCodeMessage };

                $.ajax({

                    type: "POST",
                    url: "authenticate.php",
                    data: dataString,
                    dataType: "json",
                    cache : false,
                    success: function(data){

                        if(data.success == true){
                            alert("You have successfully logged in!");
                            self.location.replace('home.php');
                        } else {
                            alert("The credentials not match!");
                            self.location.replace('index.php');
                        }


                    } ,error: function(xhr, status, error) {
                        alert(error);
                    },
                });


            }
        }
        return html5qrcode.start({ facingMode: facingMode },{ fps: 10, qrbox: 250 },onScanSuccess);
    }
    function stopScanning() {
        return html5qrcode.stop();
    }
    docReady(function() {
        var button = document.getElementById('start');
        var facingModeSelect = 'user';
        if (!scanning) {
            startScanning(facingModeSelect)
                .then(_ => {
                    scanning = true;
                    button.innerHTML = "Stop Scanning";

                })
                .catch(err => {
                    alert(err);
                })
        } else {
            stopScanning()
                .then(_ => {
                    scanning = false;
                    button.innerHTML = "Start Scanning";
                })
                .catch(err => {
                    alert(err);
                })
        }
    });
</script>