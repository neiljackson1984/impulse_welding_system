<?php


class screw
{
	public $pitch;
	public $tappableHoleDiameter;
	public $closeFitDiameter;
	public $freeFitDiameter;
	public $clampingDiameter;
	public $length;
	public $threadEmbedmentDiameter; //a cylinder of material of this diameter must be reserved around the threaded hole.
	public $threadSpec;
	
	function __construct($threadSpec)
	{
		global $inch, $mm;
		$this->threadSpec = $threadSpec;
		switch($threadSpec)
		{

			case "UTS_10-32":
				$this->pitch                   = 1/32   * $inch;
				$this->tappableHoleDiameter    = 0.1590 * $inch; 
				$this->closeFitDiameter        = 0.1960 * $inch;
				$this->freeFitDiameter         = 0.2010 * $inch; 
				$this->clampingDiameter        = 0.85/0.3860 * $this->closeFitDiameter; //UNVERIFIED
				$this->threadEmbedmentDiameter = 1.33 * $this->closeFitDiameter;
			break;
			
			case "UTS_1/4-20":
				$this->pitch                    = 1/20  * $inch;
				$this->tappableHoleDiameter     = 0.201 * $inch; 
				$this->closeFitDiameter         = 0.257 * $inch;
				$this->freeFitDiameter          = 0.266 * $inch; 
				$this->clampingDiameter         = 0.9   * $inch; //UNVERIFIED
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "UTS_3/8-16":
				$this->pitch                    = 1/16   * $inch;
				$this->tappableHoleDiameter     = 0.3125 * $inch; 
				$this->closeFitDiameter         = 0.3860 * $inch; 
				$this->freeFitDiameter          = 0.3970 * $inch; 
				$this->clampingDiameter         = 0.85   * $inch;
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "UTS_5/16-18":
				$this->pitch                    = 1/18   * $inch;
				$this->tappableHoleDiameter     = 0.2570 * $inch; 
				$this->closeFitDiameter         = 0.323  * $inch; 
				$this->freeFitDiameter          = 0.332  * $inch; 
				$this->clampingDiameter         = 1      * $inch; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M6-1":
				$this->pitch                    = 1      * $mm  ;
				$this->tappableHoleDiameter     = 5      * $mm  ; 
				$this->closeFitDiameter         = 6.30   * $mm  ; 
				$this->freeFitDiameter          = 6.60   * $mm  ; 
				$this->clampingDiameter         = 0.9    * $inch; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M5-0.8":
				$this->pitch                    = 0.8      * $mm  ;
				$this->tappableHoleDiameter     = 4.20     * $mm  ; 
				$this->closeFitDiameter         = 5.25     * $mm  ; 
				$this->freeFitDiameter          = 5.50     * $mm  ; 
				$this->clampingDiameter         = 0.8      * $inch ; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M4-0.7":
				$this->pitch                    = 0.7      * $mm  ;
				$this->tappableHoleDiameter     = 3.3      * $mm  ; 
				$this->closeFitDiameter         = 4.20     * $mm  ; 
				$this->freeFitDiameter          = 4.40     * $mm  ; 
				$this->clampingDiameter         = 15       * $mm ; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M3-0.5":
				$this->pitch                    = 0.50     * $mm  ;
				$this->tappableHoleDiameter     = 2.50     * $mm  ; 
				$this->closeFitDiameter         = 3.15     * $mm  ; 
				$this->freeFitDiameter          = 3.30     * $mm  ; 
				$this->clampingDiameter         = 8.4      * $mm ; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			
			default:
				trigger_error("unrecognized threadSpec ($threadSpec) was passed to screw constructor." );
			break;
		}
	}
}


class rightRectanguloid extends properties
{
	// the corner of the angle is on the xMin yMin intersection.
	public $extentX;
	public $extentY;
	public $extentZ;
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		//default values:
		$this->extentX = 1 * $inch ;  
		$this->extentY = 2 * $inch; 
		$this->extentZ = 3 * $inch; 
	}
}

abstract class rectangularBeam extends rightRectanguloid
{
	public $crossSectionReference;
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		//default values:
		$this->extentX = 10 * $inch ;  //short side of cross section 
		$this->extentY = 0.5 * $inch; //long side of cross section 
		$this->extentZ = 0.5 * $inch; // extrusion length 
	}
}

class eightyTwenty1010Beam extends rectangularBeam
{
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extentX = 1 * $inch ; //short side of cross section 
		$this->extentY = 1 * $inch; //long side of cross section 
		$this->crossSectionReference = "eightyTwenty_1010.SLDBLK";
		$this->extentZ = 0.5 * $inch; // extrusion length 
	}
}

