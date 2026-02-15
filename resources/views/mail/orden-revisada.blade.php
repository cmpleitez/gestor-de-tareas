@component('mail::message')
# Orden de Compra #{{ \App\Services\KeyRipper::rip($recepcion->atencion_id) }} Revisada

ASUNTO: **GESTIÓN DE PAGO Y ENTREGA**

Estimado(a) **{{ $recepcion->usuarioOrigen->name }}**,

Su orden de compra #{{ \App\Services\KeyRipper::rip($recepcion->atencion_id) }} ha sido revisada exitosamente. A continuación, encontrará el detalle de la misma, por favor revisela y validela para continuar con la gestión del pago y entrega:

@component('mail::table')
| Kit | Cantidad | Precio Unitario | Subtotal |
| :--------- | :-------------: | :-------------: | :--------: |
@php
    $totalGlobal = 0;
@endphp
@foreach ($recepcion->atencion->ordenes as $orden)
@php
    $subtotal = $orden->unidades * $orden->precio;
    $totalGlobal += $subtotal;
@endphp
| {{ $orden->kit->kit }} | {{ $orden->unidades }} | ${{ number_format($orden->precio, 2, '.', ',') }} | ${{ number_format($subtotal, 2, '.', ',') }} |
@endforeach
| | | **TOTAL** | **${{ number_format($totalGlobal, 2, '.', ',') }}** |
@endcomponent

Para ver más detalles, puede acceder a su cuenta haciendo clic en el siguiente botón:

@component('mail::button', ['url' => route('dashboard')])
Ver Mi Orden
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
