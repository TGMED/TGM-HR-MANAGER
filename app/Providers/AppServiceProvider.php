<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerBrevoMailer();
    }

    /**
     * Register the Brevo transactional email API as a mail transport.
     */
    protected function registerBrevoMailer(): void
    {
        Mail::extend('brevo', function (array $config): BrevoApiTransport {
            $key = $config['key'] ?? config('services.brevo.key');

            if (blank($key)) {
                throw new \InvalidArgumentException('The Brevo mailer requires an API key. Set BREVO_API_KEY in your environment.');
            }

            return new BrevoApiTransport($key);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        // The signup form mirrors these rules as a live checklist, so they must
        // hold in every environment. Only the breach check — which costs an
        // HTTP round trip — stays gated to production.
        Password::defaults(fn (): Password => Password::min(8)
            ->mixedCase()
            ->letters()
            ->numbers()
            ->symbols()
            ->when(
                app()->isProduction(),
                fn (Password $rule): Password => $rule->uncompromised(),
            ),
        );
    }
}
