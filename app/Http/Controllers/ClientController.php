<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CommissionAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients avec filtrage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validation des paramètres de requête
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:customer,carrier',
            'active' => 'nullable|boolean',
            'search' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|string|in:name,email,created_at,country',
            'sort_direction' => 'nullable|string|in:asc,desc',
            'with' => 'nullable|array',
            'with.*' => 'nullable|string|in:commissionAccount,invoices,payments,invoiceReferences'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Paramètres de filtrage invalides',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Client::query();

        // Filtrer par type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtrer par statut actif/inactif
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Recherche par nom ou email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtrer par pays
        if ($request->has('country')) {
            $query->where('country', $request->country);
        }

        // Tri
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Chargement des relations
        $allowedRelations = ['commissionAccount', 'invoices', 'payments', 'invoiceReferences'];
        $relations = [];

        if ($request->has('with')) {
            $requestedRelations = $request->input('with', []);
            foreach ($requestedRelations as $relation) {
                if (in_array($relation, $allowedRelations)) {
                    $relations[] = $relation;
                }
            }
        } else {
            // Relations par défaut
            $relations = ['commissionAccount'];
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $clients = $query->with($relations)->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $clients
        ]);
    }

    /**
     * Stocker un nouveau client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:customer,carrier',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'client_references' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Créer le client
            $client = Client::create([
                'name' => $request->name,
                'type' => $request->type,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'tax_id' => $request->tax_id,
                'client_references' => $request->client_references,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            // Si c'est un transporteur, créer un compte de commission
            if ($client->type === 'carrier') {
                CommissionAccount::create([
                    'client_id' => $client->id,
                    'balance' => 0,
                    'currency' => 'XOF',
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Client créé avec succès',
                'data' => $client->fresh(['commissionAccount'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un client spécifique.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $client = Client::with(['commissionAccount', 'invoices', 'payments'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $client
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mettre à jour un client existant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);

            // Validation des données
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'type' => 'sometimes|required|string|in:customer,carrier',
                'email' => 'sometimes|required|string|email|max:255|unique:clients,email,' . $id,
                'phone' => 'sometimes|required|string|max:20',
                'address' => 'sometimes|required|string|max:255',
                'city' => 'sometimes|required|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'sometimes|required|string|max:100',
                'postal_code' => 'sometimes|required|string|max:20',
                'tax_id' => 'nullable|string|max:50',
                'client_references' => 'nullable|array',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                DB::beginTransaction();

                // Mise à jour du type de client
                $oldType = $client->type;
                $newType = $request->type ?? $oldType;

                // Mettre à jour les informations du client
                $client->fill($request->only([
                    'name',
                    'type',
                    'email',
                    'phone',
                    'address',
                    'city',
                    'state',
                    'country',
                    'postal_code',
                    'tax_id',
                    'client_references',
                    'is_active'
                ]));

                $client->save();

                // Si le type a changé de customer à carrier, créer un compte de commission
                if ($oldType !== 'carrier' && $newType === 'carrier' && !$client->commissionAccount) {
                    CommissionAccount::create([
                        'client_id' => $client->id,
                        'balance' => 0,
                        'currency' => 'XOF',
                        'is_active' => true,
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Client mis à jour avec succès',
                    'data' => $client->fresh(['commissionAccount'])
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour du client',
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Supprimer un client.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);

            // Vérifier s'il y a des factures ou paiements liés à ce client
            if ($client->invoices()->count() > 0 || $client->payments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce client car il possède des factures ou des paiements associés'
                ], 409);
            }

            // Supprimer le compte de commission si c'est un transporteur
            if ($client->commissionAccount) {
                $client->commissionAccount->delete();
            }

            // Supprimer le client (soft delete)
            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Client non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mettre à jour ou créer le profil client de l'utilisateur connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserProfile(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:customer,carrier',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'client_references' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Récupérer l'utilisateur connecté
        $user = $request->user();

        // Créer ou mettre à jour le profil client
        try {
            DB::beginTransaction();

            // Rechercher un client existant pour cet utilisateur
            $client = Client::where('email', $user->email)->first();

            if (!$client) {
                // Créer un nouveau profil client
                $client = new Client();
                $client->email = $user->email;
            }

            // Mettre à jour les informations du client
            $client->name = $request->name;
            $client->type = $request->type;
            $client->phone = $request->phone;
            $client->address = $request->address;
            $client->city = $request->city;
            $client->state = $request->state;
            $client->country = $request->country;
            $client->postal_code = $request->postal_code;
            $client->tax_id = $request->tax_id;
            $client->client_references = $request->client_references;
            $client->is_active = true;

            $client->save();

            // Si c'est un transporteur, créer un compte de commission si nécessaire
            if ($client->type === 'carrier' && !$client->commissionAccount) {
                $client->commissionAccount()->create([
                    'balance' => 0,
                    'currency' => 'XOF',
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil client mis à jour avec succès',
                'data' => [
                    'client' => $client->fresh(['commissionAccount'])
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le profil client de l'utilisateur connecté.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUserProfile(Request $request)
    {
        $user = $request->user();
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Profil client non trouvé',
                'profile_status' => 'incomplete'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'client' => $client->load(['commissionAccount']),
                'profile_status' => 'complete'
            ]
        ]);
    }

    /**
     * Vérifier si l'utilisateur a complété son profil.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkProfileStatus(Request $request)
    {
        $user = $request->user();
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json([
                'success' => true,
                'profile_status' => 'incomplete'
            ]);
        }

        // Vérifier si les champs obligatoires sont remplis
        $requiredFields = ['name', 'type', 'phone', 'address', 'city', 'country', 'postal_code'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (empty($client->$field)) {
                $missingFields[] = $field;
            }
        }

        if (count($missingFields) > 0) {
            return response()->json([
                'success' => true,
                'profile_status' => 'incomplete',
                'missing_fields' => $missingFields
            ]);
        }

        return response()->json([
            'success' => true,
            'profile_status' => 'complete'
        ]);
    }

    /**
     * Obtenir la liste des transporteurs (clients de type 'carrier').
     *
     * @return \Illuminate\Http\Response
     */
    public function getCarriers()
    {
        $carriers = Client::where('type', 'carrier')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $carriers
        ]);
    }

    /**
     * Obtenir la liste des clients (clients de type 'customer').
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomers()
    {
        $customers = Client::where('type', 'customer')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }
}
