<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Récupérer les notifications d'un utilisateur
     *
     * @param User|Authenticatable $user L'utilisateur
     * @param bool $onlyUnread Ne récupérer que les notifications non lues
     * @param int $perPage Nombre d'éléments par page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserNotifications($user, bool $onlyUnread = false, int $perPage = 15)
    {
        $query = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($onlyUnread) {
            $query->whereNull('read_at');
        }

        return $query->simplePaginate($perPage);
    }

    /**
     * Marquer une notification comme lue
     *
     * @param Notification $notification
     * @return Notification
     */
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        return $notification;
    }

    /**
     * Marquer toutes les notifications d'un utilisateur comme lues
     *
     * @param User|Authenticatable $user
     * @return int Nombre de notifications marquées comme lues
     */
    public function markAllAsRead($user)
    {
        $query = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at');

        $count = $query->count();

        $query->update(['read_at' => now()]);

        return $count;
    }

    /**
     * Supprimer une notification
     *
     * @param Notification $notification
     * @return bool
     */
    public function deleteNotification(Notification $notification)
    {
        return $notification->delete();
    }

    /**
     * Récupérer le nombre de notifications non lues
     *
     * @param User|Authenticatable $user
     * @return int
     */
    public function getUnreadCount($user)
    {
        return Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Envoyer une notification
     *
     * @param Model $notifiable Modèle qui recevra la notification
     * @param string $type Type de notification
     * @param string $message Message de la notification
     * @param array $data Données supplémentaires
     * @param User|null $user Utilisateur qui envoie la notification (facultatif)
     * @return Notification
     */
    public function sendNotification(Model $notifiable, string $type, string $message, array $data = [], ?User $user = null)
    {
        try {
            $notification = new Notification();
            $notification->notifiable_type = get_class($notifiable);
            $notification->notifiable_id = $notifiable->id;
            $notification->type = $type;
            $notification->message = $message;
            $notification->data = $data;
            $notification->user_id = $user ? $user->id : null;
            $notification->save();

            // Ici, vous pourriez ajouter une logique pour notifier en temps réel
            // Par exemple, avec des WebSockets ou des événements Laravel

            return $notification;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Envoyer une notification à plusieurs destinataires
     *
     * @param array $notifiables Tableau de modèles qui recevront la notification
     * @param string $type Type de notification
     * @param string $message Message de la notification
     * @param array $data Données supplémentaires
     * @param User|null $user Utilisateur qui envoie la notification (facultatif)
     * @return array Tableau des notifications créées
     */
    public function sendBulkNotifications(array $notifiables, string $type, string $message, array $data = [], User $user = null)
    {
        $notifications = [];

        foreach ($notifiables as $notifiable) {
            try {
                $notifications[] = $this->sendNotification($notifiable, $type, $message, $data, $user);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi groupé de notifications: ' . $e->getMessage());
                // On continue malgré l'erreur pour traiter les autres notifications
            }
        }

        return $notifications;
    }

    /**
     * Envoyer une notification système à tous les utilisateurs ou à un groupe spécifique
     *
     * @param string $type Type de notification
     * @param string $message Message de la notification
     * @param array $data Données supplémentaires
     * @param array $filters Filtres pour sélectionner les utilisateurs
     * @return int Nombre de notifications envoyées
     */
    public function sendSystemNotification(string $type, string $message, array $data = [], array $filters = [])
    {
        $query = User::query();

        // Appliquer les filtres si nécessaire
        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                $query->where($column, $value);
            }
        }

        $users = $query->get();
        $count = 0;

        foreach ($users as $user) {
            try {
                $this->sendNotification($user, $type, $message, $data);
                $count++;
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de notification système: ' . $e->getMessage());
                // On continue malgré l'erreur
            }
        }

        return $count;
    }


}