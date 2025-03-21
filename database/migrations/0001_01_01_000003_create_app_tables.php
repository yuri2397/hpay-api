<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTables extends Migration
{

    public function up()
    {
        // Table pour les entreprises maritimes
        Schema::create('shipping_companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('api_endpoint')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Nous supprimons la table client_types qui n'est plus nécessaire

        // Table pour les clients
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // Nom du client (entreprise ou personne)
            $table->enum('type', ['customer', 'carrier']); // Type de client intégré directement
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('tax_id')->nullable();
            $table->json('client_references')->nullable(); // Pour stocker les références des clients du carrier
            $table->boolean('is_active')->default(true); // Solde de commission pour les carriers
            $table->timestamps();
            $table->softDeletes();
        });


        // Table pour les factures
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('end_client_info')->nullable(); // Informations sur le client final si transitaire
            $table->string('reference')->nullable(); // Une seule référence par facture
            $table->string('invoice_type')->default('invoice'); // Type de facture intégré directement (fret, douane, etc.)

            // Champs standardisés mappés depuis les différents formats d'API
            $table->string('invoice_number'); // Identifiant unique de la facture chez la compagnie
            $table->decimal('amount', 15, 2); // Montant total de la facture
            $table->string('currency', 3)->default('XOF');

            // Statut de paiement dans notre système
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');

            // Stockage des données spécifiques à chaque format de compagnie
            $table->json('invoice_data')->nullable(); // Toutes les données spécifiques au format de la compagnie

            // Référence à la facture originale
            $table->string('document_path')->nullable(); // Chemin vers le PDF de la facture
            $table->boolean('is_api_fetched')->default(false); // Indique si la facture a été récupérée via API

            $table->foreignUuid('shipping_company_id')->references('id')->on('shipping_companies');
            $table->foreignUuid('client_id')->references('id')->on('clients');

            $table->timestamps();
            $table->softDeletes();

            // Chaque entreprise doit avoir un numéro de facture unique
            $table->unique(['shipping_company_id', 'invoice_number']);
        });

        // Table pour les méthodes de paiement
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // ex: 'carte_credit', 'virement', 'mobile_money', 'stripe', etc.
            $table->string('provider'); // ex: 'orange_money', 'stripe', 'paypal', etc.
            $table->json('configuration')->nullable(); // Configuration spécifique au fournisseur
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Table pour les frais de service
        Schema::create('service_fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('percentage', 5, 2); // Pourcentage des frais
            $table->decimal('fixed_amount', 10, 2)->default(0); // Montant fixe éventuel
            $table->decimal('min_amount', 10, 2)->nullable(); // Montant minimum des frais
            $table->decimal('max_amount', 10, 2)->nullable(); // Montant maximum des frais
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            $table->foreignUuid('shipping_company_id')->nullable()->references('id')->on('shipping_companies')->onDelete('set null');
            $table->foreignUuid('payment_method_id')->nullable()->references('id')->on('payment_methods')->onDelete('set null');

            $table->timestamps();
        });

        // Table pour les paiements - une facture liée à un seul paiement
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('end_client_info')->nullable(); // Informations sur le client final si payé par un transitaire
            $table->string('transaction_id')->nullable(); // ID de transaction externe
            $table->decimal('amount', 15, 2); // Montant total payé
            $table->decimal('invoice_amount', 15, 2); // Montant des factures
            $table->decimal('fee_amount', 15, 2); // Montant des frais de service
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('payment_response')->nullable(); // Réponse du service de paiement
            $table->json('metadata')->nullable(); // Données supplémentaires

            $table->foreignUuid('client_id')->references('id')->on('clients');
            $table->foreignUuid('payment_method_id')->references('id')->on('payment_methods');
            $table->foreignUuid('service_fee_id')->references('id')->on('service_fees');
            $table->foreignUuid('invoice_id')->nullable()->references('id')->on('invoices');

            $table->timestamps();
            $table->softDeletes();
        });

        // Table pour la relation entre paiements et factures - N'EST PLUS NECESSAIRE car une facture a un seul paiement
        // Nous la supprimons

        // Table pour les références temporaires de factures
        Schema::create('invoice_references', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_type'); // Type de référence (BL, conteneur, facture, etc.)
            $table->string('reference_value'); // Valeur de la référence
            $table->json('api_response')->nullable(); // Réponse de l'API lors de la recherche
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();

            $table->foreignUuid('client_id')->references('id')->on('clients');
            $table->foreignUuid('shipping_company_id')->references('id')->on('shipping_companies');

            $table->timestamps();
        });

        // Table pour les journaux d'API
        Schema::create('api_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('endpoint');
            $table->string('method'); // GET, POST, etc.
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
            $table->integer('status_code')->nullable();
            $table->boolean('is_success');
            $table->text('error_message')->nullable();
            $table->timestamp('request_time');
            $table->timestamp('response_time')->nullable();

            $table->foreignUuid('shipping_company_id')->references('id')->on('shipping_companies');

            $table->timestamps();
        });

        // Table pour les notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('notifiable'); // Peut être lié à différents modèles (client, facture, etc.)
            $table->string('type'); // Type de notification
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->foreignUuid('user_id')->nullable()->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Ajout d'une table pour les comptes de commission des transitaires
        Schema::create('commission_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('balance', 15, 2)->default(0); // Solde actuel du compte
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreignUuid('client_id')->references('id')->on('clients')->onDelete('cascade');
        });

        // Table pour les transactions des comptes de commission
        Schema::create('commission_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['deposit', 'withdrawal', 'commission', 'adjustment']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('balance_after', 15, 2); // Solde après transaction
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreignUuid('commission_account_id')->references('id')->on('commission_accounts')->onDelete('cascade');
            $table->foreignUuid('invoice_id')->nullable()->references('id')->on('invoices')->onDelete('set null');
            $table->foreignUuid('payment_id')->nullable()->references('id')->on('payments')->onDelete('set null');
        });
    }

    public function down()
    {
        // Supprimer les tables dans l'ordre inverse de leur création (à cause des contraintes étrangères)
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('invoice_payment');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('service_fees');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_types');
        Schema::dropIfExists('client_types');

        Schema::dropIfExists('clients');
        Schema::dropIfExists('client_types');
        Schema::dropIfExists('shipping_companies');
    }
};
