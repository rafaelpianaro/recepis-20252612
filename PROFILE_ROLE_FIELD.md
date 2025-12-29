# Role Field in Profile Settings

## ✅ Implementação Completa

Foi adicionado um campo de seleção de **Role** na página de perfil do usuário.

### 📍 Localização
**URL:** `http://127.0.0.1:8000/settings/profile`

### 🎯 Funcionalidades

1. **Select Component criado** em `resources/js/components/ui/select/`
   - Select.vue
   - SelectTrigger.vue
   - SelectValue.vue
   - SelectContent.vue
   - SelectItem.vue

2. **Backend atualizado:**
   - Controller: `app/Http/Controllers/Settings/ProfileController.php`
     - Envia lista de roles disponíveis para o frontend
   - Request: `app/Http/Requests/Settings/ProfileUpdateRequest.php`
     - Validação: `role` deve ser 'admin', 'manager' ou 'user'

3. **Frontend atualizado:**
   - View: `resources/js/pages/settings/Profile.vue`
     - Campo Select para escolher role
     - Opções: User, Manager, Administrator

### 📋 Campos no Formulário

| Campo         | Tipo   | Descrição                           |
|---------------|--------|-------------------------------------|
| Name          | Input  | Nome do usuário                     |
| Email         | Input  | Email do usuário                    |
| **Role**      | Select | **Role: User, Manager, Admin**      |

### 🎨 Opções de Role

O dropdown exibe:
- **User** (value: 'user')
- **Manager** (value: 'manager')  
- **Administrator** (value: 'admin')

### 🔒 Validação

O backend valida que o role seja um dos valores permitidos:
```php
'role' => ['required', 'string', 'in:admin,manager,user']
```

### 📸 Como Usar

1. Faça login com qualquer usuário
2. Acesse: `http://127.0.0.1:8000/settings/profile`
3. Veja o novo campo "Role" abaixo do email
4. Selecione uma role no dropdown
5. Clique em "Save" para atualizar

### 🔄 Fluxo de Atualização

```
User selects role → Form submits → 
ProfileUpdateRequest validates → 
ProfileController updates user → 
User role is updated in database → 
Redirect back with success
```

### ⚠️ Considerações de Segurança

**IMPORTANTE:** Atualmente qualquer usuário pode mudar sua própria role. Para produção, considere:

1. **Restringir quem pode alterar roles:**
```php
// No ProfileUpdateRequest.php
public function authorize(): bool
{
    // Apenas admins podem alterar roles
    return $this->user()->isAdmin();
}
```

2. **Ou permitir apenas admin alterar roles de outros usuários:**
```php
// Criar uma rota separada /admin/users/{user}/role
Route::patch('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])
    ->middleware(['auth', 'role:admin']);
```

3. **Ou remover o campo role do perfil do usuário e criar uma interface admin separada**

### 🛠️ Exemplo de Proteção (Sugerido)

Para evitar que usuários comuns se tornem admin:

```php
// app/Http/Requests/Settings/ProfileUpdateRequest.php
public function rules(): array
{
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class)->ignore($this->user()->id),
        ],
    ];

    // Apenas admin pode alterar role
    if ($this->user()->isAdmin()) {
        $rules['role'] = ['required', 'string', 'in:admin,manager,user'];
    }

    return $rules;
}
```

E no Vue:
```vue
<div v-if="user?.role === 'admin'" class="grid gap-2">
    <Label for="role">Role</Label>
    <Select v-model="selectedRole" name="role">
        <!-- ... -->
    </Select>
</div>
```

### 📦 Arquivos Modificados

1. `app/Http/Controllers/Settings/ProfileController.php`
2. `app/Http/Requests/Settings/ProfileUpdateRequest.php`
3. `resources/js/pages/settings/Profile.vue`
4. `resources/js/components/ui/select/*` (novo)

### ✅ Status

- [x] Select component criado
- [x] Backend validando role
- [x] Frontend com dropdown funcional
- [x] Build realizado com sucesso
- [x] Documentação criada
- [ ] Adicionar proteção de segurança (recomendado)
