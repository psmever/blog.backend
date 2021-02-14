<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class AdminNotification extends Notification
{
    use Queueable;

    private $task;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    public function toSlack($notifiable)
    {

        $slackMessage = new \stdClass();
        $slackMessage->env = env('APP_ENV');


        if($this->task->type == 'notice') {
            $slackMessage->title = ":: 알림 ::";
            $slackMessage->hex = '#663399';
        } else if($this->task->type == 'exception') {
            $slackMessage->title = ":: exception ::";
            $slackMessage->hex = '#F0F8FF';
        }

        $slackMessage->message = "{$this->task->message}";

        return (new SlackMessage)->from('BlogApiServer', ':flushed:')->warning()->to('#blog_backend_notifications')->attachment(function ($attachment) use ($slackMessage) {
            $attachment->title(" {$slackMessage->title}")->content("({$slackMessage->env}) : {$slackMessage->message}")->color($slackMessage->hex);
        });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
