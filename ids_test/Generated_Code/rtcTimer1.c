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

rtc_timedate_t rtcTimer1_StartTime0 =
{
    .year       =   2016U,
    .month      =   1U,
    .day        =   1U,
    .hour       =   0U,
    .minutes    =   0U,
    .seconds    =   0U
};

rtc_alarm_config_t rtcTimer1_AlarmConfig0 = 
{

    .alarmTime  =
    {
        .year       =   2016U,
        .month      =   1U,
        .day        =   1U,
        .hour       =   0U,
        .minutes    =   0U,
        .seconds    =   0U
    },

    .repetitionInterval  =       0UL,
    .numberOfRepeats     =       0UL,
    .repeatForever       =       false,
    .alarmIntEnable      =      false,
    .alarmCallback       =     NULL,   
    .callbackParams      =     NULL
};