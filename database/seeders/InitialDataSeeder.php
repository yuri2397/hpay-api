<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\ServiceFee;
use App\Models\ShippingCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création de la compagnie maritime CMA CGM
        $company = ShippingCompany::create([
            'id' => Str::uuid(),
            'name' => 'CMA CGM',
            'api_key' => 'cma_'.Str::random(20),
            'api_secret' => Str::random(32),
            'api_endpoint' => 'https://api.cmacgm-group.com/v1',
            'is_active' => true,
            'description' => 'CMA CGM est un leader mondial du transport maritime et de la logistique.',
            'contact_email' => 'contact@cmacgm.com',
            'contact_phone' => '+33 4 88 91 90 00',
            'logo' => 'storage/logos/cmacgm.png',
        ]);

        // Création des méthodes de paiement
        $waveMoney = PaymentMethod::create([
            'id' => Str::uuid(),
            'name' => 'Wave',
            'provider' => 'wave',
            'configuration' => [
                'merchant_id' => 'wave_merchant_'.Str::random(10),
                'api_key' => Str::random(32),
                'api_secret' => Str::random(32),
                'webhook_url' => env('APP_URL').'/api/webhooks/wave',
                'environment' => 'test',
            ],
            'is_active' => true,
            'description' => 'Paiement via l\'application Wave Money',
        ]);

        $orangeMoney = PaymentMethod::create([
            'id' => Str::uuid(),
            'name' => 'Orange Money',
            'provider' => 'orange_money',
            'configuration' => [
                'merchant_id' => 'om_merchant_'.Str::random(10),
                'api_key' => Str::random(32),
                'api_secret' => Str::random(32),
                'webhook_url' => env('APP_URL').'/api/webhooks/orange-money',
                'environment' => 'test',
            ],
            'is_active' => true,
            'description' => 'Paiement via Orange Money',
        ]);

        $visa = PaymentMethod::create([
            'id' => Str::uuid(),
            'name' => 'Carte Visa/Mastercard',
            'provider' => 'stripe',
            'configuration' => [
                'public_key' => 'pk_test_'.Str::random(24),
                'secret_key' => 'sk_test_'.Str::random(24),
                'webhook_secret' => 'whsec_'.Str::random(24),
                'webhook_url' => env('APP_URL').'/api/webhooks/stripe',
                'environment' => 'test',
            ],
            'is_active' => true,
            'description' => 'Paiement par carte bancaire via Stripe',
        ]);

        // Création des frais de service pour chaque méthode de paiement
        ServiceFee::create([
            'id' => Str::uuid(),
            'name' => 'Frais Wave',
            'percentage' => 1.5,
            'fixed_amount' => 0,
            'min_amount' => 100,
            'max_amount' => null,
            'is_active' => true,
            'description' => 'Frais de transaction pour les paiements Wave',
            'shipping_company_id' => $company->id,
            'payment_method_id' => $waveMoney->id,
        ]);

        ServiceFee::create([
            'id' => Str::uuid(),
            'name' => 'Frais Orange Money',
            'percentage' => 2.0,
            'fixed_amount' => 0,
            'min_amount' => 200,
            'max_amount' => null,
            'is_active' => true,
            'description' => 'Frais de transaction pour les paiements Orange Money',
            'shipping_company_id' => $company->id,
            'payment_method_id' => $orangeMoney->id,
        ]);

        ServiceFee::create([
            'id' => Str::uuid(),
            'name' => 'Frais Carte Bancaire',
            'percentage' => 2.9,
            'fixed_amount' => 500,
            'min_amount' => 500,
            'max_amount' => null,
            'is_active' => true,
            'description' => 'Frais de transaction pour les paiements par carte bancaire',
            'shipping_company_id' => $company->id,
            'payment_method_id' => $visa->id,
        ]);

        // Création d'un frais de service général
        ServiceFee::create([
            'id' => Str::uuid(),
            'name' => 'Frais standard',
            'percentage' => 1.0,
            'fixed_amount' => 0,
            'min_amount' => 500,
            'max_amount' => 10000,
            'is_active' => true,
            'description' => 'Frais standard appliqués à tous les paiements',
            'shipping_company_id' => null,
            'payment_method_id' => null,
        ]);

        $this->command->info('Données initiales créées avec succès !');
    }
}
