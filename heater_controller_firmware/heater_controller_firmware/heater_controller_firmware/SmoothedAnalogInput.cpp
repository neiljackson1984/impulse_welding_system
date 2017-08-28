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
		update();
	}
}

float SmoothedAnalogInput::value()
{
	return dataSet.mean();
}

void SmoothedAnalogInput::update(int numTimes)
{
	for(int i = 0; i<numTimes; i++ )
	{
		dataSet.push(analogRead(pinNumber));
	}
}
void SmoothedAnalogInput::update()
{
	update(1);
}

float SmoothedAnalogInput::rolling(int numTimes)
{
	update(numTimes);
	return value();
}

float SmoothedAnalogInput::rolling()
{
	return rolling(1);
}