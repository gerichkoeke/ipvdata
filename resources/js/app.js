import './bootstrap';

import Alpine from 'alpinejs';
import Persist from '@alpinejs/persist';
import Intersect from '@alpinejs/intersect';
import Focus from '@alpinejs/focus';

// Registrar plugins
Alpine.plugin(Persist);
Alpine.plugin(Intersect);
Alpine.plugin(Focus);

// APENAS expor globalmente - NÃO chamar Alpine.start()
// O Livewire 3 gerencia o ciclo de vida do Alpine
window.Alpine = Alpine;
