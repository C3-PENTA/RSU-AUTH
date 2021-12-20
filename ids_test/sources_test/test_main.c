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
}