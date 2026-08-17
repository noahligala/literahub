<?php

namespace App\Jobs;

use App\Models\ReadingActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecordReadingActivity implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;


    public int $tries = 3;


    public int $timeout = 30;


    public int $maxExceptions = 3;


    /**
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public int $userId,
        public int $bookId,
        public string $eventType,

        public ?int $readerSessionId = null,
        public ?int $schoolId = null,
        public ?int $registeredDeviceId = null,
        public ?int $pageNumber = null,
        public ?string $ipAddress = null,
        public ?array $metadata = null,
        public ?string $occurredAt = null,
    ) {
        $this->onQueue(
            'reader-activity'
        );


        /*
         * Capture actual event time when the job is dispatched,
         * not several seconds later when the worker handles it.
         */

        $this->occurredAt ??=
            now()->toISOString();
    }


    /**
     * Execute the queued activity write.
     */
    public function handle(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Event Type
        |--------------------------------------------------------------------------
        */

        if (! $this->validEventType()) {
            throw new \InvalidArgumentException(
                "Invalid reader activity event type: {$this->eventType}"
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Page Number
        |--------------------------------------------------------------------------
        */

        if (
            $this->pageNumber !== null
            &&
            $this->pageNumber < 1
        ) {
            throw new \InvalidArgumentException(
                'Reading activity page number must be greater than zero.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Store Activity
        |--------------------------------------------------------------------------
        */

        ReadingActivity::query()
            ->create([
                'reader_session_id' =>
                    $this->readerSessionId,

                'user_id' =>
                    $this->userId,

                'book_id' =>
                    $this->bookId,

                'school_id' =>
                    $this->schoolId,

                'registered_device_id' =>
                    $this->registeredDeviceId,

                'page_number' =>
                    $this->pageNumber,

                'event_type' =>
                    $this->eventType,

                'ip_address' =>
                    $this->ipAddress,

                'metadata' =>
                    $this->metadata,

                'occurred_at' =>
                    Carbon::parse(
                        $this->occurredAt
                    ),
            ]);
    }


    /**
     * Reader event types currently supported.
     */
    private function validEventType(): bool
    {
        return in_array(
            $this->eventType,
            [
                /*
                 * Reader lifecycle.
                 */
                'reader_started',
                'reader_ended',
                'reader_resumed',

                /*
                 * Page behaviour.
                 */
                'page_view',
                'page_previous',
                'page_next',

                /*
                 * Reader features.
                 */
                'bookmark',
                'bookmark_removed',

                /*
                 * Library behaviour.
                 */
                'borrow',
                'return',

                /*
                 * Security / entitlement outcomes.
                 */
                'access_denied',
                'session_expired',
                'session_revoked',

                /*
                 * Optional future events.
                 */
                'search',
                'chapter_opened',
            ],
            true
        );
    }


    /**
     * Handle permanent queue failure.
     *
     * We intentionally do not throw anything else from here because
     * reader analytics must never break the reader itself.
     */
    public function failed(
        ?Throwable $exception
    ): void {
        Log::warning(
            'Unable to record LiteraHub reading activity.',
            [
                'user_id' =>
                    $this->userId,

                'book_id' =>
                    $this->bookId,

                'reader_session_id' =>
                    $this->readerSessionId,

                'event_type' =>
                    $this->eventType,

                'page_number' =>
                    $this->pageNumber,

                'message' =>
                    $exception?->getMessage(),
            ]
        );
    }


    public function backoff(): array
    {
        return [
            5,
            30,
            120,
        ];
    }
}