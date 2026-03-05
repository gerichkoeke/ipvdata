#!/bin/bash
# =============================================================================
# IPV ERP — Setup i18n
# Executa na raiz do projeto: bash setup-i18n.sh
# =============================================================================

set -e

BOLD="\033[1m"
GREEN="\033[0;32m"
YELLOW="\033[1;33m"
RED="\033[0;31m"
RESET="\033[0m"

log()    { echo -e "${GREEN}✔${RESET} $1"; }
warn()   { echo -e "${YELLOW}⚠${RESET}  $1"; }
header() { echo -e "\n${BOLD}$1${RESET}"; }

# =============================================================================
header "1/5 — Verificando diretório do projeto"
# =============================================================================

if [ ! -f "artisan" ]; then
    echo -e "${RED}✖ Execute este script na raiz do projeto Laravel (onde está o artisan).${RESET}"
    exit 1
fi
log "Raiz do projeto Laravel confirmada."

# =============================================================================
header "2/5 — Limpando arquivos desnecessários"
# =============================================================================

FILES_TO_REMOVE=(
    "resources/js/i18n.js"
    "resources/js/components/LanguageCurrencySelector.jsx"
    "resources/js/locales/pt-BR.json"
    "resources/js/locales/en.json"
    "resources/js/locales/es.json"
)

for f in "${FILES_TO_REMOVE[@]}"; do
    if [ -f "$f" ]; then
        rm "$f"
        log "Removido: $f"
    fi
done