class eightyTwenty1020Beam extends rectangularBeam
{
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extentX = 1 * $inch ; //short side of cross section 
		$this->extentY = 2 * $inch; //long side of cross section 
		$this->crossSectionReference = "eightyTwenty_1015.SLDBLK";
		$this->extentZ = 0.5 * $inch; // extrusion length 
	}
}

class eightyTwenty1515Beam extends rectangularBeam
{
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extentX = 1.5 * $inch ; //short side of cross section 
		$this->extentY = 1.5 * $inch; //long side of cross section 
		$this->crossSectionReference = "eightyTwenty_1515.SLDBLK";
		$this->extentZ = 0.5 * $inch; // extrusion length 
	}
}

class eightyTwenty1530Beam extends rectangularBeam
{
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extentX = 1.5 * $inch ; //short side of cross section 
		$this->extentY = 3.0 * $inch; //long side of cross section 
		$this->crossSectionReference = "eightyTwenty_1530.SLDBLK";
		$this->extentZ = 0.5 * $inch; // extrusion length 
	}
}

class way extends properties
{
	public $extentX;
	public $extentY;
	public $extentZ;
	public $mountingSurfaceOffset;
	public $crossSectionReference;
}

class carriage extends properties
{
	public $extentX;
	public $extentY;
	public $extentZ;
	public $mountingSurfaceOffset;
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extentX = 3 * $inch ; //default
		$this->extentY = 1 * $inch; //default
		$this->extentZ = 0.5 * $inch; // extrusion length 
		$this->mountingSurfaceOffset = 0.5 * $inch; //FIX ME
	}
}

class igus10W40Way extends way
{
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extentX = 50 * $mm; //confirmed
		$this->extentY = 14 * $mm; //confirmed
		$this->crossSectionReference = "igus_10W40.SLDBLK";
		$this->extentZ = 0.5 * $inch; // extrusion length 
		$this->mountingSurfaceOffset = 9 * $mm; //confirmed
		$this->deckPositionY = -3.5*$mm; //this is the y coordinate of the y-normal flat between the two cylinders.
	}
}

class igus10W40x100Carriage extends carriage
{
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extentX = 73 * $mm ;  //confirmed
		$this->extentY = 22.5 * $mm; //confirmed
		$this->extentZ = 100 * $mm; // confirmed
		$this->mountingSurfaceOffset = 15 * $mm; //confirmed
	}
}

class extrudedAngle extends properties
{
	// the corner of the angle is on the xMin yMin intersection.
	public $extentX;
	public $extentY;
	public $extentZ;
	public $thickness;
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		//default values:
		$this->extentX = 1 * $inch ;  //short leg length 
		$this->extentY = 2 * $inch; //long leg length
		$this->extentZ = 0.5 * $inch; // extrusion length 
		$this->thickness = 1/8 * $inch; // thickness of material
	}
}

