<?php

setParameterSetId("8d6b35c3ccc34e29a27bc5d1ed799a3c");
include "classes.php";

$millimeter = 1;
$inch = 25.4 * $millimeter;
$mm = $millimeter;

$degree = 1;
$radian = 180/pi();

function average($arr)
{
   if (!is_array($arr)) return false;

   return array_sum($arr)/count($arr);
}


// the heaterBed is a right-rectangular flat surface that is the aluminum surface lying below and supporting the heating element.
$heaterBed->extent->x = 30 * $millimeter;
$heaterBed->extent->z = 80 * $millimeter;

$mainBar->extent->x = $heaterBed->extent->x;
$mainBar->extent->z = $heaterBed->extent->z + 100 * $millimeter;
$mainBar->extent->y = 70 * $millimeter;
$mainBar->foot->extent->y = 15 * $millimeter;
$mainBar->pivotInsetZ = 30 * $millimeter;
$mainBar->pivotInsetY = 7 * $millimeter;


$arm->sidePlate->extent->x = 3 * $millimeter; //stock sheet thickness

$arm->sidePlate->extent->z = 10 * $millimeter;
$arm->sidePlate->pivotDedendum = 4 * $millimeter;
$arm->sidePlate->extent->y = $arm->sidePlate->pivotDedendum + $mainBar->extent->y  - $mainBar->pivotInsetY;


$arm->endBlock->extent->x = $heaterBed->extent->x;
$arm->endBlock->extent->z = 9 * $millimeter;

$arm->pivotAxisRootPoint->position->y = 0;


$pivotShaft->diameter = 5 * $millimeter;
$pivotShaft->holeDiameter = $pivotShaft->diameter + 0.02 * $millimeter;
$pivotShaft->length = $mainBar->extent->x + 2*$arm->sidePlate->extent->x + 10 * $millimeter;

$arm->endBlockYMax = $arm->pivotAxisRootPoint->position->y + $mainBar->extent->y  - $mainBar->pivotInsetY;

$arm->viseScrew = new screw("M5-0.8");
$arm->viseScrew->length = 14 * $mm;

$arm->viseScrew->clampingDiameter = 10 * $mm;
$arm->viseClampingZone->extent->x = 5 * $mm;
$arm->viseScrews->intervalX = max([$arm->viseClampingZone->extent->x + $arm->viseScrew->freeFitDiameter, $arm->viseScrew->clampingDiameter]);

$arm->viseBar->extent->x = $arm->viseScrews->intervalX + $arm->viseScrew->clampingDiameter;//$arm->endBlock->extent->x;

$arm->viseBarYMax = $arm->endBlockYMax - 5 * $mm;
$arm->viseBar->extent->y = $arm->viseScrew->clampingDiameter;
$arm->viseBarYMin = $arm->viseBarYMax - $arm->viseBar->extent->y;

$arm->viseBar->extent->z = 6 * $mm;

$arm->viseBarPosition->y = average([$arm->viseBarYMax, $arm->viseBarYMin]);

$arm->terminalScrew = new screw("M5-0.8");
$arm->terminalScrew->length = 10* $mm;
$arm->terminalClampingDiameter = 14 * $mm;
$arm->terminalScrewPosition->y = $arm->viseBarYMin - $arm->terminalClampingDiameter/2;


$arm->endBlockYMin = $arm->terminalScrewPosition->y - $arm->terminalClampingDiameter/2;
$arm->endBlockPosition->y = average([$arm->endBlockYMax, $arm->endBlockYMin]);

$arm->endBlock->extent->y = $arm->endBlockYMax - $arm->endBlockYMin;

$arm->endBlockToSidePlateBindingScrew = new screw("M5-0.8");

$arm->endBlockToSidePlateBindingScrews->pattern->spanY = $arm->endBlock->extent->y - $arm->endBlockToSidePlateBindingScrew->clampingDiameter;
$arm->endBlockToSidePlateBindingScrews->pattern->countY = 2;
$arm->endBlockToSidePlateBindingScrews->pattern->intervalY = $arm->endBlockToSidePlateBindingScrews->pattern->spanY/($arm->endBlockToSidePlateBindingScrews->pattern->countY - 1) ;


$arm->endBlockToSidePlateBindingScrew = new screw("M5-0.8");

?>