# 📝 LOG VIEWER INSTALADO E PROTEGIDO

**Data:** 2025-12-29  
**Atualizado:** 2025-12-30  
**Pacote:** opcodesio/log-viewer v3.21.1  
**Segurança:** Apenas admins podem acessar

---

## ✅ Instalação Completa

### Pacote Instalado
```bash
composer require opcodesio/log-viewer
```

**Dependências instaladas:**
- ✅ opcodesio/log-viewer v3.21.1
- ✅ opcodesio/mail-parser v0.1.6

---

## 🔐 Segurança Implementada

### 1. Gate de Autorização
**Arquivo:** `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    Gate::policy(User::class, UserPolicy::class);

    // LogViewer authorization: Only admins can view logs
    Gate::define('viewLogViewer', function (User $user) {
        return $user->isAdmin();
    });
}
```

### 2. Middleware Configurado
**Arquivo:** `config/log-viewer.php`

```php
'middleware' => [
    'web',
    'auth',              // ✅ Requer autenticação
    'verified',          // ✅ Requer email verificado
    \Opcodes\LogViewer\Http\Middleware\AuthorizeLogViewer::class,
],

'api_middleware' => [
    'auth',              // ✅ Protege API também
    'verified',
    \Opcodes\LogViewer\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    \Opcodes\LogViewer\Http\Middleware\AuthorizeLogViewer::class,
],
```

### 3. Rota Protegida
```php
'route_path' => 'admin/logs',  // ✅ Sob namespace /admin
```

---

## 📋 Configuração Completa

### URL de Acesso
```
https://seu-dominio.com/admin/logs
```

### Proteções Ativas
1. ✅ **Autenticação Laravel**: Usuário deve estar logado
2. ✅ **Email verificado**: Conta deve estar verificada
3. ✅ **Gate `viewLogViewer`**: Apenas admins (`isAdmin()`)
4. ✅ **Middleware específico**: `AuthorizeLogViewer`

---

## 🎯 Fluxo de Autorização

```
1. Usuário tenta acessar /admin/logs
   ↓
2. Middleware 'auth' verifica autenticação
   ↓ (se não autenticado → redirect /login)
3. Middleware 'verified' verifica email
   ↓ (se não verificado → redirect /verify-email)
4. AuthorizeLogViewer chama Gate 'viewLogViewer'
   ↓
5. Gate verifica: $user->isAdmin()
   ↓ (se false → HTTP 403 Forbidden)
6. ✅ Acesso liberado
```

---

## 🎨 Menu Sidebar Atualizado

**Arquivo:** `resources/js/components/AppSidebar.vue`

```typescript
// Admin-only menu items
if (user.value?.role === 'admin') {
    items.push({
        title: 'Admin',
        href: '/admin/dashboard',
        icon: Shield,
    });
    items.push({
        title: 'Users',
        href: '/admin/users',
        icon: Users,
    });
    items.push({
        title: 'Logs',        // ✅ NOVO
        href: '/admin/logs',
        icon: FileText,       // 📄 Icon
    });
}
```

### Menu Visual
```
Admin Sidebar (apenas para admins):
├─ 📊 Dashboard
├─ 🛡️  Admin
├─ 👥 Users
└─ 📄 Logs  ← NOVO!
```

---

## 🚀 Funcionalidades do LogViewer

### Visualização de Logs
- ✅ **Interface Web moderna**: UI responsiva
- ✅ **Busca em tempo real**: Filtrar logs por texto
- ✅ **Múltiplos arquivos**: Ver todos os arquivos de log
- ✅ **Níveis de log**: Emergency, Alert, Critical, Error, Warning, Notice, Info, Debug
- ✅ **Syntax highlighting**: Código formatado
- ✅ **Stack traces**: Traces de erro formatados

### Gerenciamento de Arquivos
- ✅ **Download de logs**: Baixar arquivos individualmente
- ✅ **Deletar logs**: Remover arquivos antigos
- ✅ **Limpar cache**: Cache management
- ✅ **Múltiplos hosts**: Suporte para logs de diferentes servidores

### Filtros Avançados
- ✅ **Por data**: Filtrar por período
- ✅ **Por nível**: emergency, error, warning, info, etc.
- ✅ **Por arquivo**: Selecionar arquivo específico
- ✅ **Por host**: Em ambientes distribuídos

---

## 📊 Rotas Registradas

### Interface Principal
```
GET  /admin/logs           → Interface web do LogViewer
```

### API (todas protegidas)
```
GET     /admin/logs/api/files                    → Lista arquivos de log
DELETE  /admin/logs/api/files/{fileIdentifier}   → Deleta arquivo
GET     /admin/logs/api/files/{fileIdentifier}/download → Download
POST    /admin/logs/api/clear-cache-all          → Limpa todo cache
GET     /admin/logs/api/folders                  → Lista pastas de log
GET     /admin/logs/api/hosts                    → Lista hosts
GET     /admin/logs/api/logs                     → Busca/filtro de logs
```

---

## 🔍 Testes de Segurança

### Teste 1: User Regular
```
1. Login como user@example.com
2. Tentar acessar /admin/logs
3. Resultado: 403 Forbidden ✅
```

### Teste 2: Manager
```
1. Login como manager@example.com
2. Tentar acessar /admin/logs
3. Resultado: 403 Forbidden ✅
```

### Teste 3: Admin
```
1. Login como admin@example.com
2. Acessar /admin/logs
3. Resultado: LogViewer interface ✅
```

### Teste 4: Não Autenticado
```
1. Não estar logado
2. Tentar acessar /admin/logs
3. Resultado: Redirect para /login ✅
```