class sprocket extends properties
{
	public $extentZ; //this is the overall thickness of the sprocket
	//public $pitchDiameter;
	public $boreDiameter;
	public $toothCount;
	public $chainOffsetZ; //the center of the chain rides along in the (z = 0 + $chainOffsetZ) plane.
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		//default values:
		$this->extentZ = 0.5 * $inch; // overall thickness of the sprocket
		$this->boreDiameter = 0.5 * $inch; 
		$this->toothPitch = 0.5 * $inch; 
		$this->toothCount = 10; 
		$this->chainOffsetZ = 0 * $inch; 
	}
	
	public function get_pitchDiameter()
	{
		global $inch;
		return ($this->toothCount * $this->toothPitch)/pi();
	}
	
	public function get_outerDiameter()
	{
		global $inch, $mm;
		//copied from McMaster catalog
		switch ($this->toothCount)
		{
			case  9: return	1.67  * $inch; break;
			case 10: return	1.84  * $inch; break;
			case 11: return	2     * $inch; break;
			case 12: return	2.17  * $inch; break;
			case 13: return	2.33  * $inch; break;
			case 14: return	2.49  * $inch; break;
			case 15: return	2.65  * $inch; break;
			case 16: return	2.81  * $inch; break;
			case 17: return	2.97  * $inch; break;
			case 18: return	3.14  * $inch; break;
			case 19: return	3.3   * $inch; break;
			case 20: return	3.46  * $inch; break;
			case 21: return	3.62  * $inch; break;
			case 22: return	3.78  * $inch; break;
			case 23: return	3.94  * $inch; break;
			case 24: return	4.1   * $inch; break;
			case 25: return	4.26  * $inch; break;
			case 26: return	4.42  * $inch; break;
			case 28: return	4.74  * $inch; break;
			case 30: return	5.06  * $inch; break;
			case 32: return	5.38  * $inch; break;
			case 34: return	5.7   * $inch; break;
			case 35: return	5.86  * $inch; break;
			case 36: return	6.02  * $inch; break;
			case 38: return	6.33  * $inch; break;
			case 40: return	6.65  * $inch; break;
			case 42: return	6.97  * $inch; break;
			case 44: return	7.29  * $inch; break;
			case 45: return	7.45  * $inch; break;
			case 46: return	7.61  * $inch; break;
			case 48: return	7.93  * $inch; break;
			case 50: return	8.25  * $inch; break;
			case 52: return	8.57  * $inch; break;
			case 54: return	8.88  * $inch; break;
			case 60: return	9.84  * $inch; break;
			case 70: return	11.43 * $inch; break;
			case 72: return	11.75 * $inch; break;
			case 80: return	13.03 * $inch; break;
			case 96: return	15.57 * $inch; break;
		}
	}
	
	public function get_hubDiameter()
	{
		global $inch, $mm;
		//copied from McMaster catalog
		switch ($this->toothCount)
		{
			case 9 : return ( 1 + 1/16  )* $inch; break;
			case 10: return ( 1 + 1/4   )* $inch; break;
			case 11: return ( 1 + 3/8   )* $inch; break;
			case 12: return ( 1 + 9/16  )* $inch; break;
			case 13: return ( 1 + 9/16  )* $inch; break;
			case 14: return ( 1 + 11/16 )* $inch; break;
			case 15: return ( 1 + 13/16 )* $inch; break;
			case 16: return ( 2         )* $inch; break;
			case 17: return ( 2 + 1/8   )* $inch; break;
			case 18: return ( 2 + 5/16  )* $inch; break;
			case 19: return ( 2 + 1/2   )* $inch; break;
			case 20: return ( 2 + 5/8   )* $inch; break;
			case 21: return ( 2 + 3/4   )* $inch; break;
			case 22: return ( 2 + 7/8   )* $inch; break;
			case 23: return ( 3         )* $inch; break;
			case 24: return ( 3         )* $inch; break;
			case 25: return ( 3 + 1/4   )* $inch; break;
			case 26: return ( 3 + 1/4   )* $inch; break;
			case 28: return ( 3 + 1/4   )* $inch; break;
			case 30: return ( 3 + 1/4   )* $inch; break;
			case 32: return ( 3 + 1/4   )* $inch; break;
			case 34: return ( 3 + 1/4   )* $inch; break;
			case 35: return ( 3 + 1/4   )* $inch; break;
			case 36: return ( 3 + 1/4   )* $inch; break;
			case 38: return ( 3 + 1/4   )* $inch; break;
			case 40: return ( 3 + 1/2   )* $inch; break;
			case 42: return ( 3 + 1/2   )* $inch; break;
			case 44: return ( 3 + 1/2   )* $inch; break;
			case 45: return ( 3 + 1/2   )* $inch; break;
			case 46: return ( 3 + 1/2   )* $inch; break;
			case 48: return ( 3 + 1/2   )* $inch; break;
			case 50: return ( 3 + 1/2   )* $inch; break;
			case 52: return ( 3 + 1/2   )* $inch; break;
			case 54: return ( 3 + 1/2   )* $inch; break;
			case 60: return ( 3 + 1/2   )* $inch; break;
			case 70: return ( 4         )* $inch; break;
			case 72: return ( 4         )* $inch; break;
			case 80: return ( 4         )* $inch; break;
			case 96: return ( 4         )* $inch; break;
		}
	}
	/*
		"9toothSprocket" = Iif( "externalParameters.this.toothCount" = 9  , "unsuppressed", "suppressed" )
		"10toothSprocket" = Iif( "externalParameters.this.toothCount" = 10 , "unsuppressed", "suppressed" )
		"11toothSprocket" = Iif( "externalParameters.this.toothCount" = 11 , "unsuppressed", "suppressed" )
		"12toothSprocket" = Iif( "externalParameters.this.toothCount" = 12 , "unsuppressed", "suppressed" )
		"13toothSprocket" = Iif( "externalParameters.this.toothCount" = 13 , "unsuppressed", "suppressed" )
		"14toothSprocket" = Iif( "externalParameters.this.toothCount" = 14 , "unsuppressed", "suppressed" )
		"15toothSprocket" = Iif( "externalParameters.this.toothCount" = 15 , "unsuppressed", "suppressed" )
		"16toothSprocket" = Iif( "externalParameters.this.toothCount" = 16 , "unsuppressed", "suppressed" )
		"17toothSprocket" = Iif( "externalParameters.this.toothCount" = 17 , "unsuppressed", "suppressed" )
		"18toothSprocket" = Iif( "externalParameters.this.toothCount" = 18 , "unsuppressed", "suppressed" )
		"19toothSprocket" = Iif( "externalParameters.this.toothCount" = 19 , "unsuppressed", "suppressed" )
		"20toothSprocket" = Iif( "externalParameters.this.toothCount" = 20 , "unsuppressed", "suppressed" )
		"21toothSprocket" = Iif( "externalParameters.this.toothCount" = 21 , "unsuppressed", "suppressed" )
		"22toothSprocket" = Iif( "externalParameters.this.toothCount" = 22 , "unsuppressed", "suppressed" )
		"23toothSprocket" = Iif( "externalParameters.this.toothCount" = 23 , "unsuppressed", "suppressed" )
		"24toothSprocket" = Iif( "externalParameters.this.toothCount" = 24 , "unsuppressed", "suppressed" )
		"25toothSprocket" = Iif( "externalParameters.this.toothCount" = 25 , "unsuppressed", "suppressed" )
		"26toothSprocket" = Iif( "externalParameters.this.toothCount" = 26 , "unsuppressed", "suppressed" )
		' "27toothSprocket" = Iif( "externalParameters.this.toothCount" = 27 , "unsuppressed", "suppressed" )
		"28toothSprocket" = Iif( "externalParameters.this.toothCount" = 28 , "unsuppressed", "suppressed" )
			
		
		"9toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 9  , "unsuppressed", "suppressed" )
		"10toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 10 , "unsuppressed", "suppressed" )
		"11toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 11 , "unsuppressed", "suppressed" )
		"12toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 12 , "unsuppressed", "suppressed" )
		"13toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 13 , "unsuppressed", "suppressed" )
		"14toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 14 , "unsuppressed", "suppressed" )
		"15toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 15 , "unsuppressed", "suppressed" )
		"16toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 16 , "unsuppressed", "suppressed" )
		"17toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 17 , "unsuppressed", "suppressed" )
		"18toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 18 , "unsuppressed", "suppressed" )
		"19toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 19 , "unsuppressed", "suppressed" )
		"20toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 20 , "unsuppressed", "suppressed" )
		"21toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 21 , "unsuppressed", "suppressed" )
		"22toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 22 , "unsuppressed", "suppressed" )
		"23toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 23 , "unsuppressed", "suppressed" )
		"24toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 24 , "unsuppressed", "suppressed" )
		"25toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 25 , "unsuppressed", "suppressed" )
		"26toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 26 , "unsuppressed", "suppressed" )
		' "27toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 27 , "unsuppressed", "suppressed" )
		"28toothSprocket_mates" = Iif( "externalParameters.this.toothCount" = 28 , "unsuppressed", "suppressed" )
	*/
	
	
}


