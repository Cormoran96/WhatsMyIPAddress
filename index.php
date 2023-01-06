<?php

// Get client's IP address
$client_ipv4 = $_SERVER['REMOTE_ADDR'];

// Get client's forwarded-for IP address (if set)
$client_ipv6 = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;

// If client is not connected via IPv6, set $client_ipv6 to null
if (filter_var($client_ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    $client_ipv6 = null;
}

// Get client's hostname
$hostname = gethostbyaddr($client_ipv4);

// Get data for client's IP address
$ip_data = file_get_contents('http://ip-api.com/json/' . $client_ipv4);
$ip_data = json_decode($ip_data);
$ipv4 = $ip_data->{'query'};
$ipv6 = $ip_data->{'ip'};
$city = $ip_data->{'city'};
$region = $ip_data->{'region'};
$country = $ip_data->{'country'};
$isp = $ip_data->{'isp'};
$as = $ip_data->{'as'};
$lat = $ip_data->{'lat'};
$lon = $ip_data->{'lon'};
$asn = explode(' ', $as, 2);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My IP Address</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>

<body>
    <header>
        <h1>Ip.dousse.eu</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="traceroute.php">Traceroute</a>
            <a href="atlas/">Probe</a>
	</nav>
	<div class="menu-button"></div>
    </header>


    <div class="container">
        <h1>My Public IP Address</h1>
        <p><b>IPv4</b>: <?php echo $client_ipv4; ?></p>
        <?php if ($client_ipv6): ?>
            <p><b>IPv6</b>: <?php echo $client_ipv6; ?></p>
        <?php endif; ?>
        <p><b>Hostname</b>: <?php echo $hostname; ?></p>
        <p><b>City</b>: <?php echo $city; ?></p>
        <p><b>Region</b>: <?php echo $region; ?></p>
        <p><b>Country</b>: <?php echo $country; ?></p>
        <p><b>Service Provider (ISP)</b>: <?php echo $isp; ?></p>
        <p><b>AS Number</b>: <a href="https://bgp.tools/<?php echo $asn[0]; ?>" target="_blank">
                <?php echo $as; ?>
            </a></p>

	<div id="mapid"></div>


        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            var mymap = L.map('mapid').setView([<?php echo $lat; ?>, <?php echo $lon; ?>], 13);

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: 'pk.eyJ1IjoiY29ybW9yYW45NiIsImEiOiJjbGNremhvNnkwaGhlM3ByeTl6ZGc0MDluIn0.TkLqe8vC5h58Iq3Rs170OA'
            }).addTo(mymap);

            L.marker([<?php echo $lat; ?>, <?php echo $lon; ?>]).addTo(mymap)
        </script>
    </div>
    <footer>
     <p>Copyright 2023 | <a href="https://dousse.eu">Lucas</a></p>
    </footer>
    <script>
        // Toggle the "burger menu" open and closed
        document.querySelector(".menu-button").addEventListener("click", function () {
            this.classList.toggle("open");
            document.querySelector("nav").classList.toggle("open");
        });
    </script>
</body>

</html>
