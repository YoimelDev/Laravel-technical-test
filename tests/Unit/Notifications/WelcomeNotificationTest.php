<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Tests\TestCase;

class WelcomeNotificationTest extends TestCase
{
    public function test_builds_mail_message(): void
    {
        $notification = new WelcomeNotification();
        $mailMessage = $notification->toMail(User::factory()->make());

        $this->assertEquals('Bienvenido a ' . config('app.name'), $mailMessage->subject);
        $this->assertStringContainsString('¡Bienvenido!', $mailMessage->greeting);
        $this->assertStringContainsString('Tu cuenta ha sido creada exitosamente.', $mailMessage->introLines[0]);
    }

    public function test_notification_uses_mail_channel(): void
    {
        $notification = new WelcomeNotification();
        
        $this->assertEquals(['mail'], $notification->via());
    }
}
