<?php

namespace App\Http\Controllers;

use App\Models\ShippingCompany;
use Illuminate\Http\Request;

class ShippingCompanyController extends Controller
{
    /**
     * Affiche la liste des compagnies maritimes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 15);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $onlyActive = $request->boolean('active', false);

        $query = ShippingCompany::query();

        // Filtrer par statut actif si demandé
        if ($onlyActive) {
            $query->where('is_active', true);
        }

        // Filtrer par terme de recherche si fourni
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Récupérer les résultats paginés
        $shippingCompanies = $query->orderBy('created_at', 'desc')
            ->simplePaginate($perPage, ['*'], 'page', $page);

        return $shippingCompanies;
    }
}