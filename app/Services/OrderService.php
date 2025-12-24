<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
class OrderService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Update order status and payment status
     *
     * @param Order $order
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function updateOrderStatus(Order $order, array $data): bool
    {
        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $oldPaymentStatus = $order->orderPayment?->payment_status;

            // Update order status
            $order->update([
                'status' => $data['status'],
            ]);

            // Update payment status if exists
            if ($order->orderPayment && isset($data['payment_status'])) {
                $this->updatePaymentStatus($order, $data['payment_status']);
            }

            DB::commit();

            // Send notification ONLY if order status changed (not payment status)
            if ($oldStatus !== $data['status']) {
                $this->sendOrderStatusNotification($order, $data['status'], $oldStatus);
            }

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Order update failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Update payment status
     *
     * @param Order $order
     * @param string $paymentStatus
     * @return void
     */
    private function updatePaymentStatus(Order $order, string $paymentStatus): void
    {
        $updateData = [
            'payment_status' => $paymentStatus,
        ];

        // Set paid_at timestamp when payment is approved
        if ($paymentStatus === PaymentStatus::Approved && !$order->orderPayment->paid_at) {
            $updateData['paid_at'] = now();
        }

        $order->orderPayment->update($updateData);
    }

    /**
     * Send push notification to user about order status update
     *
     * @param Order $order
     * @param string $newStatus
     * @param string $oldStatus
     * @return void
     */
    private function sendOrderStatusNotification(Order $order, string $newStatus, string $oldStatus): void
    {
        $order->load('user');
        $user = $order->user;

        if (!$user) {
            Log::warning('Cannot send notification: User not found', [
                'order_id' => $order->id,
            ]);
            return;
        }

        $title = $this->getNotificationTitle($newStatus);
        $body  = $this->getNotificationBody($order, $newStatus);

        $data = [
            'order_id'     => (string) $order->id,
            'order_number' => $order->order_number ?? '',
            'status'       => $newStatus,
            'old_status'   => $oldStatus,
            'type'         => 'order_status_update',
        ];

        /** -------------------------------
         * 1️⃣ STORE notification FIRST
         * -------------------------------- */
        try {
            // ✅ DON'T pass is_read and is_sent - let DB defaults handle it
            $notification = Notification::create([
                'user_id' => $user->id,
                'type'    => 'order_status_update',
                'title'   => $title,
                'body'    => $body,
                'data'    => $data,
                // Remove these two lines:
                // 'is_read' => false,
                // 'is_sent' => false,
            ]);

            Log::info('Notification stored', [
                'notification_id' => $notification->id,
                'order_id' => $order->id,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store notification', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return;
        }

        /** --------------------------------
         * 2️⃣ SEND PUSH (optional)
         * -------------------------------- */
        if (!$user->fcm_token) {
            Log::warning('User has no FCM token', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
            ]);
            return;
        }

        try {
            $this->notificationService->sendToDevice(
                $user->fcm_token,
                $title,
                $body,
                $data
            );

            /** -----------------------------
             * 3️⃣ MARK AS SENT
             * ------------------------------ */
            $notification->markAsSent();

            Log::info('Push notification sent', [
                'notification_id' => $notification->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }


    /**
     * Get notification title based on status
     *
     * @param string $status
     * @return string
     */
    private function getNotificationTitle(string $status): string
    {
        return match($status) {
            OrderStatus::CONFIRMED => 'Order Confirmed',
            OrderStatus::PROCESSING => 'Order Processing',
            OrderStatus::SHIPPED => 'Order Shipped',
            OrderStatus::DELIVERED => 'Order Delivered',
            OrderStatus::CANCELLED => 'Order Cancelled',
            default => 'Order Updated',
        };
    }

    /**
     * Get notification body based on status
     *
     * @param Order $order
     * @param string $status
     * @return string
     */
    private function getNotificationBody(Order $order, string $status): string
    {
        $orderNumber = $order->order_number ?? $order->id;
        
        return match($status) {
            OrderStatus::CONFIRMED => "Your order #{$orderNumber} has been confirmed.",
            OrderStatus::PROCESSING => "Your order #{$orderNumber} is being processed.",
            OrderStatus::SHIPPED => "Your order #{$orderNumber} has been shipped.",
            OrderStatus::DELIVERED => "Your order #{$orderNumber} has been delivered.",
            OrderStatus::CANCELLED => "Your order #{$orderNumber} has been cancelled.",
            default => "Your order #{$orderNumber} status has been updated to {$status}.",
        };
    }
}