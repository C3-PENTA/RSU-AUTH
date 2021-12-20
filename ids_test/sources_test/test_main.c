/* MODULE main */
/* Including needed modules to compile this module/procedure */

#include "platform_common.h"

/*Semaphore DEFINE*/
#define SEMA42_INSTANCE		0U
#define SEMA42_GATE			0U

int main(void)
{
  /* Write your local variable definition here */

  /*** Processor Expert internal initialization. DON'T REMOVE THIS CODE!!! ***/
  #ifdef PEX_RTOS_INIT
    PEX_RTOS_INIT();     

    BoardInit();

    Peripheral_Power_Supply_Init();
    CAN_TJA1043T_Enable();
    idsInitCan();
    idsInitPit();
    idsInitRtc();

	/*Semaphore initial*/
	SEMA42_DRV_Init(SEMA42_INSTANCE);

    canIpcHdr->in = 0;
	canIpcHdr->out = 0;
	almIpcHdr->in = 0;
	almIpcHdr->out = 0;
	idsorigTv.sec = 0;
	idsorigTv.usec = 0;

    	while(1)
	{
		/*Semaphore Lock Try*/
		SEMA42_DRV_LockGate(SEMA42_INSTANCE, SEMA42_GATE);
		if (SEMA42_DRV_GetGateLocker(SEMA42_INSTANCE, SEMA42_GATE) == (GET_CORE_ID() + 1))
		{
#ifdef USE_CAN_DRIVER
			/*Semaphore Unlock*/
			idsForwardMsgToIDS();
			idsSendMsgFromIDS();
			SEMA42_DRV_UnlockGate(SEMA42_INSTANCE, SEMA42_GATE);
		}
        		core0Count++;
		idsDelay(100);
	}
#endif

#ifdef NOT_USE_CAN_DRIVER
		int16_t Idx = (canIpcHdr->in + 1);
		if(Idx!=canIpcHdr->out)
		{
			RTC_DRV_GetTimeDate(RTCTIMER1, &tempTime);
			RTC_DRV_ConvertTimeDateToSeconds(&tempTime, &tempUSeconds);
			tempSeconds = tempUSeconds/1000000;

			if(core0Count == 0)
			{
				T = tempSeconds;
				UT = tempUSeconds;
			}
			uint16_t Max = CAN_IPC_NUM;
			uint16_t CanIdx = Idx % Max;
			CanMsgInfo* canMsgInfo = (CanMsgInfo*)((uint8_t*)(canIpcHdr + 1) + CanIdx*HIDS_MSG_LEN);
			canMsgInfo->mark = 0x5A5A;
			canMsgInfo->bus = (uint8_t) 1;
			canMsgInfo->dlc = (uint8_t) 8;
			canMsgInfo->mid = (uint32_t) 2;
			canMsgInfo->sec = tempSeconds - T;
			canMsgInfo->usec = tempUSeconds - UT;
			for(int i = 0 ; i < (int)canMsgInfo->dlc ; i++ )
			{
				canMsgInfo->data[i] = (uint8_t) 17;
			}
			canIpcHdr->in = CanIdx;
		}

		/*Semaphore Unlock*/
		SEMA42_DRV_UnlockGate(SEMA42_INSTANCE, SEMA42_GATE);
	   }
       	   core0Count++;
	   idsDelay(10);
	}
#endif

  #ifdef PEX_RTOS_START
    PEX_RTOS_START();                  /* Startup of the selected RTOS. Macro is defined by the RTOS component. */
  #endif

  for(;;) {
    if(exit_code != 0) {
      break;
    }
  }

    return exit_code;
  /*** Processor Expert end of main routine. DON'T WRITE CODE BELOW!!! ***/
}