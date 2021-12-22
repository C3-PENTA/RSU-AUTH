#ifndef canCom1_H
#define canCom1_H

/* Include inherited beans */
#include "Cpu.h"

/*! @brief Device instance number */
#define INST_CANCOM1 (0U)
/*! @brief Driver state structure which holds driver runtime data */
extern flexcan_state_t canCom1_State; 
extern const flexcan_user_config_t canCom1_InitConfig0;

#endif