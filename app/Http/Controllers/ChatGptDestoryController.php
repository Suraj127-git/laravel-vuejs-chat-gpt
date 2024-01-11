<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Http\RedirectResponse;

class ChatGptDestoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Chat $chat): RedirectResponse
    {
        $chat->delete();
        return to_route('chat.show');
    }
}
