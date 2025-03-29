<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    /**
     * Constructor pour injecter le service de notification
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Afficher toutes les notifications de l'utilisateur connecté
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'perPage' => 'nullable|integer|min:1|max:100',
            'unread' => 'nullable',
            'page' => 'nullable|integer|min:1'
        ]);

        $user = Auth::user();
        $perPage = $request->input('perPage', 15);
        $onlyUnread = $request->boolean('unread', false);

        $notifications = $this->notificationService->getUserNotifications($user, $onlyUnread, $perPage);

        return response()->json($notifications);
    }

    /**
     * Marquer une notification comme lue
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        // Vérifier que l'utilisateur connecté est le propriétaire de la notification
        if ($notification->notifiable_type === 'App\\Models\\User' &&
            $notification->notifiable_id === Auth::id()) {

            $this->notificationService->markAsRead($notification);

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vous n\'avez pas le droit de modifier cette notification'
        ], 403);
    }

    /**
     * Marquer toutes les notifications de l'utilisateur connecté comme lues
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => "$count notifications marquées comme lues"
        ]);
    }

    /**
     * Supprimer une notification
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        // Vérifier que l'utilisateur connecté est le propriétaire de la notification
        if ($notification->notifiable_type === 'App\\Models\\User' &&
            $notification->notifiable_id === Auth::id()) {

            $this->notificationService->deleteNotification($notification);

            return response()->json([
                'success' => true,
                'message' => 'Notification supprimée avec succès'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vous n\'avez pas le droit de supprimer cette notification'
        ], 403);
    }

    /**
     * Récupérer le nombre de notifications non lues
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Envoyer une notification au modèle spécifié
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'message' => 'required|string',
            'notifiable_type' => 'required|string',
            'notifiable_id' => 'required|string',
            'data' => 'nullable|array'
        ]);

        $notifiable = $request->input('notifiable_type')::findOrFail($request->input('notifiable_id'));

        $notification = $this->notificationService->sendNotification(
            $notifiable,
            $request->input('type'),
            $request->input('message'),
            $request->input('data', [])
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification envoyée avec succès',
            'notification' => $notification
        ]);
    }
}
