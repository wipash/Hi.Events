@php use Carbon\Carbon; use HiEvents\Helper\Currency; use HiEvents\Helper\DateHelper; @endphp
@php /** @var \HiEvents\DomainObjects\OrderDomainObject $order */ @endphp
@php /** @var \HiEvents\DomainObjects\EventDomainObject $event */ @endphp
@php /** @var \HiEvents\DomainObjects\OrganizerDomainObject $organizer */ @endphp
@php /** @var \HiEvents\DomainObjects\EventSettingDomainObject $eventSettings */ @endphp
@php /** @var string $orderUrl */ @endphp

@php /** @see \HiEvents\Mail\Order\OrderSummary */ @endphp

<x-mail::message>
# {{ __('Your Order is Confirmed! ') }} 🎉

@if($order->isOrderAwaitingOfflinePayment() === false)

<p>
{{ __('Congratulations! Your order for :eventTitle was successful. Please find your order details below.', ['eventTitle' => $event->getTitle() ]) }}
</p>

@else

<div>
<p>
{{ __('Your order is pending payment. Tickets have been issued but will not be valid until payment is received.') }}
</p>

<div style="border-radius: 4px; background-color: #d7e8f8; color: #204e84; margin-bottom: 1.5rem; padding: 1rem;">
<h2>{{ __('Payment Instructions') }}</h2>
{{ __('Please follow the instructions below to complete your payment.') }}
{!! $eventSettings->getOfflinePaymentInstructions() !!}
</div>
</div>

@endif

@if($eventSettings->getPostCheckoutMessage() && $order->isOrderCompleted())
<p>

# {{ __('Additional Information') }}

{!! $eventSettings->getPostCheckoutMessage() !!}

</p>
@endif

# {{ __('Order Summary') }}
- **{{ __('Order Number:') }}** {{ $order->getPublicId() }}
- **{{ __('Total Amount:') }}** {{ Currency::format($order->getTotalGross(), $event->getCurrency()) }}

@if($order->getAttendees() && $order->getAttendees()->count() > 0)
# {{ __('Tickets') }}
@foreach($order->getAttendees() as $attendee)
@php $orderItem = $order->getTicketOrderItems()?->first(fn($item) => $item->getProductPriceId() === $attendee->getProductPriceId()); @endphp
@if($orderItem)
- **{{ $orderItem->getItemName() }}** — {{ $attendee->getFirstName() }} {{ $attendee->getLastName() }}
@endif
@endforeach
@endif

<x-mail::button :url="$orderUrl">
    {{ __('View Order Summary & Tickets') }}
</x-mail::button>

{{ __('If you have any questions or need assistance, please contact') }} <a href="mailto:{{ $organizer->getEmail() }}">{{ $organizer->getEmail() }}</a>.

{{ __('Best regards,') }}<br>
{{ $organizer->getName() ?: config('app.name') }}
</x-mail::message>
