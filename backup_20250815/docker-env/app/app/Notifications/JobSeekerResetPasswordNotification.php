<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobSeekerResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.（🔥 修正版）
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // 🔥 修正: ルート名を正しく指定
        $url = url(route('job_seeker.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
                    ->subject('【求人応募ポータル】パスワードリセットのご案内')
                    ->greeting('いつもご利用いただき、ありがとうございます。')
                    ->line('お客様のアカウントでパスワードリセットのリクエストを受信いたしました。')
                    ->line('以下のボタンをクリックして、新しいパスワードを設定してください。')
                    ->action('パスワードをリセット', $url)
                    ->line('このリンクは60分後に有効期限が切れます。')
                    ->line('パスワードリセットをご依頼でない場合は、このメールを無視してください。')
                    ->line('セキュリティ上の理由により、このリンクは一度のみ使用可能です。')
                    ->salutation('求人応募ポータル運営チーム');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'token' => $this->token,
            'email' => $notifiable->email,
            'expires_at' => now()->addMinutes(60),
        ];
    }
}