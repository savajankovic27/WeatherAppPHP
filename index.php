<?php
if (isset($_GET['city'])) {
    $city = urlencode($_GET['city']);
    $apiKey = '96de3978e10e6e2be031390d0bb9cf51'; // got from OpenWeatherAPI
    $unit = isset($_GET['unit']) ? $_GET['unit'] : 'metric'; // Default: Celsius
    $unitSymbol = ($unit === 'metric') ? "°C" : "°F";

    $apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$city}&units={$unit}&appid={$apiKey}";
    $forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?q={$city}&units={$unit}&appid={$apiKey}";

    // Fetch Current Weather
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    $response = curl_exec($ch);
    curl_close($ch);
    $weatherData = json_decode($response, true);

    // Fetch the five-day forecast. 
    // I tried getting this to spread across horizontally, not vertically. That is more to do with the frontend and I know it's an easy fix, will patch later. 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $forecastUrl);
    $forecastResponse = curl_exec($ch);
    curl_close($ch);
    $forecastData = json_decode($forecastResponse, true);

    if ($weatherData['cod'] != 200) {
        $error = "City not found, try again";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">The Super Accurate Weather Dashboard</h1>
        
        <form method="GET" action="index.php" class="mt-4">
            <div class="form-group">
                <label for="city">Enter City Name:</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="unit">Select Unit:</label>
                <select class="form-control" id="unit" name="unit">
                    <option value="metric" <?= ($unit == 'metric') ? 'selected' : '' ?>>Celsius (°C)</option>
                    <option value="imperial" <?= ($unit == 'imperial') ? 'selected' : '' ?>>Fahrenheit (°F)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Get Weather</button>
        </form>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($weatherData) && !isset($error)): ?>
            <div class="weather-card mt-4 text-center">
                <h2><?php echo htmlspecialchars($weatherData['name']); ?></h2>
                <img src="https://openweathermap.org/img/wn/<?php echo $weatherData['weather'][0]['icon']; ?>@2x.png" alt="Weather Icon">
                <p><?php echo htmlspecialchars($weatherData['weather'][0]['description']); ?></p>
                <p><strong>Temperature:</strong> <?php echo htmlspecialchars($weatherData['main']['temp']); ?><?php echo $unitSymbol; ?></p>
                <p><strong>Feels Like:</strong> <?php echo htmlspecialchars($weatherData['main']['feels_like']); ?><?php echo $unitSymbol; ?></p>
                <p><strong>Humidity:</strong> <?php echo htmlspecialchars($weatherData['main']['humidity']); ?>%</p>
                <p><strong>Wind Speed:</strong> <?php echo htmlspecialchars($weatherData['wind']['speed']); ?> m/s</p>
                <p><strong>Sunrise:</strong> <?php echo date('h:i A', $weatherData['sys']['sunrise']); ?></p>
                <p><strong>Sunset:</strong> <?php echo date('h:i A', $weatherData['sys']['sunset']); ?></p>
            </div>

            <h3 class="mt-5">5-Day Forecast</h3>
            <div class="forecast-container">
                <?php foreach ($forecastData['list'] as $forecast): ?>
                    <div class="forecast-card">
                        <p><?php echo date("D, M j", $forecast['dt']); ?></p>
                        <img src="https://openweathermap.org/img/wn/<?php echo $forecast['weather'][0]['icon']; ?>.png" alt="Weather Icon">
                        <p><?php echo htmlspecialchars($forecast['main']['temp']); ?><?php echo $unitSymbol; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
