/**
 * Intercepta erros 419 (CSRF Token Mismatch) do Livewire
 * e faz reload automático da página em vez de mostrar o dialog
 */
document.addEventListener('DOMContentLoaded', function () {

    // Livewire 3: interceptar erro de token expirado
    document.addEventListener('livewire:init', function () {

        // Hook no request do Livewire antes de enviar
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419) {
                    preventDefault(); // evita o dialog "This page has expired"
                    // Aguarda 500ms e recarrega
                    setTimeout(() => window.location.reload(), 500);
                }
                if (status === 403) {
                    preventDefault();
                    setTimeout(() => window.location.reload(), 500);
                }
            });
        });
    });

    // Fallback: interceptar o dialog nativo do browser
    // O Livewire 3 usa window.confirm para mostrar "This page has expired"
    const _originalConfirm = window.confirm;
    window.confirm = function (message) {
        if (
            message &&
            (
                message.includes('expired') ||
                message.includes('expirou') ||
                message.includes('refresh')
            )
        ) {
            // Em vez de perguntar, apenas recarrega
            setTimeout(() => window.location.reload(), 300);
            return true; // simula "OK" no dialog original
        }
        return _originalConfirm.apply(this, arguments);
    };
});
