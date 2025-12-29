# Sistema de Roles - Laravel + Inertia + Vue

Sistema completo de roles (admin, manager, user) implementado no projeto.

## Estrutura Criada

### 1. Backend (Laravel)

#### Enum de Roles
- **Arquivo**: `app/UserRole.php`
- **Roles**: ADMIN, MANAGER, USER
- **Métodos úteis**:
  - `isAdmin()`, `isManager()`, `isUser()`
  - `hasAccessLevel()` - verifica hierarquia de acesso
  - `label()` - retorna label formatado

#### Model User
- **Arquivo**: `app/Models/User.php`
- Adicionado campo `role` (cast para UserRole enum)
- Métodos auxiliares:
  - `isAdmin()`, `isManager()`, `isUser()`
  - `hasRole(UserRole|string $role)`
  - `hasAnyRole(array $roles)`

#### Middleware
- **Arquivo**: `app/Http/Middleware/EnsureUserHasRole.php`
- **Alias**: `role`
- **Uso**: `->middleware('role:admin,manager')`

#### Migration
- **Arquivo**: `database/migrations/2025_12_27_025845_add_role_to_users_table.php`
- Adiciona coluna `role` (string, default: 'user', indexada)

#### Seeder
- **Arquivo**: `database/seeders/RoleSeeder.php`
- Cria 3 usuários de teste:
  - admin@example.com / password (admin)
  - manager@example.com / password (manager)
  - user@example.com / password (user)

### 2. Rotas

#### Admin Routes (`routes/admin.php`)
```php
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
    Route::get('/users', ...)->name('users');
});
```

#### Manager Routes (`routes/manager.php`)
```php
Route::middleware(['auth', 'verified', 'role:admin,manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
    Route::get('/reports', ...)->name('reports');
});
```

### 3. Frontend (Vue)

#### Páginas Admin
- `resources/js/pages/Admin/Dashboard.vue`
- `resources/js/pages/Admin/Users.vue`

#### Páginas Manager
- `resources/js/pages/Manager/Dashboard.vue`
- `resources/js/pages/Manager/Reports.vue`

#### Dados Compartilhados
O middleware `HandleInertiaRequests` compartilha:
```javascript
auth: {
    user: {
        id, name, email,
        role: 'admin',
        role_label: 'Administrator'
    }
}
```

## Como Usar

### No Backend

#### Proteger rotas com middleware:
```php
// Apenas admin
Route::get('/admin', ...)->middleware('role:admin');

// Admin ou Manager
Route::get('/dashboard', ...)->middleware('role:admin,manager');

// Qualquer role específica
Route::middleware(['auth', 'role:user'])->group(function () {
    //...
});
```

#### Verificar role no Controller:
```php
if ($request->user()->isAdmin()) {
    // código admin
}

if ($request->user()->hasRole('manager')) {
    // código manager
}

if ($request->user()->hasAnyRole(['admin', 'manager'])) {
    // código admin ou manager
}
```

### No Frontend (Vue)

#### Acessar role do usuário:
```vue
<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <div v-if="user?.role === 'admin'">
        Conteúdo apenas para Admin
    </div>

    <div v-if="['admin', 'manager'].includes(user?.role)">
        Conteúdo para Admin e Manager
    </div>
</template>
```

#### Criar composable (opcional):
```typescript
// composables/useAuth.ts
export function useAuth() {
    const page = usePage();
    const user = computed(() => page.props.auth.user);

    const isAdmin = computed(() => user.value?.role === 'admin');
    const isManager = computed(() => user.value?.role === 'manager');
    const isUser = computed(() => user.value?.role === 'user');

    const hasRole = (role: string) => user.value?.role === role;
    const hasAnyRole = (roles: string[]) => roles.includes(user.value?.role);

    return { user, isAdmin, isManager, isUser, hasRole, hasAnyRole };
}
```

## Factory

O UserFactory foi atualizado com métodos state para criar usuários com roles específicas:

```php
User::factory()->admin()->create();
User::factory()->manager()->create();
User::factory()->user()->create();
```

## Credenciais de Teste

Após rodar o seeder (`php artisan db:seed --class=RoleSeeder`):

| Email                   | Senha    | Role    |
|------------------------|----------|---------|
| admin@example.com      | password | admin   |
| manager@example.com    | password | manager |
| user@example.com       | password | user    |

## Hierarquia de Acesso

A hierarquia implementada no método `hasAccessLevel()`:
- **ADMIN**: acesso total a tudo
- **MANAGER**: acesso a recursos de manager e user
- **USER**: acesso apenas a recursos de user

## Próximos Passos (Sugestões)

1. Criar interface de gerenciamento de usuários em `/admin/users`
2. Implementar mudança de role pelo admin
3. Adicionar Gates/Policies para autorização mais granular
4. Criar dashboard específico para cada role
5. Adicionar filtros e pesquisa na lista de usuários
6. Implementar logs de ações por role
