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
#include <wiring_private.h> //I am including this in order to have the sbi and cbi macros.

#include "SmoothedAnalogInput.h"

// input pins
const int doImpulse_button_pin = 2;
const int pulseDuration_pin=A0;
const int throttle_pin=A1;


//output pins
//driving this pin high turns on the heating element 
const int heater_pin=3;

unsigned long remainingNumberOfPwmCyclesInTheCurrentPulse;
bool heaterIsOn;

const int smoothingBufferSize = 6;
const int numDisplayLines=8;
String displayLines[numDisplayLines];
volatile unsigned long pulseCounter;
volatile unsigned long lastPulseTime;
float lastPulseDuration;
float lastPulseThrottle;
float throttle;
float pulseDuration;

unsigned long currentTime; //every pass through the loop, we update this to millis();


SmoothedAnalogInput throttleReading =
	SmoothedAnalogInput(
		throttle_pin,	//pinNumber
		smoothingBufferSize				//bufferSize
	);
SmoothedAnalogInput pulseDurationReading =
	SmoothedAnalogInput(
		pulseDuration_pin,	//pinNumber
		smoothingBufferSize 				//bufferSize
	);

Button doImpulse_button =
	Button(
		doImpulse_button_pin,							// buttonPin
		BUTTON_PULLUP_INTERNAL,		// buttonMode
		true,						// debounceMode
		20							// debounceDuration (milliseconds)
	);



#define MICROSECONDS_PER_TIMER2_OVERFLOW (clockCyclesToMicroseconds(64 * (2*255+1)))
//the above definition assumes assumes blindly that the default Arduino timer configuration is used.  I really ought to check this dynamically.

const float pwmPeriod = MICROSECONDS_PER_TIMER2_OVERFLOW/1000000.0;
//this is the period of the pwm signal that drives the heater.  We will use this to compute the number of pwm cycles
//const float pwmPeriod = 0.001;







// Arduino pin name: "D3"
// AVR function: OC2B (output compare B for timer 2)
// interrupt sources: 
//	OCF2B (compare match) (the same pin is also used for external interrupt 1, but I am only concerned here with the timer-related interrupt) ) (I think we will see one of these events on the way up and another on the way down, when we are in phase-correct PWM mode, which I think that the ARduino uses.
//	TOV2 (overflow)

/*
	For starting and stopping the pulse train, there are a few options:
	1) poll the current time in the main loop.  REJECTED -- this is way too slow, particularly when the main loop is handling updating the display, 
	which takes a long time (100 milliseconds period, for instance).

	2) Use two timers: one to generate the PWM waveform, and another to start and stop the pulse train.  
	The signal from the slower timer to the PWM waveform generation timer could take the form of a 
	hardware signal (routed through external circuitry), or an interrupt.  REJECTED -- external 
	hardware is too much of a pain, and this scheme dedicates one whole timer just to timing the
	duration of the pulse train, which is then not available for other uses.  If there were a 
	built-in (i.e. on-chip) scheme for having one timer enable/disable a second timer, or at least
	the output of a second timer, entirely in hardware, then I might consider it, because it would
	reduce the dependence on software, but because such a built-in scheme does not seem to exist, 
	I have rejected it.

	3) Add some statements to the timer0 overflow interrupt routine (this is the timer that provides the 
	Arduino millis() and micros() functions), to start and stop the pules train at the appropriate times. 
	 REJECTED -- I would rather leave the Arduino library unadulterated.
	4) Leave the settings of timer1 and timer2 set to their Arduino defaults (i.e. overflows will 
	occur at a frequency of about 1 kHz (assuming a 16 MHz clock) in fast-PWM mode, or about 500 Hz 
	in phase-correct PWM mode.), and attach an interrupt routine to either the overflow or the compare
	 match interrupt to handle the starting and stopping of the pulse train.  This is acceptable.
	Does the overflow interrupt get fired when the timer is running in phase-correct PWM mode 
	(i.e. counting up and then down) ?


*/




/*
throttle is a real number in the range [0,1], that sets the duty cycle of the pwm signal.
*/

#define ENABLE_THE_PULSE_TIMING_INTERRUPT()		(sbi(TIMSK2, TOIE2))
#define DISABLE_THE_PULSE_TIMING_INTERRUPT()	(cbi(TIMSK2, TOIE2))

void refreshDisplay();
void doImpulse_button_onPress(Button&);
void doImpulse_button_ISR();
void fireAnImpulse(float pulseDuration, float throttle);
void turnOnHeater();
void turnOffHeater();
String throttleToString(float);
String durationToString(float);


