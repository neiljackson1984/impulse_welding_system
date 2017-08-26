/*Begining of Auto generated code by Atmel studio */
#include <Arduino.h>

/*End of auto generated code by Atmel studio */

#include <U8glib.h>
U8GLIB_SSD1306_128X64 u8g(U8G_I2C_OPT_DEV_0|U8G_I2C_OPT_NO_ACK|U8G_I2C_OPT_FAST);	// Fast I2C / TWI 
#include <Streaming.h>
#include <PID_v1.h>
#include <Average.h>
#include <SerialCommand.h>
#include <avr/eeprom.h>
#include <string.h>
#include "debugger.h"
//Beginning of Auto generated function prototypes by Atmel Studio
float getSmoothedADCReading(int pinNumber);
boolean isDigit(char x);
boolean isWholeNumber(char x);
//End of Auto generated function prototypes by Atmel Studio


 
const int heater_pin=3; //driving this pin high turns on the heating element
const int throttle_pin=A0;
const int pulseDuration_pin=A1;

float throttleReading;
float pulseDurationReading;



const int numDisplayLines=6;
const int displayLineLength=30;
char displayLines[numDisplayLines][displayLineLength];



unsigned long currentTime=0;              //time in milliseconds //to be set at the beginning of each loop.
void refreshDisplay();

#define factoryDefaultAddress '1'
#define factoryDefaultName "un-named"


void setup()
{
  Serial.begin(115200);

  pinMode(heater_pin,OUTPUT);

  u8g.begin();
  //u8g.setRot180();
}

void loop()
{
	currentTime=millis();
	Debugger.update();

	throttleReading = getSmoothedADCReading(throttle_pin);
	pulseDurationReading = getSmoothedADCReading(pulseDuration_pin);

	char floatBuffer1[7];  //a little buffer to store the results of converting floating point numbers into strings
    char floatBuffer2[7];
    snprintf(displayLines[0],displayLineLength,"throttleReading: %s",dtostrf(throttleReading,6,1,floatBuffer1));
    snprintf(displayLines[1],displayLineLength,"pulseDurationReading: %s",dtostrf(pulseDurationReading,6,1,floatBuffer1));
	
	refreshDisplay();
}


void refreshDisplay()
{

   int printPositionX, printPositionY; // used to keep track of current print position (for doing newlines)
   u8g.setColorIndex(1);
   u8g.firstPage();  
    do {
    
	// u8g.drawBox(0,34,128,1); //horizontal line
	// u8g.drawBox(63,0,1,34); //upper vertical line
	// u8g.drawBox(47,35,1,29); //lower vertical line
	
	printPositionX=0; printPositionY=0;
	u8g.setFont(u8g_font_babyr); u8g.setPrintPos(printPositionX, printPositionY += u8g.getFontLineSpacing());
	for(int lineNumber=1;lineNumber<=numDisplayLines;lineNumber++)
	{
		u8g.setPrintPos(0,u8g.getFontLineSpacing()*lineNumber);
		u8g.print(displayLines[lineNumber-1]);
	}
  } while( u8g.nextPage() );
}

float getSmoothedADCReading(int pinNumber)
{
	  const static int numSamples=100;
	  Average<float> dataSet(numSamples); //'might save run time by declaring this as static (I'm not sure how much the constructor does)
	  for(int i=0;i<numSamples;i++)
	  {
		dataSet.push(analogRead(pinNumber));
	  }
	  return dataSet.mean();
}

boolean isDigit(char x)
{
 return  '0' <= x && x<= '9';
}

boolean isWholeNumber(char* x)
{
 boolean returnValue = true;
 while(*x)
 {
   if(!isDigit(*x))
   {
     returnValue=false;
     break;
   }
   x++;
 }
 return returnValue;
}
