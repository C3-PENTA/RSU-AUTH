#if !defined(IDS_PLATFORM_COMMON_H)
#define IDS_PLATFORM_COMMON_H

#include "cstdint"
/* IO definitions (access restrictions to peripheral registers) */

typedef struct {
	__IO uint16_t NUM;          /*NUM OF RECEIVED MSG*/
} TEST_Type;

typedef struct {
  __IO uint16_t in;		  /*Ring BUFFER HEADER*/
  __IO uint16_t out;	  /*RING BUFFER TAIL*/
} HidsIpcHdr_Type;

typedef struct {
   __IO uint16_t mark;    /*DEFAULT 0x5A5A*/
   __IO uint8_t  bus;     /* BUS NUM, BCAN-0x00, CCAN-0x01, ICAN-0x03, MMCAN-0x04, PCAN-0x05*/
   __IO uint8_t  dlc;     /*DATA LENGHT*/
   __IO uint32_t mid;     /*ARBITRATION ID*/
   __IO uint32_t sec;     /*TIME(second)*/
   __IO uint32_t usec;    /*TIME(microsecond)*/
   __IO uint8_t  data[8]; /*MSG*/
} CanMsgInfo_Type;

typedef struct {
  __IO uint16_t HEAD; /*DEFAULT 0x5A5A*/
  __IO uint16_t TAIL;   /* BUS NUM, BCAN-0x00, CCAN-0x01, ICAN-0x03, MMCAN-0x04, PCAN-0x05*/
} OVER_Type;