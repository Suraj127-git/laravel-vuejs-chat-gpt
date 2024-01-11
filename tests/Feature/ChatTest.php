<?php

use App\Models\Chat;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class); // Assuming you have a seeder for users
    }

    /**
     * Tests displaying the chat page for an existing chat.
     */
    public function test_can_view_existing_chat()
    {
        // Create a chat
        $chat = Chat::factory()->create();

        // Debugging: Output the chat and associated user
        dd($chat, $chat->user);

        // Act as a user and visit the chat page
        $response = $this->actingAs($chat->user, 'web')->get(route('chat.show', $chat->id));

        // Assert chat data is displayed
        $response->assertInertia(fn ($page) => $page->has('chat', fn ($chat) => $chat->id === $chat->id));
    }

    /**
     * Tests creating a new chat and sending a prompt.
     */
    public function test_can_create_chat_and_send_prompt()
    {
        // Act as a user and send a prompt
        $prompt = 'What is the meaning of life?';
        $response = $this->actingAs(User::factory()->create())
            ->postJson(route('chat.store'), ['promt' => $prompt]);

        // Assert response status
        $response->assertStatus(200);

        // Assert response content and structure only if the response is 200
        if ($response->status() === 200) {
            $response->assertJsonStructure(['chat' => ['id', 'context']]);
            $response->assertJson(['chat' => ['context' => [['role' => 'assistant', 'content' => '...']]]]); // Replace '...' with expected response pattern
        }
    }

    // ... Add more tests for other features (e.g., deleting chats)
}
