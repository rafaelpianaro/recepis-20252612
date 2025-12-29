# Troubleshooting - Guia Completo

## 🔧 FIX: Erro SelectItem com Value Vazio

**Data:** 2025-12-29  
**Erro:** `A <SelectItem /> must have a value prop that is not an empty string`

### ❌ Problema
O componente SelectItem do Radix UI não aceita `value=""` (string vazia).

### ✅ Solução Aplicada
1. Removido `<SelectItem value="">` 
2. Tipo atualizado: `ref<string | undefined>`
3. Botão Clear (X) adicionado para limpar filtro
4. Placeholder mantido para mostrar "All roles"

**Status:** ✅ RESOLVIDO

---

## 🔧 FIX: Comportamento Estranho no Sidebar

**Data:** 2025-12-29  
**Problema:** Links do sidebar com comportamento inconsistente na navegação

### ❌ Problema
Uso incorreto do helper Wayfinder. O `dashboard()` retorna um objeto `{ url: '/dashboard', method: 'get' }`, mas o `Link` do Inertia espera uma string.

### ✅ Solução Aplicada
Substituído objetos por strings diretas:

```typescript
// ❌ Antes
href: dashboard()  // objeto

// ✅ Depois
href: '/dashboard'  // string
```

**Status:** ✅ RESOLVIDO

---

## 🔧 FIX: Erro de Autorização no UserController

**Data:** 2025-12-29  
**Erro:** `Call to undefined method App\Http\Controllers\Admin\UserController::authorize()`

### ❌ Problema
O método `authorize()` não estava disponível nos controllers porque o trait `AuthorizesRequests` não estava sendo usado.

### ✅ Solução Aplicada
Adicionado o trait no Controller base:

```php
// app/Http/Controllers/Controller.php
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;
}
```

**Status:** ✅ RESOLVIDO

---

## ✅ Credenciais Confirmadas

As seguintes credenciais foram verificadas e estão funcionando no sistema:

### ADMIN
- **Email:** admin@example.com
- **Senha:** password
- **Acesso:** Dashboard Admin, Gerenciamento de Usuários

### MANAGER
- **Email:** manager@example.com
- **Senha:** password
- **Acesso:** Dashboard Manager, Relatórios

### USER
- **Email:** user@example.com
- **Senha:** password
- **Acesso:** Dashboard padrão

## 🔍 Problemas Comuns

### 1. "Não consigo fazer login"

**Verificações:**
- [ ] Certifique-se de estar usando exatamente: `admin@example.com` (sem espaços)
- [ ] Senha: `password` (tudo minúsculo, sem espaços)
- [ ] Limpe o cache do navegador (Ctrl+Shift+Delete)
- [ ] Tente em uma aba anônima/privada
- [ ] Verifique se não há caps lock ativado

**Comandos de verificação:**
```bash
# Verificar usuários no banco
php artisan tinker --execute="App\Models\User::all(['email', 'name', 'role'])"

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild frontend
npm run build
```

### 2. Erro 403 após login

Se você consegue fazer login mas recebe erro 403 ao acessar rotas protegidas:

```bash
# Verificar role do usuário
php artisan tinker --execute="App\Models\User::where('email', 'admin@example.com')->first()->role"

# Deve retornar: admin
```

### 3. Página em branco após login

```bash
# Rebuild do frontend
npm run build

# Verificar logs
tail -50 storage/logs/laravel.log
```

### 4. "CSRF token mismatch"

```bash
# Limpar sessões
php artisan session:table
php artisan migrate:refresh --path=database/migrations/create_sessions_table.php

# Ou simplesmente
php artisan config:clear
```

## 🔧 Comandos Úteis

### Resetar senha de um usuário:
```bash
php artisan tinker
```
```php
$user = App\Models\User::where('email', 'admin@example.com')->first();
$user->password = Hash::make('novasenha');
$user->save();
```

### Verificar se usuário existe e está correto:
```bash
php artisan tinker --execute="
\$user = App\Models\User::where('email', 'admin@example.com')->first();
echo 'Nome: ' . \$user->name . PHP_EOL;
echo 'Role: ' . \$user->role->value . PHP_EOL;
echo 'Senha OK: ' . (Hash::check('password', \$user->password) ? 'SIM' : 'NÃO') . PHP_EOL;
"
```

### Recriar usuários de teste:
```bash
# Deletar usuários de teste existentes
php artisan tinker --execute="
App\Models\User::whereIn('email', ['admin@example.com', 'manager@example.com', 'user@example.com'])->delete();
"

# Rodar seeder novamente
php artisan db:seed --class=RoleSeeder
```

## 🌐 Testando no Navegador

1. Acesse: `http://localhost:8000/login` (ou a URL do seu servidor)
2. Digite: **admin@example.com**
3. Senha: **password**
4. Clique em "Login"

**Após login bem-sucedido:**
- Admin: será redirecionado para `/dashboard`
- Admin pode acessar: `/admin/dashboard` e `/admin/users`
- Manager pode acessar: `/manager/dashboard` e `/manager/reports`

## 📝 Debug Mode

Se ainda tiver problemas, ative o debug:

1. No arquivo `.env`:
```
APP_DEBUG=true
APP_ENV=local
```

2. Tente fazer login e veja a mensagem de erro completa

3. Verifique `storage/logs/laravel.log`:
```bash
tail -f storage/logs/laravel.log
```

## ✅ Teste de Autenticação

Para confirmar que tudo está funcionando:

```bash
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\$credentials = ['email' => 'admin@example.com', 'password' => 'password'];
if (Auth::attempt(\$credentials)) {
    echo '✅ Login funcionando corretamente!' . PHP_EOL;
    echo 'Usuário: ' . Auth::user()->name . PHP_EOL;
    echo 'Role: ' . Auth::user()->role->value . PHP_EOL;
} else {
    echo '❌ Falha no login' . PHP_EOL;
}
"
```

## 🆘 Ainda não funciona?

1. Verifique se o servidor está rodando: `php artisan serve`
2. Verifique se o frontend foi compilado: `npm run build`
3. Confirme a porta correta (padrão: 8000)
4. Tente com outro navegador
5. Desative extensões do navegador temporariamente

## 📧 Contato

Se o problema persistir, forneça:
- Mensagem de erro exata
- Screenshot da tela
- Conteúdo de `storage/logs/laravel.log`
- Saída do comando: `php artisan route:list | grep login`
