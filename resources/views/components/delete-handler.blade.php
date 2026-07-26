{{-- Envía las eliminaciones por DELETE con token CSRF. Antes eran enlaces GET: un <img src> a esa URL bastaba para borrar --}}
<script>
    $(document).on('click', '.btn-eliminar', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        if (!url) {
            return;
        }
        $('<form>', {
            method: 'POST',
            action: url
        }).append(
            $('<input>', { type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }),
            $('<input>', { type: 'hidden', name: '_method', value: 'DELETE' })
        ).appendTo('body').submit();
    });
</script>
