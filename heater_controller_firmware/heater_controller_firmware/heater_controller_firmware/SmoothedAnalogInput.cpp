#include <Arduino.h>
#include "SmoothedAnalogInput.h"



SmoothedAnalogInput::SmoothedAnalogInput(uint8_t pinNumber, uint32_t bufferSize) : 	dataSet(bufferSize)
{
	this->pinNumber=pinNumber;
	this->bufferSize=bufferSize;
}

void SmoothedAnalogInput::init()
{
	pinMode(pinNumber, INPUT);
	for(int i=0;i<bufferSize;i++)
	{
		process();
	}
}

float SmoothedAnalogInput::value()
{
	return dataSet.mean();
}

void SmoothedAnalogInput::process()
{
	dataSet.push(analogRead(pinNumber));
}

float SmoothedAnalogInput::rolling()
{
	process();
	return value();
}