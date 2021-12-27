#ifndef IDSCAN_H
#define IDSCAN_H

struct idsCanMsgBuf
{
	flexcan_msgbuff_t msgBuf;
	int16_t inst;
	int16_t recvReq;
};

extern uint8_t Tx_Buffer[64];

void CAN_TJA1043T_Enable(void);
void idsInitCan(void);
struct idsCanMsgBuf *idsCanRecv(void);
void idsSendCanData(uint8_t inst, uint32_t mailbox, uint32_t messageId, uint8_t * data, uint32_t len);

#endif