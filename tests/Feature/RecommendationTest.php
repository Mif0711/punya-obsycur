<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Loan;
use App\Models\SearchLog;
use App\Models\User;
use App\Services\BookRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_books_from_the_same_genre_as_reading_history_are_recommended(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $history = Book::factory()->create([
            'genre_id' => $genre->id,
            'average_rating' => 3.0,
        ]);

        Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $history->id,
            'status' => Loan::STATUS_RETURNED,
        ]);

        $similar = Book::factory()->create([
            'genre_id' => $genre->id,
            'average_rating' => 3.5,
        ]);

        $recommendations = app(BookRecommendationService::class)->recommend($user, 20);

        $this->assertTrue($recommendations->contains('id', $similar->id));
        $this->assertFalse($recommendations->contains('id', $history->id));
        $this->assertContains(
            'Sesuai riwayat bacaan Anda',
            $recommendations->firstWhere('id', $similar->id)->recommendation_reasons,
        );
    }

    public function test_search_history_influences_recommendations(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'title' => 'Filosofi Teras',
            'average_rating' => 3.0,
        ]);

        SearchLog::factory()->create([
            'user_id' => $user->id,
            'query' => 'Filosofi',
        ]);

        $recommendations = app(BookRecommendationService::class)->recommend($user, 10);

        $this->assertTrue($recommendations->contains('id', $book->id));
        $this->assertContains(
            'Sesuai pencarian Anda',
            $recommendations->firstWhere('id', $book->id)->recommendation_reasons,
        );
    }

    public function test_books_read_by_peers_with_similar_taste_are_recommended(): void
    {
        $user = User::factory()->create();
        $peer = User::factory()->create();

        $shared = Book::factory()->create(['average_rating' => 3.0]);
        $peerOnly = Book::factory()->create([
            'genre_id' => Genre::factory()->create()->id,
            'average_rating' => 3.0,
        ]);

        Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $shared->id,
            'status' => Loan::STATUS_RETURNED,
        ]);

        Loan::factory()->create([
            'user_id' => $peer->id,
            'book_id' => $shared->id,
            'status' => Loan::STATUS_RETURNED,
        ]);

        Loan::factory()->create([
            'user_id' => $peer->id,
            'book_id' => $peerOnly->id,
            'status' => Loan::STATUS_RETURNED,
        ]);

        $recommendations = app(BookRecommendationService::class)->recommend($user, 20);
        $match = $recommendations->firstWhere('id', $peerOnly->id);

        $this->assertNotNull($match);
        $this->assertContains('Disukai pembaca dengan selera serupa', $match->recommendation_reasons);
    }

    public function test_editor_picks_and_award_winners_are_recommended_for_new_members(): void
    {
        $user = User::factory()->create();

        $editorPick = Book::factory()->editorPick()->create(['average_rating' => 3.0]);
        $awardWinner = Book::factory()->awardWinner()->create(['average_rating' => 3.0]);

        $recommendations = app(BookRecommendationService::class)->recommend($user, 20);

        $editorMatch = $recommendations->firstWhere('id', $editorPick->id);
        $awardMatch = $recommendations->firstWhere('id', $awardWinner->id);

        $this->assertNotNull($editorMatch);
        $this->assertContains('Pilihan editor / pustakawan', $editorMatch->recommendation_reasons);

        $this->assertNotNull($awardMatch);
        $this->assertContains('Pemenang penghargaan sastra', $awardMatch->recommendation_reasons);
    }

    public function test_recommendations_page_renders_for_members(): void
    {
        $member = User::factory()->create();
        Book::factory()->editorPick()->create(['average_rating' => 3.0]);

        $this->actingAs($member)
            ->get(route('member.recommendations.index'))
            ->assertOk()
            ->assertSee('Rekomendasi untuk Anda')
            ->assertSee('Riwayat bacaan');
    }
}
