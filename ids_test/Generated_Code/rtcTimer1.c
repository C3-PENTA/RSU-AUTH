#include "rtcTimer1.h"

rtc_state_t rtcTimer1_State;

/*! rtcTimer1 configuration structure */
rtc_init_config_t rtcTimer1_Config0 = 
{
    .clockSelect                =   RTC_CLOCK_SOURCE_FIRC, 
    .divideBy32                 =   false,
    .divideBy512                =   false,
    .freezeEnable               =   false,
    .nonSupervisorAccessEnable  =   true
};