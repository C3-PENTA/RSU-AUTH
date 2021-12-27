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
