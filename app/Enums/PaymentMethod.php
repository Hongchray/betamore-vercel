<?php
enum PaymentMethod: string
{
    case ABA_PAYWAY = 'abapay_khqr';
    case CREDIT_CARD = 'cards';
    case CASH_ON_DELIVERY = 'cash_on_delivery';
}
