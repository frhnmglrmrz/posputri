<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Sale = 'SALE';
    case Purchase = 'PURCHASE';
    case ReturnOrder = 'RETURN';
    case Adjustment = 'ADJUSTMENT';
    case TransferIn = 'TRANSFER_IN';
    case TransferOut = 'TRANSFER_OUT';
}
