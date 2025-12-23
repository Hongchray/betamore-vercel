<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'PENDING';
    case Approved = 'APPROVED';
    case Declined = 'DECLINDED';
    case Refunded = 'REFUNDED';
    case Cancelled = ' CANCELLED';
}
