<?php

namespace App\Services;

use App\Models\User;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class MfaService
{
    public function generateSecret(): string
    {
        return Google2FA::generateSecretKey();
    }

    public function getQrCodeUrl(User $user, string $secret): string
    {
        return Google2FA::getQRCodeUrl(
            config('app.name', 'IPV ERP'),
            $user->email,
            $secret
        );
    }

    public function getQrCodeInline(User $user, string $secret): string
    {
        $url     = $this->getQrCodeUrl($user, $secret);
        $encoded = urlencode($url);
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={$encoded}";
    }

    public function verifyCode(string $secret, string $code): bool
    {
        try {
            return (bool) Google2FA::verifyKey($secret, $code);
        } catch (\Exception) {
            return false;
        }
    }

    public function enable(User $user, string $secret): void
    {
        $user->update([
            'mfa_secret'       => encrypt($secret),
            'mfa_enabled'      => true,
            'mfa_confirmed_at' => now(),
        ]);
    }

    public function disable(User $user): void
    {
        $user->update([
            'mfa_secret'       => null,
            'mfa_enabled'      => false,
            'mfa_confirmed_at' => null,
        ]);
    }

    public function getDecryptedSecret(User $user): ?string
    {
        if (!$user->mfa_secret) return null;
        try {
            return decrypt($user->mfa_secret);
        } catch (\Exception) {
            return null;
        }
    }
}
