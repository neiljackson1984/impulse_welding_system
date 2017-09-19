<?php

setParameterSetId("8531e071b2764bb4833039aed22074a5");


$millimeter = 1;
$inch = 25.4 * $millimeter;

$degree = 1;
$radian = 180/pi();



$mainBar->extentX = 30 * $millimeter;
$mainBar->extentY = 70 * $millimeter;
$mainBar->extentZ = 180 * $millimeter;

$screw->clearanceDiameter = 5.1 * $millimeter;


$pushCrown->post->diameter = 1.3 * $millimeter;

$cap->extentX = $mainBar->extentX;
$cap->extentY = $mainBar->extentY;
$cap->extentZ = 3 * $millimeter;
$cap->bore->diameter = $screw->clearanceDiameter;
$cap->postClearanceHole->diameter = $pushCrown->post->diameter + 0.1 * $millimeter;

$bearingShaft->outerDiameter=10*$millimeter;
$bearingShaft->extentZ=30*$millimeter;
$bearingShaft->bore->diameter=$screw->clearanceDiameter;


$pushCrown->posts->pattern->radius = $bearingShaft->outerDiameter/2 + $pushCrown->post->diameter/2 + 0.3 * $millimeter;

$pushCrown->extentX = 2*(1/sqrt(2) * $pushCrown->posts->pattern->radius + $pushCrown->post->diameter/2 + 0.3 * $millimeter);
$pushCrown->extentY = $pushCrown->extentX;
$pushCrown->extentZ = 40 * $millimeter;
$pushCrown->base->extentZ = 5 * $millimeter;

$pushCrown->boreDiameter = $screw->clearanceDiameter;

//$mainBar->trough->extentX - 0.1 * $millimeter;
$mainBar->trough->extentX = $pushCrown->extentX + 0.2 * $millimeter;
$mainBar->trough->extentY = $pushCrown->extentY + 0.2 * $millimeter;

$impulseBar->screwAxis->insetY = $mainBar->trough->extentY/2;

$screw->length = 100 * $millimeter;


//$spring->relaxedLength = 10 * $millimeter;
$spring->extentZ = 10*$millimeter;
?>