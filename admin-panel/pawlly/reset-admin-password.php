<?php

/**
 * Admin Password Reset Script
 *
 * Usage: php reset-admin-password.php
 *
 * This script will reset the admin password to a new password you specify.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "========================================\n";
echo "  Admin Password Reset Tool\n";
echo "========================================\n\n";

// Get admin email
echo "Enter admin email [admin@pawlly.com]: ";
$handle = fopen("php://stdin", "r");
$email = trim(fgets($handle));
if (empty($email)) {
    $email = 'admin@pawlly.com';
}

// Find user
$user = User::where('email', $email)->first();

if (!$user) {
    echo "\n❌ Error: User with email '$email' not found!\n\n";
    exit(1);
}

echo "\n✓ Found user: {$user->first_name} {$user->last_name}\n";

// Get new password
echo "\nEnter new password (min 8 characters): ";
$password = trim(fgets($handle));

if (strlen($password) < 8) {
    echo "\n❌ Error: Password must be at least 8 characters!\n\n";
    exit(1);
}

// Confirm password
echo "Confirm new password: ";
$confirmPassword = trim(fgets($handle));

if ($password !== $confirmPassword) {
    echo "\n❌ Error: Passwords do not match!\n\n";
    exit(1);
}

// Update password
try {
    $user->password = Hash::make($password);
    $user->save();

    echo "\n✅ Success! Password has been updated for {$user->email}\n";
    echo "\nYou can now login with:\n";
    echo "  Email: {$user->email}\n";
    echo "  Password: (the password you just set)\n\n";

} catch (\Exception $e) {
    echo "\n❌ Error updating password: " . $e->getMessage() . "\n\n";
    exit(1);
}

fclose($handle);
