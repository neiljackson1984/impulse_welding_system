<?php 
/*
On the command line, run
php equationProcessor.php equations.php > equations.txt 
to generate the equations file for solidworks.
This php script 'include's the file named by the first argument.
Then it echos all newly defined numeric variables in the solidworks equations file format.
-Neil Jackson
2015-03-29
*/



//PREAMBLE CODE
{ //PREAMBLE CODE TO NOTE ANY PRE-EXISITING VARIBALE NAMES
$initialNames = "something" ; //we declare $initalNames here so that array_keys($GLOBALS) below, will contain "initialNames"
$solidworksFileOutputStream = "something" ;
$options = "something" ;
$parameterSetId = "UNIDENTIFIED";
$jsonOutputFilePath = "something";
$solidworksOutputFilePath = "something";
$usageMessage = "nothing";
$php_errormsg = "nothing";
$prototypesPath = "";
$updateClassInstances = false;

$initialNames = array_keys($GLOBALS);
}





// abstract class propContainer implements IteratorAggregate
// {
	// //public $a;
	// //public $b;
	// //public $c;
	
	// //private function get_c() {return $this->a + $this->b;}
	
	// public function __get($name)
	// {
		// // if($name=="c"){return $this->a + $this->b;}
		// // else {return null;}
		// return $this->{"get_" . $name}();
	// }
	
	// public function getIterator()
	// {
		// //$myIterator = new ArrayIterator($this);
		// $myIterator = new ArrayIterator($this);
		// //$myIterator->append(99);
		// // $myIterator->offsetSet("ahoy",99);
		// // $myIterator->offsetSet("c",$this->{"c"});
		
		// // $methods = (new reflectionClass($this))->getMethods(ReflectionMethod::IS_PRIVATE);
		// //$methods = (new reflectionClass($this))->getMethods();
		// $methods = (new reflectionClass($this))->getMethods(ReflectionMethod::IS_PROTECTED);
		// foreach($methods as $method)
		// {
			// //fwrite(STDERR, "now looking at " . $method->name . "\n");
			
			// if( preg_match('/^get_(.+)$/', $method->name , $matches)>0 )
			// {
				// $nameOfProperty = $matches[1];
				// //fwrite(STDERR, "found property " . $nameOfProperty . "\n");
				// $myIterator->offsetSet($nameOfProperty,$this->{$nameOfProperty});
			// }
		// };
		// return $myIterator;
	// }
	
	// public function __construct($x=[])
	// {
		// foreach($x as $key => $value)
		// {
			// $this->{$key} = $value;
		// }
	// }
// }

// $x = new propContainer(["a"=>13,"b"=>14]);
// $x->gg = 22;


// foreach($x as $key=>$value)
// {
	// fwrite(STDERR, $key . " => " . $value . "\n");
// }



// inspired by http://php.net/manual/en/language.oop5.overloading.php#object.set
abstract class properties Implements IteratorAggregate
{
  public function __get( $property )
  {
    if( ! is_callable( array($this,'get_'.(string)$property) ) )
      throw new BadPropertyException($this, (string)$property);

    return call_user_func( array($this,'get_'.(string)$property) );
  }
  
  public function getIterator()
  {
	  // we will return an iterator that will look like an array whose keys are all the public members of the class and any "get_..." functions.
	  $temp = new ArrayObject($this);
	  
	  // foreach (["a"=>55,"b"=>66] as $name => $value)
	  // {
		  // $temp->offsetSet($name, $value);
	  // }
	  $myReflectionClass = new ReflectionClass($this);
	  
	  foreach ($myReflectionClass->getMethods( ReflectionMethod::IS_PUBLIC ) as $method)
	  {
			if(substr($method->name, 0, 4) == "get_") //perhaps we should also check to confirm that the $method takes no arguments, and that the method name is not identically "get_"
			{
				$temp->offsetSet( substr($method->name, 4), call_user_func(array($this,$method->name)));
			}
	  }
	  
	  
	  return $temp->getIterator();
  }

}




