<?php

setParameterSetId("8d6b35c3ccc34e29a27bc5d1ed799a3c");


$millimeter = 1;
$inch = 25.4 * $millimeter;

$degree = 1;
$radian = 180/pi();




// the heaterBed is a right-rectangular flat surface that is the aluminum surface lying below and supporting the heating element.
$heaterBed->extentX = 30 * $millimeter;
$heaterBed->extentZ = 80 * $millimeter;

$mainBar->extentX = $heaterBed->extentX;
$mainBar->extentZ = $heaterBed->extentZ + 100 * $millimeter;
$mainBar->extentY = 70 * $millimeter;
$mainBar->foot->extentY = 15 * $millimeter;
$mainBar->pivotInsetZ = 30 * $millimeter;
$mainBar->pivotInsetY = 7 * $millimeter;


$arm->sidePlate->extentX = 3 * $millimeter; //stock sheet thickness

$arm->sidePlate->extentZ = 10 * $millimeter;
$arm->sidePlate->pivotDedendum = 4 * $millimeter;
$arm->sidePlate->extentY = $arm->sidePlate->pivotDedendum + $mainBar->extentY  - $mainBar->pivotInsetY;


$arm->endBlock->extentX = $heaterBed->extentX;
$arm->endBlock->extentY = 20 * $millimeter;
$arm->endBlock->extentZ = 9 * $millimeter;

$arm->pivotAxisRootPoint->position->y = 0;
$arm->endBlockPosition->y = $arm->pivotAxisRootPoint->position->y + $mainBar->extentY  - $mainBar->pivotInsetY - $arm->endBlock->extentY/2;

$pivotShaft->diameter = 5 * $millimeter;
$pivotShaft->holeDiameter = $pivotShaft->diameter + 0.02 * $millimeter;
$pivotShaft->length = $mainBar->extentX + 2*$arm->sidePlate->extentX + 10 * $millimeter;




?>