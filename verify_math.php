<?php
require 'vendor/autoload.php';
$e = new \Mammesat\FilamentEthiopicDatePicker\Services\EthiopicCalendar();
// Gregorian today
$now = new \DateTime('2024-04-10');
$ethiopic = $e->toEthiopic((int)$now->format('Y'), (int)$now->format('m'), (int)$now->format('d'));
echo "PHP Ethiopic for 2024-04-10: Y=" . $ethiopic['year'] . " M=" . $ethiopic['month'] . " D=" . $ethiopic['day'] . "\n";

$now2 = new \DateTime();
$ethiopic2 = $e->toEthiopic((int)$now2->format('Y'), (int)$now2->format('m'), (int)$now2->format('d'));
echo "PHP Ethiopic for TODAY: Y=" . $ethiopic2['year'] . " M=" . $ethiopic2['month'] . " D=" . $ethiopic2['day'] . "\n";
