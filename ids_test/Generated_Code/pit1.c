#include "pit1.h"

const pit_config_t pit1_InitConfig =
{
    .enableStandardTimers = true,
    .enableRTITimer = true,
    .stopRunInDebug = true
};

pit_channel_config_t pit1_ChnConfig0 =
{
    .hwChannel = 0U,
    .periodUnit = PIT_PERIOD_UNITS_MICROSECONDS,
    .period = 500000U,
    .enableChain = false,
    .enableInterrupt = true
};