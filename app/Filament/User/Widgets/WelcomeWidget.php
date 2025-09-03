<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.user.widgets.welcome-widget';

        public function getGreeting(): string
    {
        return 'Selamat datang, ' . auth()->user()->name;
    }
}

