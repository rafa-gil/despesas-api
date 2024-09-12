<?php

namespace App\Notifications;

use App\Models\{Expense, User};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpenseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $userId, public int $expenseId)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user    = User::query()->findOrFail($this->userId);
        $expense = Expense::query()->findOrFail($this->expenseId);

        return (new MailMessage())
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Despesa Cadastrada')
            ->greeting('Olá ' . $user->name)
            ->line('Foi criada uma nova despesa para o usuário.')
            ->line('Despesa: ' . $expense->value)
            ->line('Data: ' . $expense->date)
            ->line('Descrição: ' . $expense->description)
            ->salutation('Atenciosamente, ' . config('app.name'));
    }
}