class standoff extends properties
{
	public $extentZ; //this is the overall thickness of the standoff
	public $boreDiameter;
	public $crossSectionSidesCount; //if this is less than or equal to 2, it means circular.
	public $circumscribedDiameter; 
	//public $inscribedDiameter; //i.e. distance between the flats
	public $screw;
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		//default values:
		$this->extentZ = 1 * $inch; // overall thickness of the standoff
		$this->boreDiameter = 0.2 * $inch; 
		$this->crossSectionSidesCount = 5;
		$this->circumscribedDiameter = 0.5 * $inch; 
		$this->screw = new screw("UTS_10-32");
	}
	
	//we use a sidecount of 2 or less to indicate that the cross section should be circular.
	// "polygonalStandoff_extrude" =    Iif ( "externalParameters.this.crossSectionSidesCount" > 2 , "unsuppressed" , "suppressed" )
	// "polygonalStandoff_sketch" =     Iif ( "externalParameters.this.crossSectionSidesCount" > 2 , "unsuppressed" , "suppressed" )
	// "boreForPolygonalStandoff_cut" = Iif ( "externalParameters.this.crossSectionSidesCount" > 2 , "unsuppressed" , "suppressed" )
	
	// "roundStandoff_extrude" = Iif ( "externalParameters.this.crossSectionSidesCount" <= 2 , "unsuppressed" , "suppressed" )
	// "boreForRoundStandoff_cut" = Iif ( "externalParameters.this.crossSectionSidesCount" <= 2 , "unsuppressed" , "suppressed" )


	
	public function set_inscribedDiameter($inscribedDiameter) 
	{
		//$this->inscribedDiameter = $x;
		$this->circumscribedDiameter = 
			$inscribedDiameter *
				( 
						$this->crossSectionSidesCount <= 2 
					? 
						1 
					: 
						1/cos ( pi() / $this->crossSectionSidesCount ) 
				);
	}
	
	public function get_inscribedDiameter() 
	{
		return
			$this->circumscribedDiameter *
				( 
						$this->crossSectionSidesCount <= 2 
					? 
						1 
					: 
						cos ( pi() / $this->crossSectionSidesCount ) 
				);
	}
	
	
	// // public function __get($name){
		// // switch($name)
		// // {
			// // case "inscribedDiameter" :
				// // return
					// // $this->circumscribedDiameter *
						// // ( 
								// // $this->crossSectionSidesCount <= 2 
							// // ? 
								// // 1 
							// // : 
								// // cos ( pi() / $this->crossSectionSidesCount ) 
						// // );
			// // break;	

			// // default: break;
		// // }
	// // }

	// // public function __set($name, $value){
		// // switch($name)
		// // {
			// // case "inscribedDiameter" :
				// // $this->circumscribedDiameter = 
					// // $value *
						// // ( 
								// // $this->crossSectionSidesCount <= 2 
							// // ? 
								// // 1 
							// // : 
								// // 1/cos ( pi() / $this->crossSectionSidesCount ) 
						// // );
				// // return $value;
			// // break;
			
			// // default: break;
		// // }
	// // }
}


