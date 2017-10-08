<?php

setParameterSetId("8d6b35c3ccc34e29a27bc5d1ed799a3c");
include "classes.php";

$millimeter = 1;
$inch = 25.4 * $millimeter;
$mm = $millimeter;

$degree = 1;
$radian = 180/pi();




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
$arm->endBlock->extent->y = 20 * $millimeter;
$arm->endBlock->extent->z = 9 * $millimeter;

$arm->pivotAxisRootPoint->position->y = 0;
$arm->endBlockPosition->y = $arm->pivotAxisRootPoint->position->y + $mainBar->extent->y  - $mainBar->pivotInsetY - $arm->endBlock->extent->y/2;

$pivotShaft->diameter = 5 * $millimeter;
$pivotShaft->holeDiameter = $pivotShaft->diameter + 0.02 * $millimeter;
$pivotShaft->length = $mainBar->extent->x + 2*$arm->sidePlate->extent->x + 10 * $millimeter;



$arm->viseScrew = new screw("M5-0.8");
$arm->viseClampingZone->extent->x = 5 * $mm;
$arm->viseScrews->intervalX = $arm->viseClampingZone->extent->x  + $arm->viseScrew->freeFitDiameter;

$arm->viseBar->extent->x = $arm->viseScrews->intervalX + $arm->viseScrew->clampingDiameter;//$arm->endBlock->extent->x;
$arm->viseBar->extent->y = $arm->viseScrew->clampingDiameter;
$arm->viseBar->extent->z = 6 * $mm;


?>