function setParameterSetId($x)
{
	global $parameterSetId;
	$parameterSetId = $x;
}


$options = getopt("", ["source:","solidworksOutputFile:","jsonOutputFile:","prototypesPath:","updateClassInstances:","classInstancesPath:"] );
//User definitions go here:
// prototypesPath	-- this is the path that contains the prototype solidworks files (sldprt and sldasm files whose filenames are the same as class names)
// updateClassInstances -- iff set to true, then we will try to copy the prototype files to the classInstancesPath folder, renaming with the instance name.

if(array_key_exists("source", $options))
{

	//suppress error reporting for warnings, because the source script, the way I write it, tends to generates a lot of the warnings: 'creating default object from empty value', which clutter up the output.
	error_reporting(error_reporting() & ~E_WARNING);
	include $options["source"];
} else
{
	echo "error. You must pass this script a 'source' parameter.\n";
	exit(-1);
} 





{ //POSTAMBLE CODE TO ECHO GLOBAL VARIABLES IN SOLIDWORKS EQUATION FORMAT
	/*
		//DOES NOT WORK IN PHP VERSION < 5.6.0 :
		$variablesToExport = 
			array_filter
			(
				$GLOBALS,
				function($variableName){return !in_array($variableName, $initialNames  )  ;},
				ARRAY_FILTER_USE_KEY
			);
	*/
	
	$weGeneratedAJsonFile = false; //this will be set to true later if we actual do generate a json file
	
	$variableNamesToExport = array_diff(array_keys($GLOBALS), $initialNames );
	$variablesToExport = [];
	foreach ($variableNamesToExport as $name)
	{
		$variablesToExport[$name] = $GLOBALS[$name];
	}
	
	
	if(array_key_exists("solidworksOutputFile", $options))
	{
		$solidworksOutputFilePath = str_replace("<id>",$parameterSetId,$options["solidworksOutputFile"]);
		
		// // // // } else
		// // // // {
			// // // // $solidworksOutputFilePath = "parameters-" . $parameterSetId . ".solidworksEquations";
		// // // // } 
		$solidworksFileOutputStream = fopen($solidworksOutputFilePath,'w');
		toSldWorksEquationSyntax($variablesToExport);
		fclose($solidworksFileOutputStream);
	}	
	if(array_key_exists("jsonOutputFile", $options))
	{
		$jsonOutputFilePath = str_replace("<id>",$parameterSetId,$options["jsonOutputFile"]);
		// // // // } else
		// // // // {
			// // // // $jsonOutputFilePath = "parameters-" . $parameterSetId . ".json";
		// // // // } 
		//print_r($variablesToExport);
		file_put_contents(
			$jsonOutputFilePath,
			//json_encode($variablesToExport,JSON_HEX_TAG+JSON_HEX_AMP+JSON_HEX_APOS+JSON_HEX_QUOT+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES)
			json_encode(object_to_array($variablesToExport),JSON_HEX_TAG+JSON_HEX_AMP+JSON_HEX_APOS+JSON_HEX_QUOT+JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES) //2016-04-30: wrapped object_to_array here to allow custom classes that implement ItteratorAggregate to be correctly encoded to json.
		);
		$weGeneratedAJsonFile = true;
	}
	
	if(array_key_exists("prototypesPath",$options))
	{
		$prototypesPath = $options["prototypesPath"];
	} else
	{
		$prototypesPath = dirname(realpath($options["source"])) . DIRECTORY_SEPARATOR . "prototypes"; //DEFAULT prototypesPath
		//echo "protoypesPath: " . $prototypesPath . "\n\n";
	}

	if(array_key_exists("classInstancesPath",$options))
	{
		$classInstancesPath = $options["classInstancesPath"];
	} else
	{
		$classInstancesPath = dirname(realpath($options["source"])) ; //DEFAULT classInstancesPath
		//echo "protoypesPath: " . $prototypesPath . "\n\n";
	}
	
	if(array_key_exists("updateClassInstances",$options) && $options["updateClassInstances"] == "true")
	{

		$prototypeModels = []; //an array whose keys are the class names, and whose values are the fully qualified path to sldworks files
		foreach(scandir($prototypesPath) as $filename)
		{
			$thisFile = $prototypesPath . DIRECTORY_SEPARATOR . $filename;
			if(
				is_file($thisFile) 
				&&  
				in_array( 
					strtolower(pathinfo($thisFile, PATHINFO_EXTENSION)), 
					["sldprt", "sldasm"]
				)
			) //if $thisFile is a file (as opposed to a folder) and has a file extension indicating that it is either a solidworks part or a solidworks assembly...
			{
				$prototypeModels[pathinfo($thisFile, PATHINFO_FILENAME)] = realpath($thisFile);
			}
		}
		//might consider checking whether there is a sldprt and an sldasm with the same file name, to issue a warning.
		echo "prototypeModels: " . "\n"; print_r($prototypeModels);
		//print_r($variablesToExport);
		$classInstances = [];
		updateClassInstances($variablesToExport);
		array_map("sort",$classInstances); //sort each sublist of names
		ksort($classInstances); //sort the array by key name
		print_r($classInstances);

	}
	
	if($weGeneratedAJsonFile)
	{
		
		//making the presence of the 'prototypesPath' command line option be the thing that determines whether we put 
		// a copy of the paramters file in the $prototypesPath is a bit sloppy.  Same for the classInstancesPath.
		// The goal is that even when we are not updating the class instance files, we want to put a copy of the parameters file in the classInstances folder,
		// and I did not want to clutter up the command line syntax with an explicit option to enable copying the parameters file.
		
		if(array_key_exists("prototypesPath",$options)) 
		{
			mkdir($prototypesPath, 0777, true); //make the destination directory if it does not already exist.
			copy($jsonOutputFilePath, $prototypesPath . DIRECTORY_SEPARATOR . basename($jsonOutputFilePath)); //this is a bit of a hack to help prototypes whose instances will expect to see the parameters file in their own folder in case we are not copying the instance files into the same folder as the parameters file; we copy the parameters file into the folder where the class instances are being copied. 
		
		}

		if(array_key_exists("classInstancesPath",$options)) //making the presence of the 'classInstancesPath' command line option be the thing that determines whether we put a copy of the paramters file in the $classInstancesPath is a bit of a hack
		{
			mkdir($classInstancesPath, 0777, true); //make the destination directory if it does not already exist.
			copy($jsonOutputFilePath, $classInstancesPath . DIRECTORY_SEPARATOR . basename($jsonOutputFilePath)); //this is a bit of a hack to help prototypes whose instances will expect to see the parameters file in their own folder in case we are not copying the instance files into the same folder as the parameters file; we copy the parameters file into the folder where the class instances are being copied. 
		}
		
		
	}
	
	
}


