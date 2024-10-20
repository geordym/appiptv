<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {



        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {


            if (Auth::check() && (Auth::user()->role === 'SUPER_ADMINISTRATOR' || Auth::user()->role === 'ADMINISTRATOR')) {
                $event->menu->add(
                    [
                        'text' => 'Canales IPTV',
                        'url'  => '/admin/canales',
                    ],
                    [
                        'text' => 'Paquetes',
                        'url'  => '/admin/paquetes',
                    ]
                );
            }


            if (Auth::check() && Auth::user()->role === 'SUPER_ADMINISTRATOR') {

                $event->menu->add([
                    'text' => 'Usuarios',
                    'url'  => '/admin/users',
                ]);
            }

            $event->menu->add([
                'text' => 'Acceso',
                'url'  => '/change-password',
            ]);

        });

    }
}
