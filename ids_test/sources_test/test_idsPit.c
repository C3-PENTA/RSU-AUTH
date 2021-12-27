void PIT_Ch0_IRQHandler(void)
{
    /*
     * action of definition
     */
    PIT_DRV_ClearStatusFlags(INST_PIT1, pit1_ChnConfig0.hwChannel);     /* Clear channel 0 interrupt flag */
}