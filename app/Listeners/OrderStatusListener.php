<?php

namespace App\Listeners;

use App\Events\OrderStatusEvent;
use App\Traits\PushNotificationTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdateMail;

class OrderStatusListener
{
    use PushNotificationTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusEvent $event): void
    {
        $this->sendNotification($event);
        if ($event->type == 'customer') {
            $this->sendMail($event);
        }
    }

    private function sendNotification(OrderStatusEvent $event): void
    {
        $key = $event->key;
        $type = $event->type;
        $order = $event->order;
        $this->sendOrderNotification(key: $key, type: $type, order: $order);
    }

    private function sendMail(OrderStatusEvent $event): void
    {
        $order = $event->order;
        if ($order && $order->customer) {
            $email = $order->customer->email;
            $userName = $order->customer->f_name . ' ' . $order->customer->l_name;
            $status = $event->key;
            $orderId = $order->id;

            try {
                Mail::to($email)->send(new OrderStatusUpdateMail($orderId, $status, $userName));
            } catch (\Exception $e) {
                // Ignore exception if mail fails to avoid blocking the request
            }
        }
    }
}