$usageMessage = 
	"=======================================================" . "\n" .
	"To susbcribe to the parameter set, add the following custom property to your solidworks model: " . "\n" . "\n" .
	"externalParametersConfig.file: " . $jsonOutputFilePath . "\n" . 
	"The path may be relative to the model file, if desired.  For instance, if the externalParameters file resides in the same directory as the model file, you could set " . "\n" . "\n" .
	
	"externalParametersConfig.file: " . "." . DIRECTORY_SEPARATOR . basename($jsonOutputFilePath) . "\n" .
	"=======================================================" . "\n" . 
	"";
	
echo $usageMessage;
//fwrite(STDERR, $usageMessage);
	
/*

This function takes an object,
scans it at all levels to produce (i.e. echo to stdout), for each primitive submember, 
a line of output.  (Perhaps it should also/alternatively return a string containing those lines of text, but that is a bit more complicated because we would have to use static variables in the function to accumulate the string (this function will work by recursion)).
Example 1:

$x = new stdClass;
$x->a = 13;
$x->b = 127;
$x->c = -34.25;

Then, 
	objectToSldWorksEquationSyntax($x, "foo")
echoes the following lines of text:
"foo.a" = 13
"foo.b" = 127
"foo.c" = -34.25


Example 2:

$y = new stdClass;
$y->alabama = 78;
$y->arkansas = 15;
$y->ohio = 88;

$x = new stdClass;
$x->a = 13;
$x->b = 127;
$x->c = -34.25;
$x->bar = $y;

Then, 
	objectToSldWorksEquationSyntax($x, "foo")
evalulates to the string containing the following lines of text:
"foo.a" = 13
"foo.b" = 127
"foo.c" = -34.25
"foo.bar.alabama" = 78
"foo.bar.arkansas" = 15
"foo.bar.ohio" = 88

//Slight change of plans... we will do this with associative arrays, not objects.


*/


