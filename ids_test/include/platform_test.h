#if !defined(IDS_PLATFORM_COMMON_H)
#define IDS_PLATFORM_COMMON_H

#include "cstdint"
/* IO definitions (access restrictions to peripheral registers) */

typedef struct {
	__IO uint16_t NUM;          /*NUM OF RECEIVED MSG*/
} TEST_Type;