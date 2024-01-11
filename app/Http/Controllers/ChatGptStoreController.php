<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatRequest;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class ChatGptStoreController extends Controller
{
    public function __invoke(StoreChatRequest $request, ?string $id = null)
    {
        $messages = [];
        if ($id) {
            $chat = Chat::findOrFail($id);
            $messages = $chat->context;
        }
        $messages[] = ['role' => 'user', 'content' => $request->input('promt')];
        // dd($messages);
        // $yourApiKey = getenv('OPENAI_API_KEY');
        // $OpenAI_client = OpenAI::client($yourApiKey);
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 0.7,
            // "stream" => true,
            // 'max_tokens' => 600
        ]);
        $messages[] = ['role' => 'assistant', 'content' => $response->choices[0]->message->content];
        try {
            $chat = Chat::updateOrCreate(
                [
                    'id' => $id,
                    'user_id' => Auth::id(),
                ],
                [
                    'context' => $messages,
                ]
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            echo 'Error: '.$e->getMessage();
            dd('Die');
        }

        return redirect()->route('chat.show', [$chat->id]);
    }
}