### Teste 5: API Protegida
```bash
# Sem autenticação
curl http://localhost/admin/logs/api/files
# Resultado: 401 Unauthorized ✅

# Com autenticação de non-admin
curl -H "Cookie: laravel_session=..." http://localhost/admin/logs/api/files
# Resultado: 403 Forbidden ✅
```

---

## 🎓 Como Usar

### 1. Acesso Básico
```
1. Fazer login como admin
2. Clicar em "Logs" no sidebar
3. Visualizar interface do LogViewer
```

### 2. Ver Logs Específicos
```
1. Selecionar arquivo na lista (ex: laravel.log)
2. Escolher nível de log (All, Error, Warning, etc.)
3. Usar busca para filtrar mensagens
```

### 3. Download de Logs
```
1. Clicar no arquivo desejado
2. Botão "Download" no topo
3. Arquivo .log é baixado
```

### 4. Limpar Logs Antigos
```
1. Selecionar arquivo(s)
2. Botão "Delete" 
3. Confirmar exclusão
```

---

## ⚙️ Configurações Avançadas

### Personalizar Aparência
```php
// config/log-viewer.php

'theme' => Theme::Dark, // ou Theme::Light
```

### Alterar Timezone
```php
'timezone' => 'America/Sao_Paulo',
```

### Formato de Data
```php
'datetime_format' => 'd/m/Y H:i:s',
```

### Desabilitar em Produção
```bash
# .env
LOG_VIEWER_ENABLED=false
```

---

## 🔒 Boas Práticas de Segurança

### 1. ✅ Implementadas
- Gate com verificação de role
- Middleware de autenticação
- Middleware de email verificado
- Rota sob namespace admin
- API protegida com mesmo nível de segurança

### 2. ⚠️ Recomendações Adicionais

#### Rate Limiting
```php
// config/log-viewer.php
'middleware' => [
    'web',
    'auth',
    'verified',
    'throttle:60,1', // ✅ Max 60 requisições/minuto
    \Opcodes\LogViewer\Http\Middleware\AuthorizeLogViewer::class,
],
```

#### IP Whitelist (opcional)
```php
// AppServiceProvider.php
Gate::define('viewLogViewer', function (User $user) {
    $allowedIps = ['127.0.0.1', '192.168.1.1'];
    $userIp = request()->ip();
    
    return $user->isAdmin() && in_array($userIp, $allowedIps);
});
```

#### Logs de Auditoria
```php
// Criar evento quando logs são acessados
Gate::after(function (User $user, $ability) {
    if ($ability === 'viewLogViewer' && Gate::allows($ability)) {
        Log::info('LogViewer accessed', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'ip' => request()->ip(),
        ]);
    }
});
```

---

## 📈 Performance

### Cache Automático
O LogViewer usa cache para melhor performance:
```
Cache TTL: 60 minutos (padrão)
Storage: file cache do Laravel
```

### Paginação
```
Registros por página: 25 (padrão)
Lazy loading: Sim
```

### Índice de Arquivos
```
Cache de lista de arquivos: 5 minutos
Refresh automático: Sim
```

---

## 🐛 Troubleshooting

### Erro 403 ao Acessar
```
Causa: Gate não autoriza
Solução: Verificar se usuário é admin
```
```bash
php artisan tinker
>>> $user = User::find(1);
>>> $user->isAdmin(); // Deve retornar true
```

### Interface Não Carrega
```
Causa: Assets não publicados
Solução: Republicar assets
```
```bash
php artisan log-viewer:publish --force
```

### Logs Não Aparecem
```
Causa: Permissões de arquivo
Solução: Ajustar permissões
```
```bash
chmod -R 775 storage/logs
chown -R www-data:www-data storage/logs
```

---

## 📚 Documentação Adicional

### Links Úteis
- [LogViewer Docs](https://log-viewer.opcodes.io/)
- [GitHub Repo](https://github.com/opcodesio/log-viewer)
- [Laravel Logging](https://laravel.com/docs/11.x/logging)

---

## ✅ Checklist de Implementação

- [x] Pacote instalado via Composer
- [x] Configuração publicada
- [x] Assets publicados
- [x] Gate de autorização criado
- [x] Middleware configurado
- [x] Rota movida para /admin/logs
- [x] Menu sidebar atualizado
- [x] Frontend compilado
- [x] Testes de segurança passando
- [x] Documentação criada

---

## 🎉 Status

**✅ LOG VIEWER INSTALADO E PROTEGIDO**

**Segurança:**
- ✅ Apenas admins podem acessar
- ✅ Gate personalizado
- ✅ Middleware em todas as rotas
- ✅ API protegida
- ✅ Menu visível apenas para admins

**Funcionalidades:**
- ✅ Interface web moderna
- ✅ Busca e filtros avançados
- ✅ Download de logs
- ✅ Gerenciamento de arquivos
- ✅ Múltiplos níveis de log
- ✅ Stack traces formatados

**Performance:**
- ✅ Cache automático
- ✅ Paginação
- ✅ Lazy loading
- ✅ Bundle otimizado

---

**LogViewer está pronto para uso em produção com total segurança!** 🎉

Apenas administradores podem visualizar, baixar e gerenciar os logs do sistema através da interface web em `/admin/logs`.

---

## 🔄 Atualizações (2025-12-30)

### Botão "Back to System" Corrigido

**Problema:** Botão apontava para URL raiz do site  
**Solução:** Configurado para redirecionar ao Admin Dashboard

**Configuração:**
```php
// config/log-viewer.php
'back_to_system_url' => config('app.url') . '/admin/dashboard',
'back_to_system_label' => 'Back to Admin Dashboard',
```

**Antes:** "Back to Laravel" → `http://localhost:8000/`  
**Depois:** "Back to Admin Dashboard" → `http://localhost:8000/admin/dashboard`

