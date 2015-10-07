<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>What's My IP Address?</title>
	<link href="main.css" type="text/css" rel="stylesheet">
</head>
<body>
<?php
$ipaddress = $_SERVER['REMOTE_ADDR']; //ip address


$host = gethostbyaddr($ipaddress); 

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

//echo "$ipaddress;$realip;$host;";


$locationstr="http://ip-api.com/xml/";
$locationstr = $locationstr.$ipaddress;
//loading the xml file directly from the website
$xml = simplexml_load_file($locationstr); 
$countrycode = $xml->countryCode; //country code
$countryname = $xml->country; //country name
$region = $xml->regionName; //region name
$city = $xml->city; //city name
$lattitude = $xml->lat; //city latitude
$longitude = $xml->lon; //city longitude
$isp = $xml->isp; //city latitude
$noeud = $xml->as; //city longitude
//$browsername = $xml->browserName; //browser name
?>
<div class="main" style="width:950px;">
	<h2 class="post-title">Voici votre adresse IP locale : <span class="adress"><?php //echo $ipaddress; ?></span></h2>
	<h2 class="post-title">Voici votre adresse IP publique : <?php //echo $ipaddress; ?></h2>
	<h2 class="post-title">Voici votre nom d'hôte : <?php echo $host; ?></h2>
	Cette adresse a été localisée dans la ville de <b><?php echo $city ?></b> (Région : <?php echo $region ?> / Pays : <?php echo $countryname ?>)
	<br/>(Localisation effectuée en fonction de votre FAI (<b><?php echo $isp; ?></b>) et du noeud réseau auquel vous êtes rattaché (<b><?php echo $noeud; ?></b>))
	<p>
		<center>
		<iframe width="900" height="550"frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=AIzaSyCRTD-ARSWquI_i1P0AlZrRPxnzmtIntd4&q=<?php echo $lattitude?>,<?php echo $longitude?>&zoom=12" allowfullscreen>
</iframe></center>
</p>
	
  <iframe id="iframe" sandbox="allow-same-origin" style="display: none"></iframe>
        <script>
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
                    //NOTE: you need to have an iframe in the page right above the script tag
                    //
                    //<iframe id="iframe" sandbox="allow-same-origin" style="display: none"></iframe>
                    //<script>...getIPs called in here...
                    //
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
                var b = document.createElement("b");
                b.textContent = ip + ' ';
                if (ip.match(/^(192\.168\.|169\.254\.|10\.|172\.(1[6-9]|2\d|3[01]))/))
                	//document.write(ip);
                    document.getElementsByTagName("h2")[0].appendChild(b);
                else if (ip.match(/^[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7}$/))
               		//document.write(ip);
                   document.getElementsByTagName("h2")[2].appendChild(b);
                else
                //	document.write(ip);
                    document.getElementsByTagName("h2")[1].appendChild(b);
            }
        );
                   </script>
</div>
</body>
</html>
