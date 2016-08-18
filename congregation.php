<?php
  header("Content-Type: text/html; charset=utf-8");
  $time = array(array("number" => "один", "clock" => "час"),
    array("number" => "два", "clock" => "часа"),
    array("number" => "три", "clock" => "часа"),
    array("number" => "четыре", "clock" => "часа"),
    array("number" => "пять", "clock" => "часов"),
    array("number" => "шесть", "clock" => "часов"),
    array("number" => "семь", "clock" => "часов"),
    array("number" => "восемь", "clock" => "часов"),
    array("number" => "девять", "clock" => "часов"),
    array("number" => "десять", "clock" => "часов"),
    array("number" => "одиннадцать", "clock" => "часов"),
    array("number" => "двенадцать", "clock" => "часов"));
  $area = array("камсе", "юбике", "фестике", "пашковке", "дубинке", "красной",
    "славянском", "черёмушках", "кск", "тец", "солнечном острове", "табрисе"
    , "парке горького", "первомайской роще", "галерее", "сбс", "озе", "хате у меня");
  $weekday = array("понедельник", "вторник", "среду", "четверг", "пятницу", "субботу",
    "воскресенье");
  $timeRand = mt_rand( 0, count($time) - 1);
  $areaRand = mt_rand( 0, count($area) - 1);
  $weekdayRand = mt_rand( 0, count($weekday) - 1);
  $minutesRand = mt_rand(0, 5) . "0";
  $hourRand = mt_rand(8, 23);

  if ($hourRand < 10) {
    $hourRand = "0" . $hourRand;
  }

  $congregationClock = "Сходка в " . $weekday[$weekdayRand]  . " в " . $hourRand  .
  ":" . $minutesRand . " на " . $area[$areaRand] . "!";
/*
  $congregation = "Сходка через " . $time[$timeRand]["number"]  .
  " " . $time[$timeRand]["clock"] . " на " . $area[$areaRand] . "!";
  echo $congregation;
  echo "<br>";
  echo $congregationClock;
*/
?>
