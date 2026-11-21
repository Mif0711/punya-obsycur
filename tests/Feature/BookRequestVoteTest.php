<?php

namespace Tests\Feature;

use App\Models\BookRequest;
use App\Models\BookRequestVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRequestVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_sees_book_requests_submitted_by_other_members(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $bookRequest = BookRequest::factory()->create([
            'user_id' => $owner->id,
            'title' => 'Judul Bersama',
        ]);

        $this->actingAs($other)
            ->get(route('member.book-requests.index'))
            ->assertOk()
            ->assertSee('Judul Bersama')
            ->assertSee('Diajukan oleh '.$owner->name);
    }

    public function test_member_can_vote_with_a_reason(): void
    {
        $bookRequest = BookRequest::factory()->create();
        $voter = User::factory()->create();

        $this->actingAs($voter)
            ->post(route('member.book-requests.vote', $bookRequest), [
                'comment' => 'Saya ingin buku ini untuk penelitian sastra.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('book_request_votes', [
            'user_id' => $voter->id,
            'book_request_id' => $bookRequest->id,
            'comment' => 'Saya ingin buku ini untuk penelitian sastra.',
        ]);
    }

    public function test_vote_requires_a_reason(): void
    {
        $bookRequest = BookRequest::factory()->create();
        $voter = User::factory()->create();

        $this->actingAs($voter)
            ->post(route('member.book-requests.vote', $bookRequest), ['comment' => ''])
            ->assertSessionHasErrors(['comment' => 'Alasan vote wajib diisi.']);

        $this->assertDatabaseMissing('book_request_votes', [
            'user_id' => $voter->id,
            'book_request_id' => $bookRequest->id,
        ]);
    }

    public function test_voting_again_cancels_the_existing_vote(): void
    {
        $bookRequest = BookRequest::factory()->create();
        $voter = User::factory()->create();
        BookRequestVote::factory()->create([
            'book_request_id' => $bookRequest->id,
            'user_id' => $voter->id,
        ]);

        $this->actingAs($voter)
            ->post(route('member.book-requests.vote', $bookRequest))
            ->assertRedirect();

        $this->assertDatabaseMissing('book_request_votes', [
            'user_id' => $voter->id,
            'book_request_id' => $bookRequest->id,
        ]);
    }

    public function test_owner_delete_reduces_one_vote_and_keeps_the_request(): void
    {
        $owner = User::factory()->create();
        $voter = User::factory()->create();
        $bookRequest = BookRequest::factory()->create(['user_id' => $owner->id]);
        BookRequestVote::factory()->create([
            'book_request_id' => $bookRequest->id,
            'user_id' => $voter->id,
        ]);

        $this->actingAs($owner)
            ->delete(route('member.book-requests.destroy', $bookRequest))
            ->assertRedirect();

        $this->assertDatabaseHas('book_requests', ['id' => $bookRequest->id]);
        $this->assertDatabaseMissing('book_request_votes', ['book_request_id' => $bookRequest->id]);
    }

    public function test_owner_delete_removes_a_request_without_votes(): void
    {
        $owner = User::factory()->create();
        $bookRequest = BookRequest::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->delete(route('member.book-requests.destroy', $bookRequest))
            ->assertRedirect();

        $this->assertDatabaseMissing('book_requests', ['id' => $bookRequest->id]);
    }

    public function test_member_cannot_delete_another_members_request(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $bookRequest = BookRequest::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->delete(route('member.book-requests.destroy', $bookRequest))
            ->assertForbidden();

        $this->assertDatabaseHas('book_requests', ['id' => $bookRequest->id]);
    }
}
