#ifndef SmoothedAnalogInput_h
#define SmoothedAnalogInput_h
#include <Average.h>

class SmoothedAnalogInput {
  public:
  
    SmoothedAnalogInput(uint8_t pinNumber, uint32_t bufferSize);
    uint8_t             pinNumber;
    void update(int numTimes); //reads a new sample 
	float rolling(int numTimes);  //reads a new sample and returns the new mean
	void update(); //reads a new sample
	float rolling();  //reads a new sample and returns the new mean
	float value(); //returns the mean of dataSet.
    void init();

  private: 
	Average<float>		dataSet;
	uint32_t bufferSize;
};

#endif