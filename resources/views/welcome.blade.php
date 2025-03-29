<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HPay - Solution de paiement pour compagnies maritimes</title>
    <meta name="description" content="HPay - Plateforme innovante de gestion des paiements pour les compagnies maritimes">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        secondary: {
                            800: '#093461',
                            900: '#052244',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans text-gray-800">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-6 flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/hpay-full-logo.svg') }}" alt="HPay Logo" class="h-10">
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="#features" class="font-medium text-gray-700 hover:text-primary-600 transition-colors">Fonctionnalités</a>
                <a href="#about" class="font-medium text-gray-700 hover:text-primary-600 transition-colors">À propos</a>
                <a href="#contact" class="font-medium text-gray-700 hover:text-primary-600 transition-colors">Contact</a>
            </nav>
            <div class="md:hidden">
                <button id="menu-toggle" class="text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden px-4 py-4 bg-white border-t border-gray-100">
            <a href="#features" class="block py-2 font-medium text-gray-700 hover:text-primary-600">Fonctionnalités</a>
            <a href="#about" class="block py-2 font-medium text-gray-700 hover:text-primary-600">À propos</a>
            <a href="#contact" class="block py-2 font-medium text-gray-700 hover:text-primary-600">Contact</a>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-secondary-800 to-secondary-900 text-white">
            <div class="container mx-auto px-4 py-16 md:py-24">
                <div class="md:flex md:items-center md:space-x-12">
                    <div class="md:w-1/2 mb-10 md:mb-0">
                        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight mb-4">
                            Simplifiez vos paiements maritimes avec HPay
                        </h1>
                        <p class="text-lg md:text-xl text-gray-200 mb-8">
                            Notre plateforme révolutionne la gestion des paiements pour les compagnies maritimes et leurs clients.
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="#contact" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition-colors text-center">
                                Commencer maintenant
                            </a>
                            <a href="#features" class="inline-block bg-white text-secondary-800 font-medium py-3 px-6 rounded-lg hover:bg-gray-100 transition-colors text-center">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                    <div class="md:w-1/2">
                        <img src="https://plus.unsplash.com/premium_photo-1661963511722-3bed499c9459?q=80&w=3271&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                            alt="Cargo maritime" class="rounded-lg shadow-lg w-full">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-16 md:py-24 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-2xl md:text-3xl font-bold text-secondary-800 mb-4">Fonctionnalités principales</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        Découvrez comment HPay transforme la gestion des paiements avec ses fonctionnalités innovantes.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-secondary-800 mb-3">Paiement Sécurisé</h3>
                        <p class="text-gray-600">
                            Transactions hautement sécurisées avec protection des données sensibles et conformité aux normes internationales.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-secondary-800 mb-3">Gestion des Factures</h3>
                        <p class="text-gray-600">
                            Création, suivi et gestion simplifiés des factures maritimes en un seul endroit, accessible à tout moment.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-secondary-800 mb-3">Intégration Facile</h3>
                        <p class="text-gray-600">
                            S'intègre parfaitement avec les systèmes existants des compagnies maritimes, sans perturber vos opérations.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-secondary-800 mb-3">Analyses Détaillées</h3>
                        <p class="text-gray-600">
                            Rapports et analyses personnalisés pour suivre les performances et prendre des décisions informées.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-secondary-800 mb-3">Traitement Rapide</h3>
                        <p class="text-gray-600">
                            Accélérez vos paiements et réduisez les délais de traitement grâce à notre système automatisé.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="bg-gray-50 rounded-xl p-8 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-primary-100 rounded-lg flex items-center justify-center text-primary-600 mb-5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-secondary-800 mb-3">Support 24/7</h3>
                        <p class="text-gray-600">
                            Une équipe dédiée disponible à tout moment pour vous aider et répondre à vos questions.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-16 md:py-24 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="md:flex md:items-center md:space-x-12">
                    <div class="md:w-1/2 mb-10 md:mb-0">
                        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
                            alt="Équipe HPay" class="rounded-lg shadow-lg w-full">
                    </div>
                    <div class="md:w-1/2">
                        <h2 class="text-2xl md:text-3xl font-bold text-secondary-800 mb-6">À propos de HPay</h2>
                        <p class="text-gray-600 mb-6">
                            Fondée en 2023, HPay est née de la vision d'un groupe d'experts en technologie financière et en logistique maritime.
                            Nous avons identifié un besoin critique dans le secteur maritime : une solution de paiement moderne,
                            sécurisée et spécifiquement adaptée aux défis uniques de cette industrie.
                        </p>
                        <p class="text-gray-600 mb-6">
                            Notre mission est de simplifier les transactions financières entre les compagnies maritimes et leurs clients,
                            tout en offrant une transparence totale et une sécurité de premier ordre. Nous croyons fermement que
                            la technologie peut transformer positivement le secteur maritime, et HPay est notre contribution à cette transformation.
                        </p>
                        <p class="text-gray-600">
                            Aujourd'hui, nous sommes fiers de servir plus de 20 compagnies maritimes majeures à travers l'Afrique,
                            traitant mensuellement des milliers de transactions et contribuant à l'efficacité de l'industrie maritime.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="bg-white py-16 md:py-24">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-2xl md:text-3xl font-bold text-secondary-800 mb-4">Ce que nos clients disent</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        Des compagnies maritimes du monde entier font confiance à HPay pour leurs solutions de paiement.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="bg-gray-50 p-8 rounded-xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="text-primary-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-6">
                            "HPay a complètement transformé notre processus de facturation. Nous avons réduit nos délais de paiement
                            de 45 à 7 jours en moyenne. Une solution indispensable dans notre secteur."
                        </p>
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full bg-secondary-800 flex items-center justify-center text-white font-medium">
                                AM
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-secondary-800">Amadou Mbaye</p>
                                <p class="text-sm text-gray-500">Directeur Financier, MSC Sénégal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-gray-50 p-8 rounded-xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="text-primary-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-6">
                            "La sécurité et la fiabilité de HPay sont exceptionnelles. Nos clients apprécient la transparence
                            et nous bénéficions d'un système qui s'intègre parfaitement à notre infrastructure existante."
                        </p>
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full bg-secondary-800 flex items-center justify-center text-white font-medium">
                                FN
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-secondary-800">Fatou Ndiaye</p>
                                <p class="text-sm text-gray-500">Directrice Technique, Bolloré Logistics</p>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-gray-50 p-8 rounded-xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="text-primary-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-6">
                            "Les rapports détaillés de HPay nous ont permis d'optimiser nos flux de trésorerie et d'identifier
                            des opportunités d'amélioration que nous n'aurions jamais remarquées autrement."
                        </p>
                        <div class="flex items-center">
                            <div class="h-12 w-12 rounded-full bg-secondary-800 flex items-center justify-center text-white font-medium">
                                OD
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-secondary-800">Ousmane Diallo</p>
                                <p class="text-sm text-gray-500">CFO, Maersk West Africa</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-16 md:py-24 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-2xl md:text-3xl font-bold text-secondary-800 mb-4">Contactez-nous</h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        Vous souhaitez en savoir plus sur HPay ? Remplissez le formulaire ci-dessous et notre équipe vous contactera rapidement.
                    </p>
                </div>

                <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="md:flex">
                        <div class="md:w-1/2 p-8">
                            <h3 class="text-xl font-semibold text-secondary-800 mb-4">Envoyez-nous un message</h3>
                            <form>
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                                    <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                <div class="mb-4">
                                    <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Entreprise</label>
                                    <input type="text" id="company" name="company" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                </div>
                                <div class="mb-6">
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                    <textarea id="message" name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    Envoyer
                                </button>
                            </form>
                        </div>
                        <div class="md:w-1/2 bg-gradient-to-br from-primary-500 to-primary-700 p-8 text-white">
                            <h3 class="text-xl font-semibold mb-4">Informations de contact</h3>
                            <p class="mb-6">
                                N'hésitez pas à nous contacter directement à l'aide des informations ci-dessous.
                            </p>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">Adresse</p>
                                        <p>123 Boulevard de la Corniche</p>
                                        <p>Dakar, Sénégal</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">Téléphone</p>
                                        <p>+221 33 123 45 67</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">Email</p>
                                        <p>contact@hpay.com</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-medium">Horaires d'ouverture</p>
                                        <p>Lundi - Vendredi: 8h à 18h</p>
                                        <p>Samedi: 9h à 13h</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8">
                                <h4 class="text-lg font-medium mb-4">Suivez-nous</h4>
                                <div class="flex space-x-4">
                                    <a href="#" class="text-white hover:text-gray-200 transition-colors">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                                    </a>
                                    <a href="#" class="text-white hover:text-gray-200 transition-colors">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124-4.09-.193-7.715-2.157-10.141-5.126-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 14-7.503 14-14v-.617c.961-.689 1.8-1.56 2.46-2.548z"/></svg>
                                    </a>
                                    <a href="#" class="text-white hover:text-gray-200 transition-colors">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                    <a href="#" class="text-white hover:text-gray-200 transition-colors">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-12 md:py-16">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-6">Prêt à transformer votre gestion des paiements ?</h2>
                <p class="text-lg text-white/90 max-w-3xl mx-auto mb-8">
                    Rejoignez les compagnies maritimes qui font confiance à HPay pour simplifier leurs transactions financières et améliorer leur efficacité opérationnelle.
                </p>
                <a href="#contact" class="inline-block bg-white text-primary-700 font-medium py-3 px-8 rounded-lg hover:bg-gray-100 transition-colors">
                    Commencer maintenant
                </a>
            </div>
        </section>
    </main>

    <footer class="bg-secondary-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <img src="{{ asset('images/hpay-full-logo.svg') }}" alt="HPay Logo" class="h-10 mb-6">
                    <p class="text-gray-300 mb-6">
                        HPay simplifie les paiements pour les compagnies maritimes et leurs clients grâce à une plateforme sécurisée et intuitive.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.954 4.569c-.885.389-1.83.654-2.825.775 1.014-.611 1.794-1.574 2.163-2.723-.951.555-2.005.959-3.127 1.184-.896-.959-2.173-1.559-3.591-1.559-2.717 0-4.92 2.203-4.92 4.917 0 .39.045.765.127 1.124-4.09-.193-7.715-2.157-10.141-5.126-.427.722-.666 1.561-.666 2.475 0 1.71.87 3.213 2.188 4.096-.807-.026-1.566-.248-2.228-.616v.061c0 2.385 1.693 4.374 3.946 4.827-.413.111-.849.171-1.296.171-.314 0-.615-.03-.916-.086.631 1.953 2.445 3.377 4.604 3.417-1.68 1.319-3.809 2.105-6.102 2.105-.39 0-.779-.023-1.17-.067 2.189 1.394 4.768 2.209 7.557 2.209 9.054 0 14-7.503 14-14v-.617c.961-.689 1.8-1.56 2.46-2.548z"/></svg>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-white transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Liens rapides</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition-colors">Accueil</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors">Fonctionnalités</a></li>
                        <li><a href="#about" class="hover:text-white transition-colors">À propos</a></li>
                        <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Services</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition-colors">Paiements sécurisés</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Gestion des factures</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Rapports financiers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">API d'intégration</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Support premium</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Légal</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="#" class="hover:text-white transition-colors">Conditions d'utilisation</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Politique de confidentialité</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Politique de cookies</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Conformité RGPD</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Mentions légales</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">&copy; 2025 HPay. Tous droits réservés.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Termes</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Confidentialité</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Menu mobile toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });

            // Close mobile menu when clicking on a link
            const mobileLinks = mobileMenu.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                });
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>