<?php

namespace Database\Seeders;

use App\Models\ShippingCompany;
use App\Models\ShippingCompanySetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class CmaCgmSettingsSeeder extends Seeder
{
    /**
     * Seed the CMA CGM settings.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Ajout des configurations CMA CGM...');

        try {
            // Récupérer la compagnie maritime CMA CGM
            $cmaCgm = ShippingCompany::where('name', 'CMA CGM')->first();

            if (!$cmaCgm) {
                $this->command->error('Compagnie maritime CMA CGM non trouvée. Exécutez d\'abord le seeder InitialDataSeeder.');
                return;
            }

            // Configuration des URLs API
            $settings = [

                // Paramètres business
                'commission_percentage' => '.9',
                'default_behalf_of' => '',  // Laisser vide par défaut

            ];

            // Insérer ou mettre à jour chaque paramètre
            foreach ($settings as $key => $value) {
                ShippingCompanySetting::updateOrCreate(
                    [
                        'shipping_company_id' => $cmaCgm->id,
                        'key' => $key
                    ],
                    ['value' => $value]
                );
            }

            $this->command->info('Configurations CMA CGM ajoutées avec succès!');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout des configurations CMA CGM: ' . $e->getMessage());
            $this->command->error('Erreur lors de l\'ajout des configurations CMA CGM: ' . $e->getMessage());
        }
    }
}