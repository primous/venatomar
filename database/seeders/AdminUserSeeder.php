<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Creates (or updates) the back-office account from ADMIN_EMAIL / ADMIN_PASSWORD.
 *
 *   php artisan db:seed --class=AdminUserSeeder
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'info@venatomar.com');
        // Jamais de mot de passe par défaut dans le code : il serait public sur GitHub.
        $password = (string) env('ADMIN_PASSWORD', '');
        $contactEmail = (string) env('ADMIN_CONTACT_EMAIL', 'contact@venatomar.com');
        $contactPassword = (string) env('ADMIN_CONTACT_PASSWORD', $password);
        $ayudaEmail = (string) env('ADMIN_AYUDA_EMAIL', 'ayuda@venatomar.com');
        $ayudaPassword = (string) env('ADMIN_AYUDA_PASSWORD', $password);

        if ($email === '' || strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD (12+ characters) in .env first.');
        }

        // Primary Admin (info@venatomar.com)
        User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Admin Principal (Info)', 'password' => $password]
        );

        // Secondary Admin (contact@venatomar.com)
        User::updateOrCreate(
            ['email' => $contactEmail],
            ['name' => 'Admin Secondaire (Contact)', 'password' => $contactPassword]
        );

        // Third Admin (ayuda@venatomar.com)
        User::updateOrCreate(
            ['email' => $ayudaEmail],
            ['name' => 'Admin Empréstimo (Ayuda)', 'password' => $ayudaPassword]
        );
    }
}
