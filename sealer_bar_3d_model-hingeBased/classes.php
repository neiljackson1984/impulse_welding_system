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
				$this->majorDiameter           = 0.1900 * $inch;
				$this->pitch                   = 1/32   * $inch;
				$this->tappableHoleDiameter    = 0.1590 * $inch; 
				$this->closeFitDiameter        = 0.1960 * $inch;
				$this->freeFitDiameter         = 0.2010 * $inch; 
				$this->clampingDiameter        = 0.85/0.3860 * $this->closeFitDiameter; //UNVERIFIED
				$this->threadEmbedmentDiameter = 1.33 * $this->closeFitDiameter;
			break;
			
			case "UTS_1/4-20":
				$this->majorDiameter            = 1/4 	* $inch;
				$this->pitch                    = 1/20  * $inch;
				$this->tappableHoleDiameter     = 0.201 * $inch; 
				$this->closeFitDiameter         = 0.257 * $inch;
				$this->freeFitDiameter          = 0.266 * $inch; 
				$this->clampingDiameter         = 0.9   * $inch; //UNVERIFIED
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "UTS_3/8-16":
				$this->majorDiameter            = 3/8    * $inch;
				$this->pitch                    = 1/16   * $inch;
				$this->tappableHoleDiameter     = 0.3125 * $inch; 
				$this->closeFitDiameter         = 0.3860 * $inch; 
				$this->freeFitDiameter          = 0.3970 * $inch; 
				$this->clampingDiameter         = 0.85   * $inch;
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "UTS_5/16-18":
				$this->majorDiameter            = 5/16   * $inch;
				$this->pitch                    = 1/18   * $inch;
				$this->tappableHoleDiameter     = 0.2570 * $inch; 
				$this->closeFitDiameter         = 0.323  * $inch; 
				$this->freeFitDiameter          = 0.332  * $inch; 
				$this->clampingDiameter         = 1      * $inch; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M6-1":
				$this->majorDiameter            = 6		 * $mm;
				$this->pitch                    = 1      * $mm;
				$this->tappableHoleDiameter     = 5      * $mm; 
				$this->closeFitDiameter         = 6.30   * $mm; 
				$this->freeFitDiameter          = 6.60   * $mm; 
				$this->clampingDiameter         = 0.9    * $inch; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M5-0.8":
				$this->majorDiameter            = 5        * $mm;
				$this->pitch                    = 0.8      * $mm;
				$this->tappableHoleDiameter     = 4.20     * $mm; 
				$this->closeFitDiameter         = 5.25     * $mm; 
				$this->freeFitDiameter          = 5.50     * $mm; 
				$this->clampingDiameter         = 0.8      * $inch ; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M4-0.7":
				$this->majorDiameter            = 4        * $mm;
				$this->pitch                    = 0.7      * $mm;
				$this->tappableHoleDiameter     = 3.3      * $mm; 
				$this->closeFitDiameter         = 4.20     * $mm; 
				$this->freeFitDiameter          = 4.40     * $mm; 
				$this->clampingDiameter         = 15       * $mm ; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			case "M3-0.5":
				$this->majorDiameter            = 3        * $mm;
				$this->pitch                    = 0.50     * $mm;
				$this->tappableHoleDiameter     = 2.50     * $mm; 
				$this->closeFitDiameter         = 3.15     * $mm; 
				$this->freeFitDiameter          = 3.30     * $mm; 
				$this->clampingDiameter         = 8.4      * $mm; //unverified
				$this->threadEmbedmentDiameter  = 1.33 * $this->closeFitDiameter;
			break;
			
			
			default:
				trigger_error("unrecognized threadSpec ($threadSpec) was passed to screw constructor." );
			break;
		}
	}
}

class vector3 extends properties
{
	public $x;
	public $y;
	public $z;
	
	public function __construct()
	{
		//default values
		$this->x = 0;
		$this->y = 0;
		$this->z = 0;
	}
}

//richtRectanguloid represents a right-rectanguloid whose own frame is parallel to the parent frame.
class rightRectanguloid extends properties
{
	public $extent;
	public $position; //position within the containing space.
	
	public function get_minCorner()
	{
		$returnValue = new vector3;
		$returnValue->x = $this->position->x - $this->extent->x/2;
		$returnValue->y = $this->position->y - $this->extent->y/2;
		$returnValue->z = $this->position->z - $this->extent->z/2;

		return $returnValue;
	}
	
	public function get_maxCorner()
	{
		$returnValue = new vector3;
		$returnValue->x = $this->position->x + $this->extent->x/2;
		$returnValue->y = $this->position->y + $this->extent->y/2;
		$returnValue->z = $this->position->z + $this->extent->z/2;

		return $returnValue;
	}
	
	public function __construct()
	{
		global $inch, $mm, $degree, $radian;
		$this->extent = new vector3;
		$this->position = new vector3;
		//default values:
		$this->extent->x = 1 * $inch ;  
		$this->extent->y = 2 * $inch; 
		$this->extent->z = 3 * $inch; 
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



?>