<?php

namespace App;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case USER = 'user';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MANAGER => 'Manager',
            self::USER => 'User',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isManager(): bool
    {
        return $this === self::MANAGER;
    }

    public function isUser(): bool
    {
        return $this === self::USER;
    }

    public function hasAccessLevel(self $role): bool
    {
        return match($this) {
            self::ADMIN => true,
            self::MANAGER => in_array($role, [self::MANAGER, self::USER]),
            self::USER => $role === self::USER,
        };
    }
}
