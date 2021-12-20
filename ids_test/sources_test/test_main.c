/* MODULE main */
/* Including needed modules to compile this module/procedure */

#include "platform_common.h"

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
}