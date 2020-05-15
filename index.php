<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="IPv4 Address, IPv6 Address, IP Address Lookup" />
    <meta name="author" content="">

    <title>What's My IP ? - dousse.eu </title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.4/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>

    <!-- Custom styles for this template -->
    <link href="cover.css" rel="stylesheet">
    <!-- Perso CSS -->
    <link href="dist/css/style.css" rel="stylesheet">
    <!-- Leaflet JS  -->
    <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js" integrity="sha512-nMMmRyTVoLYqjP9hrbed9S+FzjZHW5gY1TWCHA5ckwXZBadntCNs8kEqAWdrb9O7rxbCaA4lKTIWjDXZxflOcA==" crossorigin=""></script>
</head>

<body class="text-center">

    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
      <header class="masthead mb-auto">
        <div class="inner">
          <h3 class="masthead-brand">Ip.dousse.eu</h3>
          <nav class="nav nav-masthead justify-content-center">
            <a class="nav-link active" href="#">Home</a>
            <!--<a class="nav-link" href="#">Features</a>-->
        </nav>
    </div>
</header>


<?php

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
      $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
      $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
  }
  $client  = @$_SERVER['HTTP_CLIENT_IP'];
  $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
  $remote  = $_SERVER['REMOTE_ADDR'];

  if(filter_var($client, FILTER_VALIDATE_IP))
  {
    $ip = $client;
}
elseif(filter_var($forward, FILTER_VALIDATE_IP))
{
    $ip = $forward;
}
else
{
    $ip = $remote;
}

return $ip;
}


$user_ip = getUserIP();


$host = gethostbyaddr($user_ip);

    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $realip=$_SERVER['HTTP_CLIENT_IP'];
  }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else
  {
      $realip=$_SERVER['REMOTE_ADDR'];
  }


  $loc = file_get_contents('http://ip-api.com/json/'.$user_ip.'?fields=country,regionName,city,zip,lat,lon,timezone,isp,org,as,reverse,mobile,proxy,query,status,message');
  $obj = json_decode($loc);
  $as = explode(' ', $obj->{'as'}, 2);

  ?>


  <main role="main" class="inner cover">
    <p class="lead">Your public ip:</p>
    <h1 class="cover-heading"><?php echo $user_ip; ?></h1>
    <p class="lead">Your hostname:</p>
    <h1 class="cover-heading"><?php echo $host; ?></h1>
    <p class="lead">Your private ip:</p>
    <h1 class="cover-heading"><ul></ul></h1>
    <hr> 
    <div class="divTable" style="width: 100%;" >
        <div class="divTableBody">
            <div class="divTableRow">
                <div class="divTableCell text-right">City: </div>
                <div class="divTableCell text-left"><?php echo $obj->{'city'};?></div>
            </div>
            <div class="divTableRow">
                <div class="divTableCell text-right">Region: </div>
                <div class="divTableCell text-left"><?php echo $obj->{'regionName'};?></div>
            </div>
            <div class="divTableRow">
                <div class="divTableCell text-right">Country: </div>
                <div class="divTableCell text-left"><?php echo $obj->{'country'};?></div>
            </div>
            <div class="divTableRow">
                <div class="divTableCell text-right">Service Provider (ISP): </div>
                <div class="divTableCell text-left"><?php echo $obj->{'isp'};?></div>
            </div>
            <div class="divTableRow">
                <div class="divTableCell text-right">AS number: </div>
                <div class="divTableCell text-left"><a href="https://bgp.he.net/<?php echo $as[0];?>" target="_blank"><?php echo $obj->{'as'};?></a></div>
            </div>
        </div>
    </div>
    <hr>
    <div id="mapid" class="leaflet-container leaflet-touch leaflet-fade-anim leaflet-grab leaflet-touch-drag leaflet-touch-zoom"  ></div>
</main>
<iframe id="iframe" sandbox="allow-same-origin" style="display: none"></iframe>

<script>

    var mymap = L.map('mapid').setView([<?php echo $obj->{'lat'};?>, <?php echo $obj->{'lon'};?>], 13);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
        '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
        'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        id: 'mapbox.streets-satellite'
    }).addTo(mymap);

    L.marker([<?php echo $obj->{'lat'};?>, <?php echo $obj->{'lon'};?>]).addTo(mymap)

            //get the IP addresses associated with an account
            function getIPs(callback){
                var ip_dups = {};
                //compatibility for firefox and chrome
                var RTCPeerConnection = window.RTCPeerConnection
                || window.mozRTCPeerConnection
                || window.webkitRTCPeerConnection;
                var useWebKit = !!window.webkitRTCPeerConnection;
                //bypass naive webrtc blocking using an iframe
                if(!RTCPeerConnection){
                    var win = iframe.contentWindow;
                    RTCPeerConnection = win.RTCPeerConnection
                    || win.mozRTCPeerConnection
                    || win.webkitRTCPeerConnection;
                    useWebKit = !!win.webkitRTCPeerConnection;
                }
                //minimal requirements for data connection
                var mediaConstraints = {
                    optional: [{RtpDataChannels: true}]
                };
                var servers = {iceServers: [{urls: "stun:stun.services.mozilla.com"}]};
                //construct a new RTCPeerConnection
                var pc = new RTCPeerConnection(servers, mediaConstraints);
                function handleCandidate(candidate){
                    //match just the IP address
                    var ip_regex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/
                    var ip_addr = ip_regex.exec(candidate)[1];
                    //remove duplicates
                    if(ip_dups[ip_addr] === undefined)
                        callback(ip_addr);
                    ip_dups[ip_addr] = true;
                }
                //listen for candidate events
                pc.onicecandidate = function(ice){
                    //skip non-candidate events
                    if(ice.candidate)
                        handleCandidate(ice.candidate.candidate);
                };
                //create a bogus data channel
                pc.createDataChannel("");
                //create an offer sdp
                pc.createOffer(function(result){
                    //trigger the stun server request
                    pc.setLocalDescription(result, function(){}, function(){});
                }, function(){});
                //wait for a while to let everything done
                setTimeout(function(){
                    //read candidate info from local description
                    var lines = pc.localDescription.sdp.split('\n');
                    lines.forEach(function(line){
                        if(line.indexOf('a=candidate:') === 0)
                            handleCandidate(line);
                    });
                }, 1000);
            }
            //insert IP addresses into the page
            getIPs(function(ip){
                var li = document.createElement("li");
                li.textContent = ip;
                //local IPs
                if (ip.match(/^(192\.168\.|169\.254\.|10\.|172\.(1[6-9]|2\d|3[01]))/))
                    document.getElementsByTagName("ul")[0].appendChild(li);
                //IPv6 addresses
                else if (ip.match(/^[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7}$/))
                    document.getElementsByTagName("ul")[2].appendChild(li);
                //assume the rest are public IPs
                else
                    document.getElementsByTagName("ul")[1].appendChild(li);
            });
        </script>


        <footer class="mastfoot mt-auto">
            <div class="inner">
              <p>Have a good day, <a href=http://dousse.eu>Lucas</a>.</p>
          </div>
      </footer>
  </div>


    <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
        <script src="assets/js/vendor/popper.min.js"></script>
        <script src="dist/js/bootstrap.min.js"></script>
    </body>
    </html>
