#include "test_idsCan.h"

uint16_t CANtimeout = 0;
void idsSendCanData(uint8_t inst, uint32_t mailbox, uint32_t messageId, uint8_t * data, uint32_t len)
{
    /* Set information about the data to be sent
     *  - 1 byte in length
     *  - Standard message ID
     *  - Bit rate switch enabled to use a different bitrate for the data segment
     *  - Flexible data rate enabled
     *  - Use zeros for FD padding
     */
    flexcan_data_info_t dataInfo =
    {
            .data_length = len,
            .msg_id_type = FLEXCAN_MSG_ID_STD,
            .enable_brs  = true,
            .fd_enable   = true,
            .fd_padding  = 0U
    };

    flexcan_data_info_t RXdataInfo =
    {
		.data_length = 8U,
		.msg_id_type = FLEXCAN_MSG_ID_STD,
		.enable_brs  = true,
		.fd_enable   = true,
		.fd_padding  = 0U
    };

    /* Execute send non-blocking */
    if(messageId != 0x4c1 || messageId != 0x4c2)
    {
    	while(FLEXCAN_DRV_Send(inst, mailbox, &dataInfo, messageId, data) == STATUS_BUSY && CANtimeout < 1000)
    	{
    		CANtimeout++;
    	}
    }
    else
    {
    	while(FLEXCAN_DRV_Send(inst, mailbox, &dataInfo, messageId, data) == STATUS_BUSY);
    }
    CANtimeout = 0;
}