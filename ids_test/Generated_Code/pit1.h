#ifndef pit1_H
#define pit1_H

/* MODULE pit1.
 *
 * @page misra_violations MISRA-C:2012 violations
 *
 * @section [global]
 * Violates MISRA 2012 Advisory Rule 2.5, Global macro not referenced.
 * The global macro will be used in function call of the module.
 */

/* Include inherited beans */
#include "clockMan1.h"
#include "Cpu.h"
#include "pit_driver.h"

/*! Device instance number */
#define INST_PIT1 (0U)

/*! Global configuration of pit1 */
extern const pit_config_t  pit1_InitConfig;
/*! User channel configuration 0 */
extern pit_channel_config_t pit1_ChnConfig0;

#endif