void fireAnImpulse(float pulseDuration, float throttle)
{
	//if(heaterIsOn)
	//{
		////in this case, we are already performing an impulse, which means that something unusual has happened (two presses of the pulse button in rapid succession perhaps).  In this case, we 
		//// will turn off the heater, just to be on the safe side.
		//turnOffHeater();
		//remainingNumberOfPwmCyclesInTheCurrentPulse=0;
		//DISABLE_THE_PULSE_TIMING_INTERRUPT();
	//} else
	{

		//set the duty cycle for the pwm signal according to throttle.
		OCR2B = throttle * 255;

		//compute numberOfPwmCycles
		unsigned long numberOfPwmCycles;
		numberOfPwmCycles = pulseDuration/pwmPeriod;  //should we round up or down in a consistent way?
		remainingNumberOfPwmCyclesInTheCurrentPulse += numberOfPwmCycles;
		ENABLE_THE_PULSE_TIMING_INTERRUPT();
	}
}

// this is the interrupt routine that is in charge of starting and stopping the impulse.
ISR(TIMER2_OVF_vect) //void pulseTimingInterruptRoutine()
{
	if(!heaterIsOn)
	{
		turnOnHeater();
	} else 
	{
		if(remainingNumberOfPwmCyclesInTheCurrentPulse == 0)
		{
			turnOffHeater();
			DISABLE_THE_PULSE_TIMING_INTERRUPT();  //I hope that it's ok to turn off the interrupt enable bit within the interrupt routine.
		} 	else	
		{
			remainingNumberOfPwmCyclesInTheCurrentPulse--;
		}
	}
}

void turnOnHeater()
{
	sbi(TCCR2A, COM2B1); //tell the timer to seize control of the output pin
	pinMode(heater_pin, OUTPUT); //set the ouput pin direction to OUTPUT.
	heaterIsOn = true;
}

void turnOffHeater()
{
	digitalWrite(heater_pin, LOW);
	pinMode(heater_pin, INPUT);
	heaterIsOn = false;
}


void setup()
{
  Serial.begin(115200);
  throttleReading.init();
  pulseDurationReading.init();

  pinMode(heater_pin,OUTPUT);
  doImpulse_button.pressHandler(doImpulse_button_onPress);

  attachInterrupt(digitalPinToInterrupt(doImpulse_button.pin), doImpulse_button_ISR, CHANGE);

  DISABLE_THE_PULSE_TIMING_INTERRUPT(); //probably not strictly necessary here, just want to be sure that the impulse is off.
  turnOffHeater();
  
  u8g.begin();
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
	//Debugger.update();

	throttleReading.update();
	pulseDurationReading.update();

	//TODO: document and standardize the mapping from ADC reading to parameter values.  For now, I am simply throwing in a couple of likely literal values, but this does not make for very clean, intelligible code.
	throttle = round(throttleReading.value())/1023.0;
	pulseDuration = round(pulseDurationReading.value())/1000.0;



    int lineIndex = 0;
	//displayLines[lineIndex++] = String("currentMillis: ") + currentMillis;
	//displayLines[lineIndex++] = String("currentMicros: ") + currentMicros;
	//displayLines[lineIndex++] = String("deltaMillis: ") + deltaMillis;
	//displayLines[lineIndex++] = String("deltaMicros: ") + deltaMicros;
	
	//displayLines[lineIndex++] = String("throttle: ") + throttleToString(throttle);
	//displayLines[lineIndex++] = String("pulseDuration: ") + durationToString(pulseDuration);

	//displayLines[lineIndex++] = String("pulseDuration: ") + pulseDurationReading.value() + "-> " + pulseDuration;


	//displayLines[lineIndex++] = String("lastPulseTime: ") + lastPulseTime;
	//displayLines[lineIndex++] = String("lastPulseDuration: ") + durationToString(lastPulseDuration);
	//displayLines[lineIndex++] = String("lastPulseThrottle: ") + throttleToString(lastPulseThrottle);

	



	if(heaterIsOn)
	{
		float timeRemainingInCurrentPulse = remainingNumberOfPwmCyclesInTheCurrentPulse * pwmPeriod;
		displayLines[lineIndex++] = String("heater is ON. ") + durationToString(timeRemainingInCurrentPulse) + " to go";
	} else
	{
		displayLines[lineIndex++] = "heater is OFF.";
	}
	displayLines[lineIndex++] = String("pulseCounter: ") + pulseCounter;
	displayLines[lineIndex++] = "last pulse: " + durationToString(lastPulseDuration) +  " at "  +  throttleToString(lastPulseThrottle);
	displayLines[lineIndex++] = "next pulse: " + durationToString(pulseDuration)     +  " at "  +  throttleToString(throttle);
		
	refreshDisplay();

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


void doImpulse_button_onPress(Button& b)
{

	fireAnImpulse(pulseDuration, throttle);

	//update the statistics about the last pulse, to be presented to the user by the display functions.
	lastPulseDuration = pulseDuration;
	lastPulseThrottle = throttle;
	lastPulseTime = millis();
	pulseCounter++;
}

void doImpulse_button_ISR()
{
	doImpulse_button.process();
}




String throttleToString(float throttle)
{
	return String(throttle*100,1) + "%";
}

String durationToString(float pulseDuration)
{
	return String(pulseDuration * 1000, 1) + "ms";
};