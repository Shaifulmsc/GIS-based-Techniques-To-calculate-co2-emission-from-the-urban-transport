<?php
require_once 'config.php';
?>
<html>

<head>
    <title> Calculating Carbon Dioxide Emission</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script defer
            src="https://maps.googleapis.com/maps/api/js?libraries=places&language=<?= $_SESSION['lang'] ?>&key=AIzaSyAMeeBbi4bBPZgB_9A065SHsQ4Got8qbOU"
            type="text/javascript"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    

    <style>


</style>
</head>
<?php include ('admin/footer.php'); ?>
<body>
<br>
<br>
        <!-- Language -->
        <script>
            function changeLang() {
                document.getElementById('form_lang').submit();
            }
        </script>
        <form method='get' action='' id='form_lang'>
            <div class="form-group">
            Select Language : <select class="form-control" name='lang' onchange='changeLang();'>
                <option value='en' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'en') {
                    echo "selected";
                } ?> >English
                </option>
                <option value='fr' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'fr') {
                    echo "selected";
                } ?> >French
                </option>
                <option value='de' <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == 'de') {
                    echo "selected";
                } ?> >Germany
                </option>
            </select>
            </div>
        </form>

    
        <div class="container">
        <div class="row">
        <div class="col-sm-4">
            <!-- form -->
            <form id="distance_form">
                <div class="form-group"><label><?= $lang['username'] ?>: </label>
                    <input class="form-control" id="username" placeholder="<?= $lang['enter_username'] ?>"/>
                    <input  name="username" required="" type="hidden"/></div>

                <div class="form-group"><label><?= $lang['origin'] ?>: </label>
                    <input class="form-control" id="from_places" placeholder="<?= $lang['enter_location'] ?>"/>
                    <input id="origin" name="origin" required="" type="hidden"/>
                    <a class="form-control" onclick="getLocation()">Set Current Location</a>
                </div>

                <div class="form-group"><label><?= $lang['destination'] ?>: </label>
                    <input class="form-control" id="to_places" placeholder="<?= $lang['enter_location'] ?>"/>
                    <input id="destination" name="destination" required="" type="hidden"/></div>

                <div class="form-group">
                    <label><?= $lang['travel_mode'] ?></label>
                    <select class="form-control" id="travel_mode" name="travel_mode">
                        <option value="DRIVING"><?= $lang['Driving'] ?></option>
                        <option data-extra="0.17" value="DRIVING"><?= $lang['taxi'] ?></option>
                        <option data-extra="0.094" value="DRIVING"><?= $lang['moto'] ?></option>
                        <option data-extra="0" value="WALKING"><?= $lang['foot'] ?></option>
                        <option data-extra="0" value="BICYCLING"><?= $lang['bic'] ?></option>
                        <option data-extra="0.069" value="TRANSIT"><?= $lang['bus'] ?></option>
                        <option data-extra="0.042" value="TRANSIT"><?= $lang['train'] ?></option>
                    </select>
                </div>

                <div id="car_type_block" class="form-group " >
                    <label><?= $lang['car_type'] ?></label>
                    <select class="form-control" id="car_type" name="car_type">
                        <?php
                        $result = get_cars();
                        if(!empty($result)){
                        $cars = resultToArray($result);
                        foreach ($cars as $key => $car) { ?>
                            <option data-extra="<?= $car['average_co2_km'] ?>" value="<?= $car['id'] ?>"><?= $car['manufacturer'] .'-'.$car['registration'] .'-'."(" . $car['release_year'] .")"?></option>
                        <?php } } ?>
                    </select>
                    </div>

                <input class="btn btn-primary" type="submit" value="<?= $lang['calculate_btn'] ?>"/>

            </form>
        </div>

        <div class="col-sm-4">
         <div id="map" style="height: 400px; width: 450px" ></div>
        </div>
            <!-- result -->
        <div class="col-sm-4">
            <div style="margin-left: 123px;" id="result" class="hide">
                <ul class="list-group">
                    <li id="in_mile" class="list-group-item d-flex justify-content-between align-items-center"></li> <br>
                    <br>
                    <br>
                    <li id="in_kilo" class="list-group-item d-flex justify-content-between align-items-center"></li> <br>
                    <br>
                    <br>

                    <li id="duration_text" class="list-group-item d-flex justify-content-between align-items-center"></li> <br>
                    <br>
                    <br>

                    <li id="duration_value"  class="list-group-item d-flex justify-content-between align-items-center"></li> <br>
                    <br>
                    <br>
                    <li id="co2_result" style="overflow: auto; padding-right: 90px"  class="list-group-item d-flex justify-content-between align-items-center"></li>
                </ul>
                <br><br>
            </div>
        </div>
           
        </div>
        
    </div>


