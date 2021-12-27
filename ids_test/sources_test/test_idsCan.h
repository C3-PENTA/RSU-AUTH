#ifndef IDSCAN_H
#define IDSCAN_H

#include "test_canCom1.h"

/*CAN DEFINE*/
#define MASTER

#if defined(MASTER)
    #define TX_MAILBOX  (1UL)
    #define TX_MSG_ID   (1UL)
    #define RX_MAILBOX  (0UL)
    #define RX_MSG_ID   (2UL)
#elif defined(SLAVE)
    #define TX_MAILBOX  (0UL)
    #define TX_MSG_ID   (2UL)
    #define RX_MAILBOX  (1UL)
    #define RX_MSG_ID   (1UL)
#endif

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