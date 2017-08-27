/*Begining of Auto generated code by Atmel studio */
#include <Arduino.h>

/*End of auto generated code by Atmel studio */

#include <U8glib.h>
U8GLIB_SSD1306_128X64 u8g(U8G_I2C_OPT_DEV_0|U8G_I2C_OPT_NO_ACK|U8G_I2C_OPT_FAST);	// Fast I2C / TWI 
#include <Streaming.h>
#include <PID_v1.h>
#include <SerialCommand.h>
#include <avr/eeprom.h>
#include <string.h>
#include <debugger.h>
#include <Button.h>
#include "SmoothedAnalogInput.h"

//Beginning of Auto generated function prototypes by Atmel Studio
float getSmoothedADCReading(int pinNumber);
boolean isDigit(char x);
boolean isWholeNumber(char x);
//End of Auto generated function prototypes by Atmel Studio


 
const int heater_pin=3; //driving this pin high turns on the heating element
const int throttle_pin=A0;
const int pulseDuration_pin=A1;

//float throttleReading;
//float pulseDurationReading;

SmoothedAnalogInput throttleReading = 
	SmoothedAnalogInput(
		throttle_pin,	//pinNumber
		15				//bufferSize
	);

SmoothedAnalogInput pulseDurationReading =
	SmoothedAnalogInput(
		pulseDuration_pin,	//pinNumber
		15 				//bufferSize
	);



const int numDisplayLines=8;
//const int displayLineLength=31;
//char displayLines[numDisplayLines][displayLineLength];
String displayLines[numDisplayLines];



unsigned long currentTime=0;              //time in milliseconds //to be set at the beginning of each loop.
void refreshDisplay();

const int doImpulse_button_pin = 2;

Button doImpulse_button = 
	Button(
		doImpulse_button_pin,							// buttonPin
		BUTTON_PULLUP_INTERNAL,		// buttonMode
		true,						// debounceMode
		20							// debounceDuration
	);


void doImpulse();

void doImpulse_onPress(Button& b)
{
	doImpulse();
}

volatile unsigned long pulseCounter;
volatile unsigned long lastPulseTime;


void doImpulse()
{

	lastPulseTime = millis();
	pulseCounter++;
}

void doImpulse_button_ISR()
{
	doImpulse_button.process();
}

void setup()
{
  Serial.begin(115200);
  throttleReading.init();
  pulseDurationReading.init();

  pinMode(heater_pin,OUTPUT);
  doImpulse_button.pressHandler(doImpulse_onPress);

  	
  attachInterrupt(digitalPinToInterrupt(doImpulse_button.pin), doImpulse_button_ISR, CHANGE);

  u8g.begin();
  //u8g.setRot180();
}

void loop()
{
	static unsigned long lastMillis, currentMillis, deltaMillis;
	static unsigned long lastMicros, currentMicros, deltaMicros;
	
	currentMillis = millis();
	currentMicros = micros();

	deltaMillis = currentMillis - lastMillis;
	deltaMicros = currentMicros - lastMicros;
	currentTime=millis();
	Debugger.update();

    
	displayLines[0] = String("currentMillis: ") + currentMillis;
	displayLines[1] = String("currentMicros: ") + currentMicros;
	displayLines[2] = String("deltaMillis: ") + deltaMillis;
	displayLines[3] = String("deltaMicros: ") + deltaMicros;
	displayLines[4] = String("throttleReading: ") + throttleReading.rolling();
	displayLines[5] = String("pulseDurationReading: ") + pulseDurationReading.rolling();
	displayLines[6] = String("pulseCounter: ") + pulseCounter;
	displayLines[7] = String("lastPulseTime: ") + lastPulseTime;





	//String myString;
	//myString = String(currentMillis);
	//myString.toCharArray(displayLines[0],displayLineLength-1);
    //snprintf(displayLines[0],displayLineLength,"%s", myString.c_str());
    //snprintf(displayLines[0],displayLineLength - 1,"currentMillis: %s", String(currentMillis).c_str());
	//snprintf(displayLines[1],displayLineLength - 1,"currentMicros: %s", String(currentMicros).c_str());
	//snprintf(displayLines[2],displayLineLength - 1,"deltaMillis: %s", String(deltaMillis).c_str());
	//snprintf(displayLines[3],displayLineLength - 1,"deltaMicros: %s", String(deltaMicros).c_str());
	//ultoa(deltaMicros,displayLines[3],10);
	//snprintf(displayLines[4],displayLineLength,"throttleReading: %s",dtostrf(throttleReading.rolling(),6,1,floatBuffer1));
	//snprintf(displayLines[5],displayLineLength,"pulseDurationReading: %s",dtostrf(pulseDurationReading.rolling(),6,1,floatBuffer1));


	refreshDisplay();
	//doImpulse_button.process();

	lastMillis = currentMillis;
	lastMicros = currentMicros;
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