warn "Verificando arquivos legados em lang/ ..."
for dir in lang/pt_BR lang/en lang/es; do
    if [ -d "$dir" ]; then
        for file in "$dir"/*.php; do
            [ -f "$file" ] || continue
            basename_file=$(basename "$file")
            if [ "$basename_file" != "app.php" ]; then
                prefix="${basename_file%.php}"
                matches=$(grep -r "__('${prefix}\." resources/views/ 2>/dev/null | wc -l)
                if [ "$matches" -eq 0 ]; then
                    rm "$file"
                    log "Removido: $file"
                else
                    warn "Mantido: $file (usado em $matches lugar(es))"
                fi
            fi
        done
    fi
done

# =============================================================================
header "3/5 — Criando arquivos de tradução (lang/)"
# =============================================================================

mkdir -p lang/pt_BR lang/en lang/es

cat > lang/pt_BR/app.php << 'PHPEOF'
<?php
return [
    'save' => 'Salvar', 'cancel' => 'Cancelar', 'edit' => 'Editar',
    'delete' => 'Excluir', 'confirm' => 'Confirmar', 'back' => 'Voltar',
    'search' => 'Buscar', 'filter' => 'Filtrar', 'export' => 'Exportar',
    'import' => 'Importar', 'actions' => 'Ações', 'status' => 'Status',
    'active' => 'Ativo', 'inactive' => 'Inativo', 'yes' => 'Sim', 'no' => 'Não',
    'name' => 'Nome', 'email' => 'E-mail', 'phone' => 'Telefone',
    'created_at' => 'Criado em', 'updated_at' => 'Atualizado em',
    'total' => 'Total', 'subtotal' => 'Subtotal', 'discount' => 'Desconto',
    'loading' => 'Carregando...', 'no_records' => 'Nenhum registro encontrado.',
    'select' => 'Selecione', 'optional' => 'Opcional', 'required' => 'Obrigatório',
    'view' => 'Visualizar', 'close' => 'Fechar', 'send' => 'Enviar',
    'copy' => 'Copiar', 'download' => 'Baixar', 'upload' => 'Enviar arquivo',
    'new' => 'Novo', 'add' => 'Adicionar', 'remove' => 'Remover',
    'clear' => 'Limpar', 'apply' => 'Aplicar', 'reset' => 'Redefinir',
    'refresh' => 'Atualizar', 'success' => 'Sucesso', 'error' => 'Erro',
    'warning' => 'Atenção', 'info' => 'Informação',
    'auth' => [
        'login' => 'Entrar', 'logout' => 'Sair', 'email' => 'E-mail',
        'password' => 'Senha', 'remember_me' => 'Lembrar-me',
        'forgot_password' => 'Esqueceu a senha?', 'reset_password' => 'Redefinir senha',
        'send_reset_link' => 'Enviar link de redefinição',
        'new_password' => 'Nova senha', 'confirm_password' => 'Confirmar senha',
        'invalid_credentials' => 'Credenciais inválidas.',
        'account_disabled' => 'Sua conta está desativada.',
        'mfa_code' => 'Código de autenticação', 'mfa_verify' => 'Verificar',
        'mfa_invalid' => 'Código inválido ou expirado.',
        'select_language' => 'Idioma', 'welcome' => 'Bem-vindo ao IPV ERP',
        'sign_in_to' => 'Acesse o painel',
    ],
    'nav' => [
        'dashboard' => 'Dashboard', 'users' => 'Usuários', 'partners' => 'Parceiros',
        'distributors' => 'Distribuidores', 'customers' => 'Clientes',
        'products' => 'Produtos', 'plans' => 'Planos', 'orders' => 'Pedidos',
        'proposals' => 'Propostas', 'projects' => 'Projetos', 'financial' => 'Financeiro',
        'commissions' => 'Comissões', 'reports' => 'Relatórios',
        'settings' => 'Configurações', 'support' => 'Suporte', 'logs' => 'Logs',
        'profile' => 'Perfil', 'catalog' => 'Catálogo', 'infrastructure' => 'Infraestrutura',
    ],
    'dashboard' => [
        'title' => 'Dashboard', 'welcome' => 'Bem-vindo',
        'total_partners' => 'Total de Parceiros', 'total_customers' => 'Total de Clientes',
        'total_revenue' => 'Receita Total', 'total_orders' => 'Total de Pedidos',
        'active_projects' => 'Projetos Ativos', 'pending_orders' => 'Pedidos Pendentes',
        'monthly_revenue' => 'Receita Mensal', 'recent_orders' => 'Pedidos Recentes',
        'recent_customers' => 'Clientes Recentes', 'top_partners' => 'Top Parceiros',
        'commission_summary' => 'Resumo de Comissões',
    ],
    'partners' => [
        'title' => 'Parceiros', 'new' => 'Novo Parceiro', 'edit' => 'Editar Parceiro',
        'name' => 'Nome', 'email' => 'E-mail', 'phone' => 'Telefone',
        'document' => 'CNPJ/CPF', 'locale' => 'Idioma', 'currency' => 'Moeda',
        'commission_rate' => 'Taxa de Comissão', 'is_active' => 'Ativo',
        'created_at' => 'Criado em', 'distributor' => 'Distribuidor',
        'no_partners' => 'Nenhum parceiro encontrado.',
        'deleted' => 'Parceiro excluído com sucesso.', 'saved' => 'Parceiro salvo com sucesso.',
    ],
    'distributors' => [
        'title' => 'Distribuidores', 'new' => 'Novo Distribuidor', 'edit' => 'Editar Distribuidor',
        'name' => 'Nome', 'email' => 'E-mail', 'phone' => 'Telefone',
        'locale' => 'Idioma', 'currency' => 'Moeda', 'is_active' => 'Ativo',
        'saved' => 'Distribuidor salvo com sucesso.', 'deleted' => 'Distribuidor excluído com sucesso.',
    ],
    'customers' => [
        'title' => 'Clientes', 'new' => 'Novo Cliente', 'edit' => 'Editar Cliente',
        'name' => 'Nome', 'email' => 'E-mail', 'phone' => 'Telefone',
        'document' => 'CNPJ/CPF', 'partner' => 'Parceiro', 'is_active' => 'Ativo',
        'saved' => 'Cliente salvo com sucesso.', 'deleted' => 'Cliente excluído com sucesso.',
        'infra' => 'Infraestrutura', 'dashboard' => 'Visão Geral',
    ],
    'orders' => [
        'title' => 'Pedidos', 'new' => 'Novo Pedido', 'number' => 'Nº do Pedido',
        'status' => 'Status', 'pending' => 'Pendente', 'approved' => 'Aprovado',
        'rejected' => 'Rejeitado', 'cancelled' => 'Cancelado',
        'total' => 'Total', 'date' => 'Data', 'saved' => 'Pedido salvo com sucesso.',
    ],
    'proposals' => [
        'title' => 'Propostas', 'new' => 'Nova Proposta', 'number' => 'Nº da Proposta',
        'customer' => 'Cliente', 'status' => 'Status', 'draft' => 'Rascunho',
        'sent' => 'Enviada', 'accepted' => 'Aceita', 'rejected' => 'Rejeitada',
        'total' => 'Total', 'currency' => 'Moeda',
        'saved' => 'Proposta salva com sucesso.', 'sent_ok' => 'Proposta enviada com sucesso.',
    ],
    'projects' => [
        'title' => 'Projetos', 'new' => 'Novo Projeto', 'name' => 'Nome do Projeto',
        'customer' => 'Cliente', 'status' => 'Status', 'active' => 'Ativo',
        'inactive' => 'Inativo', 'total' => 'Total', 'discount' => 'Desconto',
        'saved' => 'Projeto salvo com sucesso.',
    ],
    'financial' => [
        'title' => 'Financeiro', 'balance' => 'Saldo', 'receivable' => 'A Receber',
        'paid' => 'Pago', 'pending' => 'Pendente', 'invoice' => 'Fatura',
        'payment_date' => 'Data de Pagamento', 'due_date' => 'Vencimento',
        'amount' => 'Valor', 'currency' => 'Moeda',
    ],
    'commissions' => [
        'title' => 'Comissões', 'rate' => 'Taxa', 'amount' => 'Valor',
        'status' => 'Status', 'pending' => 'Pendente', 'approved' => 'Aprovada',
        'paid' => 'Paga', 'period' => 'Período', 'partner' => 'Parceiro', 'order' => 'Pedido',
    ],
    'profile' => [
        'title' => 'Meu Perfil', 'name' => 'Nome', 'email' => 'E-mail',
        'phone' => 'Telefone', 'avatar' => 'Avatar', 'language' => 'Idioma',
        'currency' => 'Moeda', 'current_password' => 'Senha atual',
        'new_password' => 'Nova senha', 'confirm_password' => 'Confirmar senha',
        'saved' => 'Perfil atualizado com sucesso.',
        'mfa_enabled' => 'Autenticação em dois fatores ativa',
        'mfa_disabled' => 'Autenticação em dois fatores inativa',
    ],
    'languages' => ['pt_BR' => 'Português (Brasil)', 'en' => 'English', 'es' => 'Español'],
    'currencies' => [
        'BRL' => 'Real Brasileiro (BRL)', 'USD' => 'Dólar Americano (USD)',
        'EUR' => 'Euro (EUR)', 'ARS' => 'Peso Argentino (ARS)',
        'CLP' => 'Peso Chileno (CLP)', 'COP' => 'Peso Colombiano (COP)',
        'MXN' => 'Peso Mexicano (MXN)', 'PEN' => 'Sol Peruano (PEN)',
        'UYU' => 'Peso Uruguaio (UYU)', 'PYG' => 'Guarani Paraguaio (PYG)',
    ],
    'infra' => [
        'vm' => 'Máquinas Virtuais', 'storage' => 'Armazenamento', 'backup' => 'Backup',
        'network' => 'Rede', 'cpu' => 'CPU', 'ram' => 'RAM', 'disk' => 'Disco',
        'os' => 'Sistema Operacional', 'license' => 'Licença', 'bandwidth' => 'Banda',
        'iops' => 'IOPS', 'monthly' => 'Mensal', 'per_hour' => 'Por hora',
        'add_vm' => 'Adicionar VM', 'remove_vm' => 'Remover VM', 'configure' => 'Configurar',
        'price' => 'Preço', 'total_price' => 'Preço Total',
    ],
    'settings' => [
        'title' => 'Configurações', 'company' => 'Dados da Empresa',
        'company_name' => 'Nome da Empresa', 'logo' => 'Logo',
        'timezone' => 'Fuso Horário', 'default_locale' => 'Idioma Padrão',
        'saved' => 'Configurações salvas com sucesso.',
    ],
    'errors' => [
        'generic' => 'Ocorreu um erro. Tente novamente.',
        'not_found' => 'Registro não encontrado.',
        'unauthorized' => 'Você não tem permissão para esta ação.',
        'validation' => 'Verifique os campos e tente novamente.',
        'server' => 'Erro interno do servidor.',
    ],
];
PHPEOF
log "Criado: lang/pt_BR/app.php"

cat > lang/en/app.php << 'PHPEOF'
<?php
return [
    'save' => 'Save', 'cancel' => 'Cancel', 'edit' => 'Edit',
    'delete' => 'Delete', 'confirm' => 'Confirm', 'back' => 'Back',
    'search' => 'Search', 'filter' => 'Filter', 'export' => 'Export',
    'import' => 'Import', 'actions' => 'Actions', 'status' => 'Status',
    'active' => 'Active', 'inactive' => 'Inactive', 'yes' => 'Yes', 'no' => 'No',
    'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
    'created_at' => 'Created at', 'updated_at' => 'Updated at',
    'total' => 'Total', 'subtotal' => 'Subtotal', 'discount' => 'Discount',
    'loading' => 'Loading...', 'no_records' => 'No records found.',
    'select' => 'Select', 'optional' => 'Optional', 'required' => 'Required',
    'view' => 'View', 'close' => 'Close', 'send' => 'Send',
    'copy' => 'Copy', 'download' => 'Download', 'upload' => 'Upload file',
    'new' => 'New', 'add' => 'Add', 'remove' => 'Remove',
    'clear' => 'Clear', 'apply' => 'Apply', 'reset' => 'Reset',
    'refresh' => 'Refresh', 'success' => 'Success', 'error' => 'Error',
    'warning' => 'Warning', 'info' => 'Information',
    'auth' => [
        'login' => 'Sign In', 'logout' => 'Sign Out', 'email' => 'Email',
        'password' => 'Password', 'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot your password?', 'reset_password' => 'Reset password',
        'send_reset_link' => 'Send reset link',
        'new_password' => 'New password', 'confirm_password' => 'Confirm password',
        'invalid_credentials' => 'Invalid credentials.',
        'account_disabled' => 'Your account is disabled.',
        'mfa_code' => 'Authentication code', 'mfa_verify' => 'Verify',
        'mfa_invalid' => 'Invalid or expired code.',
        'select_language' => 'Language', 'welcome' => 'Welcome to IPV ERP',
        'sign_in_to' => 'Sign in to your panel',
    ],
    'nav' => [
        'dashboard' => 'Dashboard', 'users' => 'Users', 'partners' => 'Partners',
        'distributors' => 'Distributors', 'customers' => 'Customers',
        'products' => 'Products', 'plans' => 'Plans', 'orders' => 'Orders',
        'proposals' => 'Proposals', 'projects' => 'Projects', 'financial' => 'Financial',
        'commissions' => 'Commissions', 'reports' => 'Reports',
        'settings' => 'Settings', 'support' => 'Support', 'logs' => 'Logs',
        'profile' => 'Profile', 'catalog' => 'Catalog', 'infrastructure' => 'Infrastructure',
    ],
    'dashboard' => [
        'title' => 'Dashboard', 'welcome' => 'Welcome',
        'total_partners' => 'Total Partners', 'total_customers' => 'Total Customers',
        'total_revenue' => 'Total Revenue', 'total_orders' => 'Total Orders',
        'active_projects' => 'Active Projects', 'pending_orders' => 'Pending Orders',
        'monthly_revenue' => 'Monthly Revenue', 'recent_orders' => 'Recent Orders',
        'recent_customers' => 'Recent Customers', 'top_partners' => 'Top Partners',
        'commission_summary' => 'Commission Summary',
    ],
    'partners' => [
        'title' => 'Partners', 'new' => 'New Partner', 'edit' => 'Edit Partner',
        'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
        'document' => 'Tax ID', 'locale' => 'Language', 'currency' => 'Currency',
        'commission_rate' => 'Commission Rate', 'is_active' => 'Active',
        'created_at' => 'Created at', 'distributor' => 'Distributor',
        'no_partners' => 'No partners found.',
        'deleted' => 'Partner deleted successfully.', 'saved' => 'Partner saved successfully.',
    ],
    'distributors' => [
        'title' => 'Distributors', 'new' => 'New Distributor', 'edit' => 'Edit Distributor',
        'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
        'locale' => 'Language', 'currency' => 'Currency', 'is_active' => 'Active',
        'saved' => 'Distributor saved successfully.', 'deleted' => 'Distributor deleted successfully.',
    ],
    'customers' => [
        'title' => 'Customers', 'new' => 'New Customer', 'edit' => 'Edit Customer',
        'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone',
        'document' => 'Tax ID', 'partner' => 'Partner', 'is_active' => 'Active',
        'saved' => 'Customer saved successfully.', 'deleted' => 'Customer deleted successfully.',
        'infra' => 'Infrastructure', 'dashboard' => 'Overview',
    ],
    'orders' => [
        'title' => 'Orders', 'new' => 'New Order', 'number' => 'Order #',
        'status' => 'Status', 'pending' => 'Pending', 'approved' => 'Approved',
        'rejected' => 'Rejected', 'cancelled' => 'Cancelled',
        'total' => 'Total', 'date' => 'Date', 'saved' => 'Order saved successfully.',
    ],
    'proposals' => [
        'title' => 'Proposals', 'new' => 'New Proposal', 'number' => 'Proposal #',
        'customer' => 'Customer', 'status' => 'Status', 'draft' => 'Draft',
        'sent' => 'Sent', 'accepted' => 'Accepted', 'rejected' => 'Rejected',
        'total' => 'Total', 'currency' => 'Currency',
        'saved' => 'Proposal saved successfully.', 'sent_ok' => 'Proposal sent successfully.',
    ],
    'projects' => [
        'title' => 'Projects', 'new' => 'New Project', 'name' => 'Project Name',
        'customer' => 'Customer', 'status' => 'Status', 'active' => 'Active',
        'inactive' => 'Inactive', 'total' => 'Total', 'discount' => 'Discount',
        'saved' => 'Project saved successfully.',
    ],
    'financial' => [
        'title' => 'Financial', 'balance' => 'Balance', 'receivable' => 'Receivable',
        'paid' => 'Paid', 'pending' => 'Pending', 'invoice' => 'Invoice',
        'payment_date' => 'Payment Date', 'due_date' => 'Due Date',
        'amount' => 'Amount', 'currency' => 'Currency',
    ],
    'commissions' => [
        'title' => 'Commissions', 'rate' => 'Rate', 'amount' => 'Amount',
        'status' => 'Status', 'pending' => 'Pending', 'approved' => 'Approved',
        'paid' => 'Paid', 'period' => 'Period', 'partner' => 'Partner', 'order' => 'Order',
    ],
    'profile' => [
        'title' => 'My Profile', 'name' => 'Name', 'email' => 'Email',
        'phone' => 'Phone', 'avatar' => 'Avatar', 'language' => 'Language',
        'currency' => 'Currency', 'current_password' => 'Current password',
        'new_password' => 'New password', 'confirm_password' => 'Confirm password',
        'saved' => 'Profile updated successfully.',
        'mfa_enabled' => 'Two-factor authentication enabled',
        'mfa_disabled' => 'Two-factor authentication disabled',
    ],
    'languages' => ['pt_BR' => 'Português (Brasil)', 'en' => 'English', 'es' => 'Español'],
    'currencies' => [
        'BRL' => 'Brazilian Real (BRL)', 'USD' => 'US Dollar (USD)',
        'EUR' => 'Euro (EUR)', 'ARS' => 'Argentine Peso (ARS)',
        'CLP' => 'Chilean Peso (CLP)', 'COP' => 'Colombian Peso (COP)',
        'MXN' => 'Mexican Peso (MXN)', 'PEN' => 'Peruvian Sol (PEN)',
        'UYU' => 'Uruguayan Peso (UYU)', 'PYG' => 'Paraguayan Guaraní (PYG)',
    ],
    'infra' => [
        'vm' => 'Virtual Machines', 'storage' => 'Storage', 'backup' => 'Backup',
        'network' => 'Network', 'cpu' => 'CPU', 'ram' => 'RAM', 'disk' => 'Disk',
        'os' => 'Operating System', 'license' => 'License', 'bandwidth' => 'Bandwidth',
        'iops' => 'IOPS', 'monthly' => 'Monthly', 'per_hour' => 'Per hour',
        'add_vm' => 'Add VM', 'remove_vm' => 'Remove VM', 'configure' => 'Configure',
        'price' => 'Price', 'total_price' => 'Total Price',
    ],
    'settings' => [
        'title' => 'Settings', 'company' => 'Company Details',
        'company_name' => 'Company Name', 'logo' => 'Logo',
        'timezone' => 'Timezone', 'default_locale' => 'Default Language',
        'saved' => 'Settings saved successfully.',
    ],
    'errors' => [
        'generic' => 'An error occurred. Please try again.',
        'not_found' => 'Record not found.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'validation' => 'Please check the fields and try again.',
        'server' => 'Internal server error.',
    ],
];
PHPEOF
log "Criado: lang/en/app.php"

cat > lang/es/app.php << 'PHPEOF'
<?php
return [
    'save' => 'Guardar', 'cancel' => 'Cancelar', 'edit' => 'Editar',
    'delete' => 'Eliminar', 'confirm' => 'Confirmar', 'back' => 'Volver',
    'search' => 'Buscar', 'filter' => 'Filtrar', 'export' => 'Exportar',
    'import' => 'Importar', 'actions' => 'Acciones', 'status' => 'Estado',
    'active' => 'Activo', 'inactive' => 'Inactivo', 'yes' => 'Sí', 'no' => 'No',
    'name' => 'Nombre', 'email' => 'Correo electrónico', 'phone' => 'Teléfono',
    'created_at' => 'Creado el', 'updated_at' => 'Actualizado el',
    'total' => 'Total', 'subtotal' => 'Subtotal', 'discount' => 'Descuento',
    'loading' => 'Cargando...', 'no_records' => 'No se encontraron registros.',
    'select' => 'Seleccionar', 'optional' => 'Opcional', 'required' => 'Obligatorio',
    'view' => 'Ver', 'close' => 'Cerrar', 'send' => 'Enviar',
    'copy' => 'Copiar', 'download' => 'Descargar', 'upload' => 'Subir archivo',
    'new' => 'Nuevo', 'add' => 'Agregar', 'remove' => 'Eliminar',
    'clear' => 'Limpiar', 'apply' => 'Aplicar', 'reset' => 'Restablecer',
    'refresh' => 'Actualizar', 'success' => 'Éxito', 'error' => 'Error',
    'warning' => 'Advertencia', 'info' => 'Información',
    'auth' => [
        'login' => 'Iniciar sesión', 'logout' => 'Cerrar sesión', 'email' => 'Correo electrónico',
        'password' => 'Contraseña', 'remember_me' => 'Recuérdame',
        'forgot_password' => '¿Olvidaste tu contraseña?', 'reset_password' => 'Restablecer contraseña',
        'send_reset_link' => 'Enviar enlace de restablecimiento',
        'new_password' => 'Nueva contraseña', 'confirm_password' => 'Confirmar contraseña',
        'invalid_credentials' => 'Credenciales inválidas.',
        'account_disabled' => 'Tu cuenta está desactivada.',
        'mfa_code' => 'Código de autenticación', 'mfa_verify' => 'Verificar',
        'mfa_invalid' => 'Código inválido o expirado.',
        'select_language' => 'Idioma', 'welcome' => 'Bienvenido a IPV ERP',
        'sign_in_to' => 'Accede a tu panel',
    ],
    'nav' => [
        'dashboard' => 'Dashboard', 'users' => 'Usuarios', 'partners' => 'Socios',
        'distributors' => 'Distribuidores', 'customers' => 'Clientes',
        'products' => 'Productos', 'plans' => 'Planes', 'orders' => 'Pedidos',
        'proposals' => 'Propuestas', 'projects' => 'Proyectos', 'financial' => 'Financiero',
        'commissions' => 'Comisiones', 'reports' => 'Informes',
        'settings' => 'Configuración', 'support' => 'Soporte', 'logs' => 'Registros',
        'profile' => 'Perfil', 'catalog' => 'Catálogo', 'infrastructure' => 'Infraestructura',
    ],
    'dashboard' => [
        'title' => 'Dashboard', 'welcome' => 'Bienvenido',
        'total_partners' => 'Total de Socios', 'total_customers' => 'Total de Clientes',
        'total_revenue' => 'Ingresos Totales', 'total_orders' => 'Total de Pedidos',
        'active_projects' => 'Proyectos Activos', 'pending_orders' => 'Pedidos Pendientes',
        'monthly_revenue' => 'Ingresos Mensuales', 'recent_orders' => 'Pedidos Recientes',
        'recent_customers' => 'Clientes Recientes', 'top_partners' => 'Mejores Socios',
        'commission_summary' => 'Resumen de Comisiones',
    ],
    'partners' => [
        'title' => 'Socios', 'new' => 'Nuevo Socio', 'edit' => 'Editar Socio',
        'name' => 'Nombre', 'email' => 'Correo electrónico', 'phone' => 'Teléfono',
        'document' => 'Nº de Identificación', 'locale' => 'Idioma', 'currency' => 'Moneda',
        'commission_rate' => 'Tasa de Comisión', 'is_active' => 'Activo',
        'created_at' => 'Creado el', 'distributor' => 'Distribuidor',
        'no_partners' => 'No se encontraron socios.',
        'deleted' => 'Socio eliminado correctamente.', 'saved' => 'Socio guardado correctamente.',
    ],
    'distributors' => [
        'title' => 'Distribuidores', 'new' => 'Nuevo Distribuidor', 'edit' => 'Editar Distribuidor',
        'name' => 'Nombre', 'email' => 'Correo electrónico', 'phone' => 'Teléfono',
        'locale' => 'Idioma', 'currency' => 'Moneda', 'is_active' => 'Activo',
        'saved' => 'Distribuidor guardado correctamente.', 'deleted' => 'Distribuidor eliminado correctamente.',
    ],
    'customers' => [
        'title' => 'Clientes', 'new' => 'Nuevo Cliente', 'edit' => 'Editar Cliente',
        'name' => 'Nombre', 'email' => 'Correo electrónico', 'phone' => 'Teléfono',
        'document' => 'Nº de Identificación', 'partner' => 'Socio', 'is_active' => 'Activo',
        'saved' => 'Cliente guardado correctamente.', 'deleted' => 'Cliente eliminado correctamente.',
        'infra' => 'Infraestructura', 'dashboard' => 'Resumen',
    ],
    'orders' => [
        'title' => 'Pedidos', 'new' => 'Nuevo Pedido', 'number' => 'Nº de Pedido',
        'status' => 'Estado', 'pending' => 'Pendiente', 'approved' => 'Aprobado',
        'rejected' => 'Rechazado', 'cancelled' => 'Cancelado',
        'total' => 'Total', 'date' => 'Fecha', 'saved' => 'Pedido guardado correctamente.',
    ],
    'proposals' => [
        'title' => 'Propuestas', 'new' => 'Nueva Propuesta', 'number' => 'Nº de Propuesta',
        'customer' => 'Cliente', 'status' => 'Estado', 'draft' => 'Borrador',
        'sent' => 'Enviada', 'accepted' => 'Aceptada', 'rejected' => 'Rechazada',
        'total' => 'Total', 'currency' => 'Moneda',
        'saved' => 'Propuesta guardada correctamente.', 'sent_ok' => 'Propuesta enviada correctamente.',
    ],
    'projects' => [
        'title' => 'Proyectos', 'new' => 'Nuevo Proyecto', 'name' => 'Nombre del Proyecto',
        'customer' => 'Cliente', 'status' => 'Estado', 'active' => 'Activo',
        'inactive' => 'Inactivo', 'total' => 'Total', 'discount' => 'Descuento',
        'saved' => 'Proyecto guardado correctamente.',
    ],
    'financial' => [
        'title' => 'Financiero', 'balance' => 'Saldo', 'receivable' => 'Por Cobrar',
        'paid' => 'Pagado', 'pending' => 'Pendiente', 'invoice' => 'Factura',
        'payment_date' => 'Fecha de Pago', 'due_date' => 'Vencimiento',
        'amount' => 'Monto', 'currency' => 'Moneda',
    ],
    'commissions' => [
        'title' => 'Comisiones', 'rate' => 'Tasa', 'amount' => 'Monto',
        'status' => 'Estado', 'pending' => 'Pendiente', 'approved' => 'Aprobada',
        'paid' => 'Pagada', 'period' => 'Período', 'partner' => 'Socio', 'order' => 'Pedido',
    ],
    'profile' => [
        'title' => 'Mi Perfil', 'name' => 'Nombre', 'email' => 'Correo electrónico',
        'phone' => 'Teléfono', 'avatar' => 'Avatar', 'language' => 'Idioma',
        'currency' => 'Moneda', 'current_password' => 'Contraseña actual',
        'new_password' => 'Nueva contraseña', 'confirm_password' => 'Confirmar contraseña',
        'saved' => 'Perfil actualizado correctamente.',
        'mfa_enabled' => 'Autenticación en dos pasos activa',
        'mfa_disabled' => 'Autenticación en dos pasos inactiva',
    ],
    'languages' => ['pt_BR' => 'Português (Brasil)', 'en' => 'English', 'es' => 'Español'],
    'currencies' => [
        'BRL' => 'Real Brasileño (BRL)', 'USD' => 'Dólar Estadounidense (USD)',
        'EUR' => 'Euro (EUR)', 'ARS' => 'Peso Argentino (ARS)',
        'CLP' => 'Peso Chileno (CLP)', 'COP' => 'Peso Colombiano (COP)',
        'MXN' => 'Peso Mexicano (MXN)', 'PEN' => 'Sol Peruano (PEN)',
        'UYU' => 'Peso Uruguayo (UYU)', 'PYG' => 'Guaraní Paraguayo (PYG)',
    ],
    'infra' => [
        'vm' => 'Máquinas Virtuales', 'storage' => 'Almacenamiento', 'backup' => 'Respaldo',
        'network' => 'Red', 'cpu' => 'CPU', 'ram' => 'RAM', 'disk' => 'Disco',
        'os' => 'Sistema Operativo', 'license' => 'Licencia', 'bandwidth' => 'Ancho de banda',
        'iops' => 'IOPS', 'monthly' => 'Mensual', 'per_hour' => 'Por hora',
        'add_vm' => 'Agregar VM', 'remove_vm' => 'Eliminar VM', 'configure' => 'Configurar',
        'price' => 'Precio', 'total_price' => 'Precio Total',
    ],
    'settings' => [
        'title' => 'Configuración', 'company' => 'Datos de la Empresa',
        'company_name' => 'Nombre de la Empresa', 'logo' => 'Logotipo',
        'timezone' => 'Zona Horaria', 'default_locale' => 'Idioma Predeterminado',
        'saved' => 'Configuración guardada correctamente.',
    ],
    'errors' => [
        'generic' => 'Ocurrió un error. Por favor, inténtalo de nuevo.',
        'not_found' => 'Registro no encontrado.',
        'unauthorized' => 'No tienes permiso para realizar esta acción.',
        'validation' => 'Por favor verifica los campos e inténtalo de nuevo.',
        'server' => 'Error interno del servidor.',
    ],
];
PHPEOF
log "Criado: lang/es/app.php"

# =============================================================================
header "4/5 — Criando Middleware, Controller e Migration"
# =============================================================================

mkdir -p app/Http/Middleware app/Http/Controllers database/migrations

cat > app/Http/Middleware/SetLocale.php << 'PHPEOF'
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class SetLocale
{
    protected array $supported = ['pt_BR', 'en', 'es'];
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolveLocale($request));
        return $next($request);
    }
    protected function resolveLocale(Request $request): string
    {
        if (Auth::check()) {
            $user = Auth::user();
            $locale = $user->active_locale ?? $user->locale ?? config('app.locale');
            return $this->normalize($locale);
        }
        if ($request->session()->has('locale')) {
            return $this->normalize($request->session()->get('locale'));
        }
        return config('app.locale', 'pt_BR');
    }
    protected function normalize(string $locale): string
    {
        if (in_array($locale, $this->supported, true)) return $locale;
        $prefix = strtok($locale, '_-');
        foreach ($this->supported as $supported) {
            if (str_starts_with($supported, $prefix)) return $supported;
        }
        return config('app.locale', 'pt_BR');
    }
}
PHPEOF
log "Criado: app/Http/Middleware/SetLocale.php"

cat > app/Http/Controllers/LocaleController.php << 'PHPEOF'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LocaleController extends Controller
{
    protected array $supported = ['pt_BR', 'en', 'es'];
    public function switch(Request $request)
    {
        $locale = $request->input('locale', config('app.locale'));
        if (!in_array($locale, $this->supported, true)) {
            $locale = config('app.locale', 'pt_BR');
        }
        $request->session()->put('locale', $locale);
        if (Auth::check()) {
            $user = Auth::user();
            $user->locale = $locale;
            $user->save();
        }
        return redirect($request->input('redirect', url()->previous()));
    }
}
PHPEOF
log "Criado: app/Http/Controllers/LocaleController.php"

cat > database/migrations/2026_03_05_000001_add_locale_currency_to_partners_table.php << 'PHPEOF'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('locale', 10)->default('pt_BR')->after('phone');
            $table->string('currency', 3)->default('BRL')->after('locale');
        });
    }
    public function down(): void {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn(['locale', 'currency']);
        });
    }
};
PHPEOF
log "Criado: database/migrations/2026_03_05_000001_add_locale_currency_to_partners_table.php"

# =============================================================================
header "5/5 — Criando views de login com seletor de idioma (3 painéis)"
# =============================================================================

for panel in admin distributor partner; do
    DIR="resources/views/filament/${panel}/pages/auth"
    mkdir -p "$DIR"
    cat > "${DIR}/login.blade.php" << 'BLADEEOF'
<x-filament-panels::page.simple>

    {{-- Seletor de Idioma --}}
    <div class="mb-4 flex justify-end gap-2">
        @foreach(['pt_BR' => '🇧🇷 PT', 'en' => '🇺🇸 EN', 'es' => '🇪🇸 ES'] as $lang => $label)
            <form method="POST" action="{{ route('locale.switch') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ $lang }}">
                <input type="hidden" name="redirect" value="{{ url()->current() }}">
                <button
                    type="submit"
                    class="px-2 py-1 text-xs rounded border transition-colors
                           {{ app()->getLocale() === $lang
                               ? 'bg-primary-600 text-white border-primary-600'
                               : 'border-gray-300 text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700' }}"
                >
                    {{ $label }}
                </button>
            </form>
        @endforeach
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}
        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

</x-filament-panels::page.simple>
BLADEEOF
    log "Criado: ${DIR}/login.blade.php"
done

# =============================================================================
echo ""
echo -e "${BOLD}════════════════════════════════════════════════════════${RESET}"
echo -e "${GREEN}  Setup concluído! Faltam apenas 3 edições manuais:${RESET}"
echo -e "${BOLD}════════════════════════════════════════════════════════${RESET}"
echo ""
echo -e "  ${YELLOW}1. routes/web.php${RESET} — adicione no final:"
echo '     use App\Http\Controllers\LocaleController;'
echo "     Route::post('locale/switch', [LocaleController::class, 'switch'])->name('locale.switch');"
echo ""
echo -e "  ${YELLOW}2. bootstrap/app.php${RESET} — dentro de ->withMiddleware(...):"
echo '     $middleware->web(append: [\App\Http\Middleware\SetLocale::class]);'
echo ""
echo -e "  ${YELLOW}3. app/Models/Partner.php${RESET} — adicione ao \$fillable:"
echo "     'locale', 'currency',"
echo ""
echo -e "  Depois rode:"
echo -e "  ${BOLD}php artisan migrate && php artisan optimize:clear${RESET}"
echo ""
