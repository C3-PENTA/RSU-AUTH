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

    flexcan_data_info_t RXdataInfo64 =
    {
		.data_length = 64U,
		.msg_id_type = FLEXCAN_MSG_ID_STD,
		.enable_brs  = true,
		.fd_enable   = true,
		.fd_padding  = 0U
    };

    flexcan_data_info_t TXdataInfo =
    {
	   .data_length = 8U,
	   .msg_id_type = FLEXCAN_MSG_ID_STD,
	   .enable_brs  = true,
	   .fd_enable   = true,
	   .fd_padding  = 0U
    };

    flexcan_data_info_t TXdataInfo64 =
    {
	   .data_length = 64U,
	   .msg_id_type = FLEXCAN_MSG_ID_STD,
	   .enable_brs  = true,
	   .fd_enable   = true,
	   .fd_padding  = 0U
    };

    uint8_t Tx_Buffer[64] = {0, };
    uint16_t CANtimeout = 0;
    void idsSendCanData(uint8_t inst, uint32_t mailbox, uint32_t messageId, uint8_t * data, uint32_t len)

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

void idsInitCan(void)
{
    unit8_t TxNumber=0;

    /*
     * Initialize FlexCAN driver
     *  - 8,64 byte payload size
     *  - FD enabled
     *  - Bus clock as peripheral engine clock
     */
     FLEXCAN_DRV_Init(INST_CANCOM1, &canCom1_State, &canCom1_InitConfig0);

    for(TxNumber=0; TxNumber<64; TxNumber++)
    {
 	   Tx_Buffer[TxNumber] = 0;
    }
}

struct idsCanMsgBuf recvBuff[8];

status_t recvStatusReady;
status_t recvStatus;

struct idsCanMsgBuf *idsCanRecv(void)
{
    struct idsCanMsgBuf *retMsgBuf;
	uint8_t idx=0;

 	for (idx = 0; idx <= INST_CANCOM8; idx++)
	{
		if (recvBuff[idx].recvReq == CAN_MSG_RECV_REQ_DISABLE)
		{
			recvStatusReady = FLEXCAN_DRV_Receive(idx, RX_MAILBOX, &(recvBuff[idx].msgBuf));
			recvBuff[idx].recvReq = CAN_MSG_RECV_REQ_ENABLE;
		}
		retMsgBuf = &recvBuff[idx];
		recvBuff[idx].inst = idx;
		if ((recvStatus = FLEXCAN_DRV_GetTransferStatus(idx, RX_MAILBOX)) != STATUS_BUSY)
		{
			recvBuff[idx].recvReq = CAN_MSG_RECV_REQ_DISABLE;
			return retMsgBuf;
		}
		forLoopCount++;
	}
	return NULL;
   
}