class nemaMotorAdapterPlate extends rightRectanguloid
{
	public $motor1;
	public $motor2;
	public $boreDiameter;
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		//default values:
		
		// nema23:
		$this->motor1->plateSideLength = 2.3 * $inch; //confirmed
		$this->motor1->mountHoles->holeDiameter           = 0.195 * $inch;                                                    //confirmed
		$this->motor1->cutoutDiameter                     = 1.500 * $inch;                                                    //confirmed
		$this->motor1->mountHoles->pattern->diameter      = 2.625 * $inch;                                                    //confirmed
		$this->motor1->mountHoles->pattern->count         = 4;                                                                //confirmed
		$this->motor1->mountHoles->pattern->offsetAngle   = 360   * $degree / $this->motor1->mountHoles->pattern->count / 2;  //confirmed
		

		// // nema34  
		// $this->motor2->plateSideLength                    = 3.4   * $inch;                                                    //confirmed
		// $this->motor2->mountHoles->holeDiameter           = 0.218 * $inch;                                                    //confirmed
		// $this->motor2->cutoutDiameter                     = 2.875 * $inch;                                                    //confirmed 
		// $this->motor2->mountHoles->pattern->diameter      = 3.875 * $inch;                                                    //confirmed
		// $this->motor2->mountHoles->pattern->count         = 4;                                                                //confirmed
		// $this->motor2->mountHoles->pattern->offsetAngle   = 360   * $degree / $this->motor2->mountHoles->pattern->count / 2;  //confirmed
		
		//for nema42
		$this->motor2->plateSideLength                    = 4.2   * $inch;                                                     //confirmed
		$this->motor2->mountHoles->holeDiameter           = 0.281 * $inch;                                                     //confirmed
		$this->motor2->cutoutDiameter                     = 2.186 * $inch;                                                     //confirmed 
		$this->motor2->mountHoles->pattern->diameter      = 4.950 * $inch;                                                     //confirmed
		$this->motor2->mountHoles->pattern->count         = 4;                                                                 //confirmed
		$this->motor2->mountHoles->pattern->offsetAngle   = 360   * $degree / $this->motor2->mountHoles->pattern->count / 2;   //confirmed
		
		//oversize cutout diameters slighlty above and beyond the nema standard, to ease the fit.
		$this->motor1->cutoutDiameter *= 1.01; 
		$this->motor2->cutoutDiameter *= 1.01; 
		
		$this->cutoutDiameter = min([$this->motor1->cutoutDiameter, $this->motor2->cutoutDiameter]);
		$this->plateSideLength = max([$this->motor1->plateSideLength, $this->motor2->plateSideLength]);
		
		$this->extentX = $this->plateSideLength;
		$this->extentY = $this->plateSideLength;
		$this->extentZ = 1/4 * $inch; //stock thickness
		$this->cornerRoundingRadius = 1/4 * $inch;
	}
	
}


class shaft extends properties
{
	public $extentZ; //this is the overall length of the shaft
	public $diameter; 
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		//default values:
		$this->extentZ = 5 * $inch; 
		$this->diameter = 0.4 * $inch; 
	}
}

//this is a dummy class to trigger the prototype copying mechanism, in order to share the flange bearing between both versions of the sprocketBlock
class flangeBearing extends properties
{
	
}

?>