</div>
<script>
    $(function () {
        var origin, destination, map;

        // add input listeners
        google.maps.event.addDomListener(window, 'load', function (listener) {
            detect_set_destination();
            initMap();
        });

        // init or load map
        function initMap() {

            var myLatLng = {
                lat: 52.520008,
                lng: 13.404954
            };
            map = new google.maps.Map(document.getElementById('map'), {zoom: 16, center: myLatLng,});
        }

        function detect_set_destination() {
            var from_places = new google.maps.places.Autocomplete(document.getElementById('from_places'));
            var to_places = new google.maps.places.Autocomplete(document.getElementById('to_places'));

            google.maps.event.addListener(from_places, 'place_changed', function () {
                var from_place = from_places.getPlace();
                var from_address = from_place.formatted_address;
                $('#origin').val(from_address);
            });

            google.maps.event.addListener(to_places, 'place_changed', function () {
                var to_place = to_places.getPlace();
                var to_address = to_place.formatted_address;
                $('#destination').val(to_address);
            });


        }

        function displayRoute(travel_mode, origin, destination, directionsService, directionsDisplay) {
            directionsService.route({
                origin: origin,
                destination: destination,
                travelMode: travel_mode,
                avoidTolls: true
            }, function (response, status) {
                if (status === 'OK') {
                    directionsDisplay.setMap(map);
                    directionsDisplay.setDirections(response);
                } else {
                    directionsDisplay.setMap(null);
                    directionsDisplay.setDirections(null);
                    alert('Could not display directions due to: ' + status);
                }
            });
        }

        // calculate distance , after finish send result to callback function
        function calculateDistance(travel_mode, origin, destination) {

            var DistanceMatrixService = new google.maps.DistanceMatrixService();
            DistanceMatrixService.getDistanceMatrix(
                {
                    origins: [origin],
                    destinations: [destination],
                    travelMode: google.maps.TravelMode[travel_mode],
                    unitSystem: google.maps.UnitSystem.IMPERIAL, // miles and feet.
                    // unitSystem: google.maps.UnitSystem.metric, // kilometers and meters.
                    avoidHighways: false,
                    avoidTolls: false
                }, save_results);
        }

        // save distance results
        function save_results(response, status) {

            if (status != google.maps.DistanceMatrixStatus.OK) {
                $('#result').html(err);
            } else {
                var origin = response.originAddresses[0];
                var destination = response.destinationAddresses[0];
                if (response.rows[0].elements[0].status === "ZERO_RESULTS") {
                    $('#result').html("Sorry , not available to use this travel mode between " + origin + " and " + destination);
                } else {
                    var distance = response.rows[0].elements[0].distance;
                    var duration = response.rows[0].elements[0].duration;
                    var distance_in_kilo = distance.value / 1000; // the kilo meter
                    var distance_in_mile = distance.value / 1609.34; // the mile
                    var duration_text = duration.text;
                    var duration_value = duration.value;
                    var travel_mode = $('#travel_mode').val();
                    var co2_avg_km = ( travel_mode === "DRIVING" ) ?
                        $("#car_type").find(':selected').data('extra')
                        : $("#travel_mode").find(':selected').data('extra');
                    var co2_result = Math.round( co2_avg_km * distance_in_kilo );
                    append_results(co2_result,distance_in_kilo, distance_in_mile, duration_text, duration_value);
                    send_ajax_request(co2_result,origin, destination, distance_in_kilo, distance_in_mile, duration_text, duration_value);
                }
            }
        }

        // append html results
        function append_results(co2_result,distance_in_kilo, distance_in_mile, duration_text, duration_value) {
            $("#result").removeClass("hide");
            $('#in_mile').html("<?= $lang['distance_in_mile'] ?> : <span class='badge badge-pill badge-secondary'>" + distance_in_mile.toFixed(2) + "</span>");
            $('#in_kilo').html("<?= $lang['distance_in_kilo'] ?>: <span class='badge badge-pill badge-secondary'>" + distance_in_kilo.toFixed(2) + "</span>");
            $('#duration_text').html("<?= $lang['in_text'] ?>: <span class='badge badge-pill badge-success'>" + duration_text + "</span>");
            $('#duration_value').html("<?= $lang['in_minutes'] ?>: <span class='badge badge-pill badge-success'>" + duration_value + "</span>>");
            $('#co2_result').html("<?= $lang['co2_result'] ?>: <span class='badge badge-pill badge-success'>" + co2_result +  " &nbsp <?= $lang['gram'] ?>" + "</span>");
        }

        // send ajax request to save results in the database
        function send_ajax_request(co2_result,origin, destination, distance_in_kilo, distance_in_mile, duration_text, duration_value) {
            var username =   $('#username').val();
            var travel_mode =  $('#travel_mode').find(':selected').text();
            var car_type =  $('#car_type').find(':selected').text();
            $.ajax({
                url: 'common.php',
                type: 'POST',
                data: {
                    username,
                    car_type,
                    travel_mode,
                    origin,
                    destination,
                    co2_result,
                    distance_in_kilo,
                    distance_in_mile,
                    duration_text,
                    duration_value
                },
                success: function (response) {
                    console.info(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
            //@todo insure get ajax request to get cars list
            //@todo add the calculation for c2o
        }

        // on submit  display route ,append results and send calculateDistance to ajax request
        $('#distance_form').submit(function (e) {
            e.preventDefault();
            var origin = $('#origin').val();
            var destination = $('#destination').val();
            var travel_mode = $('#travel_mode').val();
            var directionsDisplay = new google.maps.DirectionsRenderer({'draggable': false});
            var directionsService = new google.maps.DirectionsService();
           displayRoute(travel_mode, origin, destination, directionsService, directionsDisplay);
            calculateDistance(travel_mode, origin, destination);
        });


        // on change travel mode hide driving select
        $('#travel_mode').change(function (e) {
            if($('#travel_mode option:selected').text() === 'Driving'){
                $("#car_type_block").removeClass("hide");
            }else{
                $("#car_type_block").addClass("hide");
            }
        });

        $("#car_type").select2();

    });

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation is not supported by this browser.")
        }
    }

    function showPosition(position) {
        console.log(position.coords.latitude);
        console.log(position.coords.longitude);
        geocodePosition(position);
    }

    function geocodePosition(pos) {
        var geocoder = new google.maps.Geocoder();
        var latlng = {lat: parseFloat(pos.coords.latitude),
            lng: parseFloat(pos.coords.longitude)};

        geocoder.geocode({ 'location' :latlng  }, function (responses) {
            console.log(responses);
            if (responses && responses.length > 0) {
                $("#origin").val(responses[1].formatted_address);
                $("#from_places").val(responses[1].formatted_address);
            //    console.log(responses[1].formatted_address);
            } else {
                alert("Cannot determine address at this location.")
            }
        });
    }

</script>

  

</body>

</html>