function toSldWorksEquationSyntax($value, $name="")
{
	global $solidworksFileOutputStream;
	if ( is_numeric($value) )
	{
		fwrite($solidworksFileOutputStream, "\"$name\" = " . number_format($value, 15,".","") . "\n");
	}
	elseif( is_string($value) ) 
	{
		fwrite($solidworksFileOutputStream, "\"$name\" = \"". $value ."\"\n");
	}
	elseif( is_array($value) )
	{
		$prefix = ($name === "" ? "" : $name . "."); //taking an empty string as a default name allows us to use this function to export an array of globals, without any prefix.
		$keys = array_keys($value);
		sort($keys);
		//fwrite(STDERR, "found within $name : \n" . print_r($keys,true). "\n");
		foreach($keys as $key)
		{
			toSldWorksEquationSyntax($value[$key], $prefix . $key);
		}
	}
	elseif( is_object($value) )
	{
		toSldWorksEquationSyntax(object_to_array($value), $name);
	}
}


function updateClassInstances($value, $name="")
{
	global $prototypeModels, $classInstancesPath, $classInstances ;
	if( is_array($value) )
	{
		$prefix = ($name === "" ? "" : $name . "."); //taking an empty string as a default name allows us to use this function to export an array of globals, without any prefix.
		$keys = array_keys($value);
		sort($keys);
		//fwrite(STDERR, "found within $name : \n" . print_r($keys,true). "\n");
		foreach($keys as $key)
		{
			updateClassInstances($value[$key], $prefix . $key);
		}
	}
	elseif( is_object($value) )
	{
		$className = get_class($value);
		
		if (!array_key_exists($className, $classInstances)) // inititalize the array element if we have not yet come across any instances of this class.
		{
			$classInstances[$className] = []; 
		}
		$classInstances[$className][] =  $name;
		if(array_key_exists($className, $prototypeModels))
		{	
			$prototypeFile = $prototypeModels[$className];
			$source = $prototypeFile;
			$destination = $classInstancesPath . DIRECTORY_SEPARATOR . $name . "." . pathinfo($prototypeFile, PATHINFO_EXTENSION);
			echo "now updating instance $name of class $className." . "\n";
			echo "\tnow copying $source to $destination\n";
			mkdir(dirname($destination), 0777, true); //make the destination directory if it does not already exist.
			copy($source, $destination);
		}
		updateClassInstances(object_to_array($value,false), $name);
	}
}

//copied from http://stackoverflow.com/questions/4345554/convert-php-object-to-associative-array :
function object_to_array($data,$recursive=true)
{
    if (is_array($data) || is_object($data))
    {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = ($recursive ? object_to_array($value) : $value);
        }
        return $result;
    }
	
	if(is_numeric($data) && is_nan($data)){return "NAN";}else{ return $data;}; //THIS IS A VERY HACKY WAY TO DEAL WITh the json exporting balking when encountering a NAN. (not a number) (e.g. sqrt(-1) returns NAN).
	

}

?>