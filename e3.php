<?php
session_start();
if(!$_SESSION['history']) {
  $_SESSION['history'] = [];
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
$endpoint = 'symbols';
$access_key = '48b2f90807e62dfe368616343474e839';
$ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($ch);
curl_close($ch);
$currencies = json_decode($json, true);

 ?>
<h1>When's your birthday?</h1>
<form method="GET">
    <input id="date" type="date" name="birthday">
    <select name="currency">
      <?php foreach($currencies['symbols'] as $key => $currency) : ?>
        <option value="<?= $key; ?>"><?= $currency; ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Check</button>
</form>
<?php

if($_GET['birthday']) {
  $current_date = explode('/', date("Y/m/d"));
  $birthday = explode('-', $_GET['birthday']);
  $last_year = (int) $current_date[0] - 1;
  $next_year = (int) $current_date[0] + 1;
  if($_GET['birthday'] < $last_year."-".$current_date[1]."-".$current_date[2]) {
    echo("<p>Please select a valid date</p>");
  } elseif($_GET[birthday] > date("Y/m/d")) {
    echo("<p>Please select a valid date</p>");
  } else {
    $currency = $_GET['currency'];
    $this_year = $current_date[0];
    var_dump($_GET['birthday']);
    if ((int) $current_date[1] < (int) $birthday[1]) {
      $last_birthday = $last_year."-".$birthday[1]."-".$birthday[2];
    } elseif ($current_date[1] == $birthday[1] && $current_date[2] < $birthday[2]) {
      $last_birthday = $last_year."-".$birthday[1]."-".$birthday[2];
    } elseif ($current_date[1] == $birthday[1] && $current_date[2] == $birthday[2]) {
      $last_birthday = $current_date[0]."-".$current_date[1]."-".$current_date[2];
      echo("<h1>Happy birthday!</h1>");
    } else {
      $last_birthday = $current_date[0]."-".$birthday[1]."-".$birthday[2];
    }
    $access_key = '48b2f90807e62dfe368616343474e839';
    $ch = curl_init('http://data.fixer.io/api/'.$last_birthday.'?access_key='.$access_key.'');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($ch);
    curl_close($ch);
    $exchangeRates = json_decode($json, true);
    $number_of_searches = 1;
    foreach($_SESSION['history'] as $history) {
      if($history['birthday'] == $_GET['birthday']) {
        $number_of_searches++;
      }
    }

    $_SESSION['history'][] = [
      'birthday' => $birthday[0]."-".$birthday[1]."-".$birthday[2],
      'last_birthday' => $last_birthday,
      'currency' => $currency,
      'exchange_rate' => $exchangeRates['rates'][$currency],
      'time' => date('H:i'),
      'times_searched' => $number_of_searches
    ];

    echo ("On your previous birthday ".$last_birthday." exchange rate of ".$currencies['symbols'][$currency]." was ".$exchangeRates['rates'][$currency]);
  }
}

?>
<h2>Previous results</h2>
<?php $history_array = $_SESSION['history']; ?>
<?php arsort($history_array); ?>
<?php foreach($history_array as $history) : ?>
  <?php if($history['times_searched'] < 2) : ?>
    <p>At <?= $history['time']; ?> you have checked <?= $currencies['symbols'][$history['currency']]; ?> course on <?= $history['birthday']; ?>. It was <?= $history['exchange_rate']; ?></p>
  <?php else : ?>
    <p>You already searched for the date <?= $history['birthday']; ?> <?= $history['times_searched']; ?> times.
  <?php endif; ?>
<?php endforeach; ?>
