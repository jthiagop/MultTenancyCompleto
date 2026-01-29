import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Importa o gerenciador de estrelas de sugestão
import './custom/tenant/suggestion-star.js';

// Importa o gerenciador de filtros de datatable
import './custom/tenant/datatable-filters.js';

// Importa o módulo de DataTable Pane (gerencia as tabelas com skeleton loading)
import './custom/tenant/tenant-datatable-pane.js';
