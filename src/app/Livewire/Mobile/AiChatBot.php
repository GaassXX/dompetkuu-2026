<?php

namespace App\Livewire\Mobile;

use App\Services\TransactionParserService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AiChatBot extends Component
{
    public string $message = '';
    public array $chatHistory = [];

    public function mount(): void
    {
        $this->chatHistory[] = [
            'from' => 'bot',
            'text' => 'Halo! Saya asisten keuangan Anda. Coba ketik: "Makan siang 50rb" atau "Gaji freelance 2jt".',
        ];
    }

    public function sendMessage(TransactionParserService $parser): void
    {
        if (trim($this->message) === '') return;

        $this->chatHistory[] = ['from' => 'user', 'text' => $this->message];

        $userId = Auth::id() ?? 1; // sementara fallback ke user 1 selama belum ada auth

        $parsed = $parser->parse($this->message, $userId);

        if ($parsed) {
            $parser->save($parsed, $userId);

            $label = $parsed['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';
            $this->chatHistory[] = [
                'from' => 'bot',
                'text' => "Berhasil dicatat ✅\n{$label}: {$parsed['description']}\nJumlah: Rp " . number_format($parsed['amount'], 0, ',', '.'),
            ];
        } else {
            $this->chatHistory[] = [
                'from' => 'bot',
                'text' => 'Maaf, saya tidak bisa mendeteksi nominal dari pesan itu. Coba format: "Makan siang 50rb".',
            ];
        }

        $this->message = '';
    }

    public function render()
    {
        return view('livewire.mobile.ai-chat-bot')->layout('layouts.mobile');
    }
}
