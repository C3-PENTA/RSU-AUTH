#if !defined(IDS_PLATFORM_COMMON_H)
#define IDS_PLATFORM_COMMON_H

#ifndef __IO
#ifdef __cplusplus
  #define   __I     volatile
#else
  #define   __I     volatile const
#endif
#define     __O     volatile
#define     __IO    volatile
#endif

typedef struct {
	__IO uint16_t NUM;          /*NUM OF RECEIVED MSG*/
} TEST_Type;

#define COUNT_BASE                        (SHARED_MEMORY_START)
#define COUNT							  ((TEST_Type *)COUNT_BASE)

typedef struct {
  __IO uint16_t RM;			  /*RECIEVE*/
  __IO uint16_t TM;		      /*TRANSMIT*/
} CANMODE_Type;

/* Ring Buffer API base address */
#define HIDS_CAN_IPC                      		(CHECK_CANMODE_BASE + CANMODE_SIZE)
#define canIpcHdr							    ((HidsIpcHdr_Type *)HIDS_CAN_IPC)