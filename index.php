<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MapQuest Distance Calculator <i class="fas fa-exchange-alt"></i></title>
    <link rel='stylesheet' id='fa-css' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' type='text/css' media='all' />
    <link href="css/styles.css" rel="stylesheet">
    
</head>
<body>

<div class="itinerary">
    <div class="itinerary__col">
        <div class="itinerary__marker">
            <i class="itinerary__icon fas fa-map-marker-alt"></i>
            <p class="itinerary__text">Bus Depot<br>Depart</p>
        </div>
    </div>
    <div class="itinerary__col">
        <div class="itinerary__marker">
            <i class="itinerary__icon fas fa-map-marker-alt"></i>
            <p class="itinerary__text">Pickup<br>Point</p>
        </div>
    </div>
    <div class="itinerary__col">
        <div class="itinerary__marker">
            <i class="itinerary__icon fas fa-map-marker-alt"></i>
            <p class="itinerary__text">Dropoff<br>Point</p>
        </div>
    </div>
    <div class="itinerary__col">
        <div class="itinerary__marker">
            <i class="itinerary__icon fas fa-map-marker-alt"></i>
            <p class="itinerary__text">Bus Depot<br>Return</p>
        </div>
    </div>
</div>

<form class="form" method="post" action="">
    <h1 class="form__heading">Get Instant Quote &amp; Book Now</h1>
    <p class="form__intro">This demo uses the Map Quest API. Additionally is uses postcodes to calculate the distance between start and end journey points  and It calculates the distance by road. This updated version also calculates the journey time and cost for leaving the depot in Luton and reaching the start point and also leaving the end point and returning to the depot. The additional fees are included in the form results marked in red and are automatically added to the customers price marked in green. For sake of simplicity this example only includes one way journeys and does not include returns.</p>
    <p class="form__intro">You can use any UK based postcode.</p>
    <p class="form__intro">Example postcodes to use for the demo:  <span class="form__postcode">MK2 3DG (Bletchley)</span>  <span class="form__postcode">E1 8RU (Central London)</span>  <span class="form__postcode">MK44 3WJ (Bedford)</span></p>

    <div class="form__column">

    <span class="form__exchange-icon"><i class="fas fa-exchange-alt"></i></span>

    <div class="form__col">
        <label class="form__label" for="startPoint"><i class="fas fa-map-marker-alt"></i> Travelling From <span class="form__validation">*</span></label>
        <input class="form__input" type="text" name="pointTwo" placeholder="Add Postcode Here" required>
    </div>
    
    <div class="form__col">
        <label class="form__label" for="endPoint"><i class="fas fa-map-marker-alt"></i> Travelling To <span class="form__validation">*</span></label>
        <input class="form__input" type="text" name="pointThree" placeholder="Add Postcode Here" required>
    </div>


    </div>


    <label class="form__label"><i class="fas fa-shuttle-van"></i> Vehicle Type <span class="form__validation">*</span></label>
    <select class="form__select" name="vehicle_rate">
        <option value="null">-- SELECT --</option>
        <option value="1.5">Vehicle A</option>
        <option value="2.5">Vehicle B</option>
    </select>
    <br>
    <button class="form__button" type="submit">Get Prices</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = '62p90WGqktOzplmVtY1MzeYQRNrGmcWm'; 

    $pointTwo = $_POST['pointTwo'];
    $pointThree = $_POST['pointThree'];

    $pointTwo = str_replace(' ', '', $pointTwo);
    $pointThree = str_replace(' ', '', $pointThree);

    $startPoint = "LU29HD";

    $vehicle_rate = $_POST['vehicle_rate'];

    // To make things simple, the calculations DO NOT include a return journey

    // Therefore we need to calculate 3 travel point as follows:
    // 1. startPoint to pointTwo
    // 2. pointTwo to pointThree
    // 3. pointThree to startPoint

    $apiUrl1 = "https://www.mapquestapi.com/directions/v2/route?key=$apiKey&from=$startPoint&to=$pointTwo&unit=m"; // Drive from depot to customers start journey
    $apiUrl2 = "https://www.mapquestapi.com/directions/v2/route?key=$apiKey&from=$pointTwo&to=$pointThree&unit=m"; // Customers journey start
    $apiUrl3 = "https://www.mapquestapi.com/directions/v2/route?key=$apiKey&from=$pointThree&to=$startPoint&unit=m"; // Customers journey end

    $response1 = file_get_contents($apiUrl1);
    $response2 = file_get_contents($apiUrl2);
    $response3 = file_get_contents($apiUrl3);

    $data1 = json_decode($response1, true);
    $data2 = json_decode($response2, true);
    $data3 = json_decode($response3, true);

    if ($_POST['vehicle_rate']!="null" && $data1['route'] && $data2['route'] && $data3['route']) {

        // Grab the distance from each journey point
        $distance1 = $data1['route']['distance'];
        $distance2 = $data2['route']['distance'];
        $distance3 = $data3['route']['distance'];

        // Grab the formatted time for each jorney point
        $formattedTime1 = $data1['route']['formattedTime'];
        $formattedTime2 = $data2['route']['formattedTime'];
        $formattedTime3 = $data3['route']['formattedTime'];


        // Convert formatted time to minutes for each journey point
        list($hours, $minutes) = explode(':', $formattedTime1);
        $totalMinutes1 = ($hours * 60) + $minutes;

        list($hours, $minutes) = explode(':', $formattedTime2);
        $totalMinutes2 = ($hours * 60) + $minutes;

        list($hours, $minutes) = explode(':', $formattedTime3);
        $totalMinutes3 = ($hours * 60) + $minutes;


        // Calculate all the vaues based on vehicle type
        if($vehicle_rate == "1.5"){
            $minutes_rate = 40 / 60;
            $hourly_rate_1 = $minutes_rate * $totalMinutes1;
            $hourly_rate_2 = $minutes_rate * $totalMinutes2;
            $hourly_rate_3 = $minutes_rate * $totalMinutes3;
            $rate1 = $distance1 * $vehicle_rate + $hourly_rate_1;
            $rate2 = $distance2 * $vehicle_rate + $hourly_rate_2;
            $rate3 = $distance3 * $vehicle_rate + $hourly_rate_3;
            $depotRate1 = $distance1 * $vehicle_rate;
            $depotRate2 = $distance2 * $vehicle_rate;
            $depotRate3 = $distance3 * $vehicle_rate;
            $total_additional_fee = $depotRate1 + $hourly_rate_1 + $depotRate3 + $hourly_rate_3;
            $total = $rate1 + $rate2 + $rate3;
        } else {
            $minutes_rate = 55 / 60;
            $hourly_rate_1 = $minutes_rate * $totalMinutes1;
            $hourly_rate_2 = $minutes_rate * $totalMinutes2;
            $hourly_rate_3 = $minutes_rate * $totalMinutes3;
            $rate1 = $distance1 * $vehicle_rate + $hourly_rate_1;
            $rate2 = $distance2 * $vehicle_rate + $hourly_rate_2;
            $rate3 = $distance3 * $vehicle_rate + $hourly_rate_3;
            $depotRate1 = $distance1 * $vehicle_rate;
            $depotRate2 = $distance2 * $vehicle_rate;
            $depotRate3 = $distance3 * $vehicle_rate;
            $total_additional_fee = $depotRate1 + $hourly_rate_1 + $depotRate3 + $hourly_rate_3;
            $total = $rate1 + $rate2 + $rate3;
        }   

        //vehicle A mileage rate = 1.50
        //vehicle A hourly rate = 40
        //vehicle B mileage rate = 2.50
        //vehicle B hourly rate = 55

        if($vehicle_rate == "1.5"){ 
            $vehicle_type = "Small Coach 0-35 passengers"; 
        } else { 
            $vehicle_type = "Big Coach 36-53 passengers"; 
        }

        // Display the result on the page
        echo "<div class='result'>";
        echo "<h3 class='results__title'>Thankyou for your submission</h3>";
        echo "<p>You are travelling from: $pointTwo</p>";
        echo "<p>You are travelling to: $pointThree</p>";
        echo "<p>Distance: $distance2 miles</p>";
        echo "<p>Estimated Travel Time: $formattedTime2</p>";
        echo "<p>Vehicle: $vehicle_type</p>";
        echo "<p>Mileage Rate: £$rate2</p>";
        echo "<p>Hourly Rate: £$hourly_rate_2</p>";
        echo "<p class='results__total'>Total Price: £$total</p>";
        echo "</div>";
        echo "<div class='result__alt'>";
        echo "<h3 class='results__title'>Extra Information For Additional Fee (Depot to travel point and visa versa)</h3>";
        echo "<p>Distance from depot ($startPoint) to pickup point ($pointTwo): $distance1 miles <strong>(£$depotRate1)</strong></p>";
        echo "<p>Travel time from depot ($startPoint) to pickup point ($pointTwo): $totalMinutes1 minutes <strong>(£$hourly_rate_1)</strong></p>";
        echo "<p>Distance from end point ($pointThree) to depot ($startPoint): $distance3 miles <strong>(£$depotRate3)</strong></p>";
        echo "<p>Travel time from end point ($pointThree) to depot ($startPoint): $totalMinutes3 minutes <strong>(£$hourly_rate_3)</strong></p>";
        echo "<p><strong>Total additional fee: £$total_additional_fee</strong></p>";
        echo "</div>";
    } else {
        echo "<div class='result'>";
        echo "<p>Unable to retrieve distance. Please check your input and try again.</p>";
        echo "</div>";
    }
}
?>

</body>
</html>
