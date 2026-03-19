<?php

namespace Modules\HomePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HomePage\App\Models\ContactMessage;
use Modules\HomePage\App\Models\NewsletterSubscriber;

class CommunicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = [
            [
                'email' => 'pastor.demo@vepl.org',
                'name' => 'Pastor Demo',
                'ministerial_interest' => 'pregacao_expositiva',
                'segment' => 'pastores',
                'preferred_frequency' => 'weekly',
                'is_active' => true,
                'is_confirmed' => true,
                'subscribed_at' => now()->subDays(10),
                'lead_score' => 85,
            ],
            [
                'email' => 'lider.demo@vepl.org',
                'name' => 'Lider Demo',
                'ministerial_interest' => 'lideranca_equipe',
                'segment' => 'lideres',
                'preferred_frequency' => 'biweekly',
                'is_active' => true,
                'is_confirmed' => true,
                'subscribed_at' => now()->subDays(6),
                'lead_score' => 72,
            ],
        ];

        foreach ($subscribers as $subscriber) {
            NewsletterSubscriber::firstOrCreate(
                ['email' => $subscriber['email']],
                $subscriber
            );
        }

        $messages = [
            [
                'name' => 'Pr. Lucas Silva',
                'email' => 'lucas.silva@igreja.org',
                'phone' => '(11) 98888-1111',
                'message' => 'Gostaria de orientacoes sobre a trilha de formacao para novos pastores auxiliares.',
                'inquiry_type' => 'formacao',
                'ministerial_context' => 'pastorado_auxiliar',
                'interest_area' => 'curriculo',
                'lead_score' => 90,
                'status' => 'new',
            ],
            [
                'name' => 'Ir. Daniel Rocha',
                'email' => 'daniel.rocha@ministerio.org',
                'phone' => '(21) 97777-2222',
                'message' => 'Minha igreja quer participar da proxima conferencia para lideres de pequenos grupos.',
                'inquiry_type' => 'evento',
                'ministerial_context' => 'lideranca_pequenos_grupos',
                'interest_area' => 'inscricao',
                'lead_score' => 76,
                'status' => 'in_progress',
                'follow_up_scheduled' => now()->addDay(),
            ],
        ];

        foreach ($messages as $message) {
            ContactMessage::firstOrCreate(
                ['email' => $message['email'], 'message' => $message['message']],
                $message
            );
        }
    }
}
