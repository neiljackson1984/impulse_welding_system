#include <Arduino.h>
#include <Streaming.h>
#include <stdint.h>

extern unsigned long currentTime;

class debugger_t
{
	public:
		enum typeDesignator {long_TYPE, ulong_TYPE,  float_TYPE, charp_TYPE, uint8_TYPE, double_TYPE, char_TYPE} ;
		typeDesignator getType(long){return long_TYPE;};
		typeDesignator getType(unsigned long){return ulong_TYPE;};
		typeDesignator getType(float){return float_TYPE;};
		typeDesignator getType(char*){return charp_TYPE;};
		typeDesignator getType(uint8_t){return uint8_TYPE;};
        typeDesignator getType(double){return double_TYPE;};
		typeDesignator getType(char){return char_TYPE;};
		
		int numberOfWatchedItems;
		unsigned long timeOfLastDisplayRefresh;
		unsigned long timeOfLastFormRedraw;
		unsigned long displayRefreshInterval;
		unsigned long formRedrawInterval;
		static const int captionMaxLength=23;
		boolean formNeedsToBeRedrawn;

		
		void* watchedItems[25];
		typeDesignator typesOfWatchedItems[25];
		char* captions[25];
		
		void watch( long            &x, char* caption)
		{
			int currentItem=numberOfWatchedItems;
			numberOfWatchedItems++;
			watchedItems[currentItem] = (void*) &x;
			typesOfWatchedItems[currentItem] = getType(x);
			captions[currentItem] = caption;
			formNeedsToBeRedrawn = true; 
		};	
		void watch( unsigned long   &x, char* caption)
		{
			int currentItem=numberOfWatchedItems;
			numberOfWatchedItems++;	
			watchedItems[currentItem] = (void*) &x;
			typesOfWatchedItems[currentItem] = getType(x);
			captions[currentItem] = caption;
			formNeedsToBeRedrawn = true; 
		};	
		void watch( float           &x, char* caption)
		{
			int currentItem=numberOfWatchedItems;
			numberOfWatchedItems++;
			watchedItems[currentItem] = (void*) &x;
			typesOfWatchedItems[currentItem] = getType(x);
			captions[currentItem] = caption;
			formNeedsToBeRedrawn = true; 
		};		
		void watch( char*           &x, char* caption)
		{
			int currentItem=numberOfWatchedItems;
			numberOfWatchedItems++;
			watchedItems[currentItem] = (void*) &x;
			typesOfWatchedItems[currentItem] = getType(x);
			captions[currentItem] = caption;
			formNeedsToBeRedrawn = true; 
		};
		void watch( uint8_t         &x, char* caption)
		{
			int currentItem=numberOfWatchedItems;
			numberOfWatchedItems++;
			watchedItems[currentItem] = (void*) &x;
			typesOfWatchedItems[currentItem] = getType(x);
			captions[currentItem] = caption;
			formNeedsToBeRedrawn = true;
		};
		void watch( double         &x, char* caption)
		{
			int currentItem=numberOfWatchedItems;
			numberOfWatchedItems++;
			watchedItems[currentItem] = (void*) &x;
			typesOfWatchedItems[currentItem] = getType(x);
			captions[currentItem] = caption;
			formNeedsToBeRedrawn = true;
		};
		void watch( char         &x, char* caption)
		{
			int currentItem=numberOfWatchedItems;
			numberOfWatchedItems++;
			watchedItems[currentItem] = (void*) &x;
			typesOfWatchedItems[currentItem] = getType(x);
			captions[currentItem] = caption;
			formNeedsToBeRedrawn = true;
		};
		
		void drawForm()
		{
			Serial << "\033[?25l\033[H\033[J\033[H";
			for(int i=0; i<numberOfWatchedItems; i++ )
			{
				//for(unsigned int j=0; j< captionMaxLength - strlen(captions[i]); j++){Serial << " ";} 
				Serial << "\033" << "[" << (char) i+1 << ";" << (char) captionMaxLength - strlen(captions[i]) << "H" << captions[i] << " = ";
			}	
			//Serial << "\n\r\n\rMORE CRAP";	 
			timeOfLastFormRedraw = currentTime;
			formNeedsToBeRedrawn = false;
		};
		
		void refreshDisplay()
		{
			for(int i=0; i<numberOfWatchedItems; i++ )
			{
				Serial << "\033" << "[" << (char) i+1 << ";" << (char) captionMaxLength+3 << "H";
				switch(typesOfWatchedItems[i])
				{
					case long_TYPE: Serial << *((long*) watchedItems[i]); break;
					case ulong_TYPE: Serial << *((unsigned long*) watchedItems[i]); break;
					case float_TYPE: Serial << *((float*) watchedItems[i]); break;
					case charp_TYPE: Serial << *((char**) watchedItems[i]); break;
					case uint8_TYPE: Serial << *((uint8_t*) watchedItems[i]); break;
                    case double_TYPE: Serial << *((double*) watchedItems[i]); break;
					case char_TYPE: Serial << *((char*) watchedItems[i]); break;
					default: break;
				}
                                Serial << "      ";
			}
			timeOfLastDisplayRefresh = currentTime;			
			
		};
		
		void update()
		{
			 if (currentTime - timeOfLastDisplayRefresh >= displayRefreshInterval)
			 {
				 if (currentTime - timeOfLastFormRedraw >= formRedrawInterval){formNeedsToBeRedrawn = true;};
				 if (formNeedsToBeRedrawn){drawForm();};
				 refreshDisplay(); 
			 }
		};
				
		debugger_t()
		{
			numberOfWatchedItems = 0;
			timeOfLastDisplayRefresh = currentTime;
			displayRefreshInterval = 250;
			formRedrawInterval = 5000;
			formNeedsToBeRedrawn = true;
		};
};


debugger_t Debugger;
#define DEBUGGER_WATCH(x) Debugger.watch(x, #x);
