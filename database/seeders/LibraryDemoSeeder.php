<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookAccessRequest;
use App\Models\BookBookmark;
use App\Models\BookBorrowing;
use App\Models\Publisher;
use App\Models\School;
use App\Models\SchoolBookLicense;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class LibraryDemoSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | Demo Password
    |--------------------------------------------------------------------------
    */

    private string $password = 'Password123!';


    /*
    |--------------------------------------------------------------------------
    | Run Seeder
    |--------------------------------------------------------------------------
    */

    public function run(): void
    {
        $this->command?->info(
            'Creating LiteraHub demo library data...'
        );

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $this->ensureRoles();


        DB::transaction(function () {

            /*
            |--------------------------------------------------------------------------
            | Schools
            |--------------------------------------------------------------------------
            */

            [$schoolA, $schoolB] =
                $this->createSchools();


            /*
            |--------------------------------------------------------------------------
            | Platform Users
            |--------------------------------------------------------------------------
            */

            $platformUsers =
                $this->createPlatformUsers();


            /*
            |--------------------------------------------------------------------------
            | School Users
            |--------------------------------------------------------------------------
            */

            $schoolAUsers =
                $this->createSchoolAUsers(
                    $schoolA
                );

            $schoolBUsers =
                $this->createSchoolBUsers(
                    $schoolB
                );


            /*
            |--------------------------------------------------------------------------
            | Classes / Streams
            |--------------------------------------------------------------------------
            */

            $schoolAStructure =
                $this->createSchoolAStructure(
                    $schoolA,
                    $schoolAUsers
                );

            $schoolBStructure =
                $this->createSchoolBStructure(
                    $schoolB,
                    $schoolBUsers
                );


            /*
            |--------------------------------------------------------------------------
            | Publishers / Authors
            |--------------------------------------------------------------------------
            */

            [$publishers, $authors] =
                $this->createPublishersAndAuthors(
                    $platformUsers
                );


            /*
            |--------------------------------------------------------------------------
            | Books
            |--------------------------------------------------------------------------
            */

            $books =
                $this->createBooks(
                    $publishers,
                    $authors,
                    $platformUsers
                );


            /*
            |--------------------------------------------------------------------------
            | Licences
            |--------------------------------------------------------------------------
            */

            $licenses =
                $this->createLicenses(
                    $schoolA,
                    $schoolB,
                    $books,
                    $publishers,
                    $authors,
                    $platformUsers
                );


            /*
            |--------------------------------------------------------------------------
            | Class Book Assignments
            |--------------------------------------------------------------------------
            */

            $this->assignBooksToClasses(
                $books,
                $schoolAStructure,
                $schoolBStructure,
                $schoolAUsers,
                $schoolBUsers
            );


            /*
            |--------------------------------------------------------------------------
            | Borrowings / Bookmarks
            |--------------------------------------------------------------------------
            */

            $this->createBorrowingsAndBookmarks(
                $schoolA,
                $books,
                $schoolAUsers
            );


            /*
            |--------------------------------------------------------------------------
            | Access Requests
            |--------------------------------------------------------------------------
            */

            $this->createAccessRequests(
                $schoolA,
                $books,
                $schoolAUsers
            );
        });


        $this->printDemoAccounts();
    }


    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    private function ensureRoles(): void
    {
        app(
            PermissionRegistrar::class
        )->forgetCachedPermissions();

        $roles = [
            'super_admin',
            'platform_admin',
            'content_manager',
            'author',
            'school_admin',
            'teacher',
            'student',
            'individual_subscriber',
            'finance',
            'support',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        app(
            PermissionRegistrar::class
        )->forgetCachedPermissions();
    }


   /*
|--------------------------------------------------------------------------
| Schools
|--------------------------------------------------------------------------
*/

private function createSchools(): array
{
    $schoolA = School::query()
        ->updateOrCreate(
            [
                'slug' => 'literahub-academy',
            ],
            [
                'name' => 'LiteraHub Academy',

                'slug' => 'literahub-academy',

                'status' => 'active',
            ]
        );


    $schoolB = School::query()
        ->updateOrCreate(
            [
                'slug' => 'greenfield-secondary-school',
            ],
            [
                'name' => 'Greenfield Secondary School',

                'slug' => 'greenfield-secondary-school',

                'status' => 'active',
            ]
        );


    return [
        $schoolA,
        $schoolB,
    ];
}


    /*
    |--------------------------------------------------------------------------
    | Platform Users
    |--------------------------------------------------------------------------
    */

    private function createPlatformUsers(): array
    {
        return [
            'super_admin' =>
                $this->user(
                    'Super Administrator',
                    'superadmin@literahub.test',
                    'super_admin'
                ),

            'platform_admin' =>
                $this->user(
                    'Platform Administrator',
                    'admin@literahub.test',
                    'platform_admin'
                ),

            'content_manager' =>
                $this->user(
                    'Content Manager',
                    'content@literahub.test',
                    'content_manager'
                ),

            'finance' =>
                $this->user(
                    'Finance Officer',
                    'finance@literahub.test',
                    'finance'
                ),

            'support' =>
                $this->user(
                    'Support Officer',
                    'support@literahub.test',
                    'support'
                ),

            /*
             * Authors become linked to Author profiles
             * later in the seeder.
             */

            'author_one' =>
                $this->user(
                    'Grace Wanjiku',
                    'grace.author@literahub.test',
                    'author'
                ),

            'author_two' =>
                $this->user(
                    'Daniel Kiptoo',
                    'daniel.author@literahub.test',
                    'author'
                ),

            'author_three' =>
                $this->user(
                    'Amina Hassan',
                    'amina.author@literahub.test',
                    'author'
                ),

            'individual' =>
                $this->user(
                    'Independent Reader',
                    'reader@literahub.test',
                    'individual_subscriber'
                ),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | School A Users
    |--------------------------------------------------------------------------
    */

    private function createSchoolAUsers(
        School $school
    ): array {
        $admin =
            $this->schoolUser(
                $school,
                'Noah School Admin',
                'schooladmin@literahub.test',
                'school_admin',
                'ADM-001'
            );

        $teacherOne =
            $this->schoolUser(
                $school,
                'Jane Literature Teacher',
                'teacher1@literahub.test',
                'teacher',
                'TCH-001'
            );

        $teacherTwo =
            $this->schoolUser(
                $school,
                'Peter English Teacher',
                'teacher2@literahub.test',
                'teacher',
                'TCH-002'
            );

        $studentOne =
            $this->schoolUser(
                $school,
                'Brian Kiprono',
                'student1@literahub.test',
                'student',
                'STD-001'
            );

        $studentTwo =
            $this->schoolUser(
                $school,
                'Faith Chebet',
                'student2@literahub.test',
                'student',
                'STD-002'
            );

        $studentThree =
            $this->schoolUser(
                $school,
                'Kevin Otieno',
                'student3@literahub.test',
                'student',
                'STD-003'
            );

        return compact(
            'admin',
            'teacherOne',
            'teacherTwo',
            'studentOne',
            'studentTwo',
            'studentThree'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | School B Users
    |--------------------------------------------------------------------------
    */

    private function createSchoolBUsers(
        School $school
    ): array {
        $admin =
            $this->schoolUser(
                $school,
                'Greenfield Administrator',
                'greenfield.admin@literahub.test',
                'school_admin',
                'GFA-001'
            );

        $teacher =
            $this->schoolUser(
                $school,
                'Greenfield Teacher',
                'greenfield.teacher@literahub.test',
                'teacher',
                'GFT-001'
            );

        $student =
            $this->schoolUser(
                $school,
                'Greenfield Student',
                'greenfield.student@literahub.test',
                'student',
                'GFS-001'
            );

        return compact(
            'admin',
            'teacher',
            'student'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | School A Classes
    |--------------------------------------------------------------------------
    */

    private function createSchoolAStructure(
        School $school,
        array $users
    ): array {
        $formOne =
            $school
                ->classes()
                ->updateOrCreate(
                    [
                        'code' =>
                            'F1-2026',
                    ],
                    [
                        'name' =>
                            'Form 1',

                        'level' =>
                            'Form 1',

                        'academic_year' =>
                            '2026',

                        'status' =>
                            'active',
                    ]
                );

        $formTwo =
            $school
                ->classes()
                ->updateOrCreate(
                    [
                        'code' =>
                            'F2-2026',
                    ],
                    [
                        'name' =>
                            'Form 2',

                        'level' =>
                            'Form 2',

                        'academic_year' =>
                            '2026',

                        'status' =>
                            'active',
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | Streams
        |--------------------------------------------------------------------------
        */

        $formOneEast =
            Stream::query()
                ->updateOrCreate(
                    [
                        'school_class_id' =>
                            $formOne->id,

                        'name' =>
                            'East',
                    ],
                    [
                        'teacher_id' =>
                            $users[
                                'teacherOne'
                            ]->id,

                        'status' =>
                            'active',
                    ]
                );

        $formOneWest =
            Stream::query()
                ->updateOrCreate(
                    [
                        'school_class_id' =>
                            $formOne->id,

                        'name' =>
                            'West',
                    ],
                    [
                        'teacher_id' =>
                            $users[
                                'teacherTwo'
                            ]->id,

                        'status' =>
                            'active',
                    ]
                );

        $formTwoEast =
            Stream::query()
                ->updateOrCreate(
                    [
                        'school_class_id' =>
                            $formTwo->id,

                        'name' =>
                            'East',
                    ],
                    [
                        'teacher_id' =>
                            $users[
                                'teacherOne'
                            ]->id,

                        'status' =>
                            'active',
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | Teaching Classes
        |--------------------------------------------------------------------------
        */

        $users['teacherOne']
            ->teacherClasses()
            ->syncWithoutDetaching([
                $formOne->id,
                $formTwo->id,
            ]);

        $users['teacherTwo']
            ->teacherClasses()
            ->syncWithoutDetaching([
                $formOne->id,
            ]);


        /*
        |--------------------------------------------------------------------------
        | Primary Class Teachers
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasColumn(
                'school_classes',
                'class_teacher_id'
            )
        ) {
            $formOne->update([
                'class_teacher_id' =>
                    $users[
                        'teacherOne'
                    ]->id,
            ]);

            $formTwo->update([
                'class_teacher_id' =>
                    $users[
                        'teacherTwo'
                    ]->id,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $users['studentOne']
            ->studentClasses()
            ->sync([
                $formOne->id => [
                    'stream_id' =>
                        $formOneEast->id,
                ],
            ]);

        $users['studentTwo']
            ->studentClasses()
            ->sync([
                $formOne->id => [
                    'stream_id' =>
                        $formOneWest->id,
                ],
            ]);

        $users['studentThree']
            ->studentClasses()
            ->sync([
                $formTwo->id => [
                    'stream_id' =>
                        $formTwoEast->id,
                ],
            ]);

        return compact(
            'formOne',
            'formTwo',
            'formOneEast',
            'formOneWest',
            'formTwoEast'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | School B Classes
    |--------------------------------------------------------------------------
    */

    private function createSchoolBStructure(
        School $school,
        array $users
    ): array {
        $formOne =
            $school
                ->classes()
                ->updateOrCreate(
                    [
                        'code' =>
                            'GF-F1-2026',
                    ],
                    [
                        'name' =>
                            'Form 1',

                        'level' =>
                            'Form 1',

                        'academic_year' =>
                            '2026',

                        'status' =>
                            'active',
                    ]
                );

        $stream =
            Stream::query()
                ->updateOrCreate(
                    [
                        'school_class_id' =>
                            $formOne->id,

                        'name' =>
                            'North',
                    ],
                    [
                        'teacher_id' =>
                            $users[
                                'teacher'
                            ]->id,

                        'status' =>
                            'active',
                    ]
                );

        $users['teacher']
            ->teacherClasses()
            ->syncWithoutDetaching([
                $formOne->id,
            ]);

        $users['student']
            ->studentClasses()
            ->sync([
                $formOne->id => [
                    'stream_id' =>
                        $stream->id,
                ],
            ]);

        if (
            Schema::hasColumn(
                'school_classes',
                'class_teacher_id'
            )
        ) {
            $formOne->update([
                'class_teacher_id' =>
                    $users[
                        'teacher'
                    ]->id,
            ]);
        }

        return compact(
            'formOne',
            'stream'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Publishers & Authors
    |--------------------------------------------------------------------------
    */

    private function createPublishersAndAuthors(
        array $users
    ): array {
        $publisherOne =
            Publisher::query()
                ->updateOrCreate(
                    [
                        'slug' =>
                            'savannah-books',
                    ],
                    [
                        'name' =>
                            'Savannah Books Limited',

                        'registration_number' =>
                            'PUB-DEMO-001',

                        'email' =>
                            'publisher@savannah.test',

                        'phone' =>
                            '+254700000001',

                        'website' =>
                            'https://example.test',

                        'description' =>
                            'Demo Kenyan educational and literary publisher.',

                        'status' =>
                            'active',
                    ]
                );

        $publisherTwo =
            Publisher::query()
                ->updateOrCreate(
                    [
                        'slug' =>
                            'highland-academic-press',
                    ],
                    [
                        'name' =>
                            'Highland Academic Press',

                        'registration_number' =>
                            'PUB-DEMO-002',

                        'email' =>
                            'publisher@highland.test',

                        'phone' =>
                            '+254700000002',

                        'description' =>
                            'Demo academic and reference publisher.',

                        'status' =>
                            'active',
                    ]
                );


        $grace =
            Author::query()
                ->updateOrCreate(
                    [
                        'slug' =>
                            'grace-wanjiku',
                    ],
                    [
                        'user_id' =>
                            $users[
                                'author_one'
                            ]->id,

                        'publisher_id' =>
                            $publisherOne->id,

                        'name' =>
                            'Grace Wanjiku',

                        'biography' =>
                            'Demo novelist and literature educator.',

                        'status' =>
                            'verified',
                    ]
                );

        $daniel =
            Author::query()
                ->updateOrCreate(
                    [
                        'slug' =>
                            'daniel-kiptoo',
                    ],
                    [
                        'user_id' =>
                            $users[
                                'author_two'
                            ]->id,

                        'publisher_id' =>
                            $publisherOne->id,

                        'name' =>
                            'Daniel Kiptoo',

                        'biography' =>
                            'Demo playwright and literary critic.',

                        'status' =>
                            'verified',
                    ]
                );

        $amina =
            Author::query()
                ->updateOrCreate(
                    [
                        'slug' =>
                            'amina-hassan',
                    ],
                    [
                        'user_id' =>
                            $users[
                                'author_three'
                            ]->id,

                        'publisher_id' =>
                            $publisherTwo->id,

                        'name' =>
                            'Amina Hassan',

                        'biography' =>
                            'Demo poet and academic writer.',

                        'status' =>
                            'verified',
                    ]
                );

        return [
            [
                'savannah' =>
                    $publisherOne,

                'highland' =>
                    $publisherTwo,
            ],
            [
                'grace' =>
                    $grace,

                'daniel' =>
                    $daniel,

                'amina' =>
                    $amina,
            ],
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Books
    |--------------------------------------------------------------------------
    */

    private function createBooks(
        array $publishers,
        array $authors,
        array $users
    ): array {
        /*
         * Generate demo files before creating records.
         */

        $paths = [
            'river' =>
                $this->createDemoPdf(
                    'river-between-hills.pdf',
                    'The River Between the Hills',
                    'A demo LiteraHub literary work for Form 1 students.'
                ),

            'voices' =>
                $this->createDemoPdf(
                    'voices-at-dawn.pdf',
                    'Voices at Dawn',
                    'A demo poetry collection used for literature study.'
                ),

            'stage' =>
                $this->createDemoPdf(
                    'stage-of-shadows.pdf',
                    'Stage of Shadows',
                    'A demo drama text for classroom reading.'
                ),

            'guide' =>
                $this->createDemoPdf(
                    'literary-analysis-guide.pdf',
                    'Practical Literary Analysis',
                    'A demo study guide for teachers and learners.'
                ),

            'review' =>
                $this->createDemoPdf(
                    'tomorrow-review.pdf',
                    'Tomorrow Has a Voice',
                    'This book is intentionally under review.'
                ),

            'unlicensed' =>
                $this->createDemoPdf(
                    'distant-mountains.pdf',
                    'Distant Mountains',
                    'Published globally but not licensed to LiteraHub Academy.'
                ),

            'download' =>
                $this->createDemoPdf(
                    'open-study-reference.pdf',
                    'Open Study Reference',
                    'Demo title where publisher explicitly permits download and print.'
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | Published / Class Assigned
        |--------------------------------------------------------------------------
        */

        $river =
            Book::query()
                ->updateOrCreate(
                    [
                        'isbn' =>
                            '9780306406157',
                    ],
                    [
                        'publisher_id' =>
                            $publishers[
                                'savannah'
                            ]->id,

                        'uploaded_by' =>
                            $users[
                                'author_one'
                            ]->id,

                        'title' =>
                            'The River Between the Hills',

                        'slug' =>
                            'the-river-between-the-hills',

                        'edition' =>
                            '1st Edition',

                        'publication_year' =>
                            2026,

                        'language' =>
                            'English',

                        'category' =>
                            'Novel',

                        'description' =>
                            'Demo novel licensed to LiteraHub Academy and assigned to Form 1.',

                        'pdf_path' =>
                            $paths['river'],

                        'page_count' =>
                            1,

                        'file_size' =>
                            Storage::disk('local')
                                ->size(
                                    $paths['river']
                                ),

                        'file_hash' =>
                            hash(
                                'sha256',
                                Storage::disk('local')
                                    ->get(
                                        $paths['river']
                                    )
                            ),

                        'status' =>
                            'published',

                        'submitted_at' =>
                            now()->subDays(20),

                        'reviewed_at' =>
                            now()->subDays(18),

                        'reviewed_by' =>
                            $users[
                                'content_manager'
                            ]->id,

                        'allow_online_reading' =>
                            true,

                        'allow_download' =>
                            false,

                        'allow_print' =>
                            false,

                        'allow_teacher_assignment' =>
                            true,

                        'allow_student_borrowing' =>
                            true,

                        'loan_days' =>
                            14,

                        'max_concurrent_loans' =>
                            50,

                        'rights_statement' =>
                            'Copyright remains with the author. Online reading only under institutional licence.',
                    ]
                );

        $river->authors()->sync([
            $authors['grace']->id => [
                'contribution' => 'author',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Published / Licensed / Not Assigned
        |--------------------------------------------------------------------------
        */

        $voices =
            Book::query()
                ->updateOrCreate(
                    [
                        'isbn' =>
                            '9781861972712',
                    ],
                    [
                        'publisher_id' =>
                            $publishers[
                                'savannah'
                            ]->id,

                        'uploaded_by' =>
                            $users[
                                'author_two'
                            ]->id,

                        'title' =>
                            'Voices at Dawn',

                        'slug' =>
                            'voices-at-dawn',

                        'publication_year' =>
                            2025,

                        'language' =>
                            'English',

                        'category' =>
                            'Poetry',

                        'description' =>
                            'Licensed to the school but intentionally not assigned to Form 1, allowing access-request testing.',

                        'pdf_path' =>
                            $paths['voices'],

                        'page_count' =>
                            1,

                        'status' =>
                            'published',

                        'submitted_at' =>
                            now()->subDays(30),

                        'reviewed_at' =>
                            now()->subDays(28),

                        'reviewed_by' =>
                            $users[
                                'content_manager'
                            ]->id,

                        'allow_online_reading' =>
                            true,

                        'allow_download' =>
                            false,

                        'allow_print' =>
                            false,

                        'allow_teacher_assignment' =>
                            true,

                        'allow_student_borrowing' =>
                            true,

                        'loan_days' =>
                            7,

                        'rights_statement' =>
                            'Online institutional reading permitted. Printing and downloads prohibited.',
                    ]
                );

        $voices->authors()->sync([
            $authors['daniel']->id => [
                'contribution' =>
                    'author',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Published Drama
        |--------------------------------------------------------------------------
        */

        $stage =
            Book::query()
                ->updateOrCreate(
                    [
                        'isbn' =>
                            '9780140328721',
                    ],
                    [
                        'publisher_id' =>
                            $publishers[
                                'savannah'
                            ]->id,

                        'uploaded_by' =>
                            $users[
                                'author_two'
                            ]->id,

                        'title' =>
                            'Stage of Shadows',

                        'slug' =>
                            'stage-of-shadows',

                        'publication_year' =>
                            2024,

                        'language' =>
                            'English',

                        'category' =>
                            'Drama',

                        'description' =>
                            'Demo drama text assigned to Form 2.',

                        'pdf_path' =>
                            $paths['stage'],

                        'page_count' =>
                            1,

                        'status' =>
                            'published',

                        'allow_online_reading' =>
                            true,

                        'allow_download' =>
                            false,

                        'allow_print' =>
                            false,

                        'allow_teacher_assignment' =>
                            true,

                        'allow_student_borrowing' =>
                            true,

                        'loan_days' =>
                            14,
                    ]
                );

        $stage->authors()->sync([
            $authors['daniel']->id => [
                'contribution' =>
                    'author',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Study Guide
        |--------------------------------------------------------------------------
        */

        $guide =
            Book::query()
                ->updateOrCreate(
                    [
                        'isbn' =>
                            '9780131103627',
                    ],
                    [
                        'publisher_id' =>
                            $publishers[
                                'highland'
                            ]->id,

                        'uploaded_by' =>
                            $users[
                                'author_three'
                            ]->id,

                        'title' =>
                            'Practical Literary Analysis',

                        'slug' =>
                            'practical-literary-analysis',

                        'publication_year' =>
                            2026,

                        'language' =>
                            'English',

                        'category' =>
                            'Study Guide',

                        'description' =>
                            'Teacher and student literary analysis reference.',

                        'pdf_path' =>
                            $paths['guide'],

                        'page_count' =>
                            1,

                        'status' =>
                            'published',

                        'allow_online_reading' =>
                            true,

                        'allow_download' =>
                            false,

                        'allow_print' =>
                            false,

                        'allow_teacher_assignment' =>
                            true,

                        'allow_student_borrowing' =>
                            true,

                        'loan_days' =>
                            30,
                    ]
                );

        $guide->authors()->sync([
            $authors['amina']->id => [
                'contribution' =>
                    'author',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Under Review
        |--------------------------------------------------------------------------
        */

        $underReview =
            Book::query()
                ->updateOrCreate(
                    [
                        'isbn' =>
                            '9780201633610',
                    ],
                    [
                        'publisher_id' =>
                            $publishers[
                                'savannah'
                            ]->id,

                        'uploaded_by' =>
                            $users[
                                'author_one'
                            ]->id,

                        'title' =>
                            'Tomorrow Has a Voice',

                        'slug' =>
                            'tomorrow-has-a-voice',

                        'publication_year' =>
                            2026,

                        'language' =>
                            'English',

                        'category' =>
                            'Novel',

                        'description' =>
                            'Demo work awaiting content review.',

                        'pdf_path' =>
                            $paths['review'],

                        'page_count' =>
                            1,

                        'status' =>
                            'under_review',

                        'submitted_at' =>
                            now()->subDay(),

                        'allow_online_reading' =>
                            true,

                        'allow_download' =>
                            false,

                        'allow_print' =>
                            false,

                        'allow_teacher_assignment' =>
                            true,

                        'allow_student_borrowing' =>
                            true,
                    ]
                );

        $underReview->authors()->sync([
            $authors['grace']->id => [
                'contribution' =>
                    'author',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Published But Unlicensed To School A
        |--------------------------------------------------------------------------
        */

        $unlicensed =
            Book::query()
                ->updateOrCreate(
                    [
                        'isbn' =>
                            '9780261103573',
                    ],
                    [
                        'publisher_id' =>
                            $publishers[
                                'highland'
                            ]->id,

                        'uploaded_by' =>
                            $users[
                                'author_three'
                            ]->id,

                        'title' =>
                            'Distant Mountains',

                        'slug' =>
                            'distant-mountains',

                        'publication_year' =>
                            2023,

                        'language' =>
                            'English',

                        'category' =>
                            'Novel',

                        'description' =>
                            'Published book intentionally not licensed to LiteraHub Academy.',

                        'pdf_path' =>
                            $paths['unlicensed'],

                        'page_count' =>
                            1,

                        'status' =>
                            'published',

                        'allow_online_reading' =>
                            true,

                        'allow_download' =>
                            false,

                        'allow_print' =>
                            false,

                        'allow_teacher_assignment' =>
                            true,

                        'allow_student_borrowing' =>
                            true,
                    ]
                );

        $unlicensed->authors()->sync([
            $authors['amina']->id => [
                'contribution' =>
                    'author',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Download / Print Allowed
        |--------------------------------------------------------------------------
        */

        $openReference =
            Book::query()
                ->updateOrCreate(
                    [
                        'isbn' =>
                            '9780132350884',
                    ],
                    [
                        'publisher_id' =>
                            $publishers[
                                'highland'
                            ]->id,

                        'uploaded_by' =>
                            $users[
                                'author_three'
                            ]->id,

                        'title' =>
                            'Open Study Reference',

                        'slug' =>
                            'open-study-reference',

                        'publication_year' =>
                            2026,

                        'language' =>
                            'English',

                        'category' =>
                            'Reference',

                        'description' =>
                            'Demo title explicitly allowing print and download.',

                        'pdf_path' =>
                            $paths['download'],

                        'page_count' =>
                            1,

                        'status' =>
                            'published',

                        'allow_online_reading' =>
                            true,

                        'allow_download' =>
                            true,

                        'allow_print' =>
                            true,

                        'allow_teacher_assignment' =>
                            true,

                        'allow_student_borrowing' =>
                            true,

                        'loan_days' =>
                            30,

                        'rights_statement' =>
                            'Demo rights allow online reading, printing and downloading.',
                    ]
                );

        $openReference->authors()->sync([
            $authors['amina']->id => [
                'contribution' =>
                    'author',
            ],
        ]);


        return compact(
            'river',
            'voices',
            'stage',
            'guide',
            'underReview',
            'unlicensed',
            'openReference'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Licences
    |--------------------------------------------------------------------------
    */

    private function createLicenses(
        School $schoolA,
        School $schoolB,
        array $books,
        array $publishers,
        array $authors,
        array $platformUsers
    ): array {
        $licenses = [];


        /*
        |--------------------------------------------------------------------------
        | School A - Active Novel Licence
        |--------------------------------------------------------------------------
        */

        $licenses['river'] =
            $this->license(
                school: $schoolA,
                book: $books['river'],
                publisher: $publishers['savannah'],
                author: null,
                number: 'DEMO-LIC-A-001',
                status: 'active',
                startsAt: now()->subMonth(),
                expiresAt: now()->addYear(),
                creator: $platformUsers['content_manager'],
                print: false,
                download: false
            );


        /*
        |--------------------------------------------------------------------------
        | School A - Active Poetry Licence
        |--------------------------------------------------------------------------
        */

        $licenses['voices'] =
            $this->license(
                school: $schoolA,
                book: $books['voices'],
                publisher: $publishers['savannah'],
                author: null,
                number: 'DEMO-LIC-A-002',
                status: 'active',
                startsAt: now()->subMonth(),
                expiresAt: now()->addMonths(6),
                creator: $platformUsers['content_manager'],
                print: false,
                download: false
            );


        /*
        |--------------------------------------------------------------------------
        | School A - Drama
        |--------------------------------------------------------------------------
        */

        $licenses['stage'] =
            $this->license(
                school: $schoolA,
                book: $books['stage'],
                publisher: null,
                author: $authors['daniel'],
                number: 'DEMO-LIC-A-003',
                status: 'active',
                startsAt: now()->subMonth(),
                expiresAt: now()->addYear(),
                creator: $platformUsers['content_manager'],
                print: false,
                download: false
            );


        /*
        |--------------------------------------------------------------------------
        | School A - Study Guide
        |--------------------------------------------------------------------------
        */

        $licenses['guide'] =
            $this->license(
                school: $schoolA,
                book: $books['guide'],
                publisher: $publishers['highland'],
                author: null,
                number: 'DEMO-LIC-A-004',
                status: 'active',
                startsAt: now()->subMonth(),
                expiresAt: now()->addYear(),
                creator: $platformUsers['content_manager'],
                print: false,
                download: false
            );


        /*
        |--------------------------------------------------------------------------
        | School A - Print & Download Permitted
        |--------------------------------------------------------------------------
        */

        $licenses['openReference'] =
            $this->license(
                school: $schoolA,
                book: $books['openReference'],
                publisher: $publishers['highland'],
                author: null,
                number: 'DEMO-LIC-A-005',
                status: 'active',
                startsAt: now()->subMonth(),
                expiresAt: now()->addYear(),
                creator: $platformUsers['content_manager'],
                print: true,
                download: true
            );


        /*
        |--------------------------------------------------------------------------
        | School B gets Distant Mountains.
        |
        | School A deliberately does not.
        |--------------------------------------------------------------------------
        */

        $licenses['schoolBUnlicensed'] =
            $this->license(
                school: $schoolB,
                book: $books['unlicensed'],
                publisher: $publishers['highland'],
                author: null,
                number: 'DEMO-LIC-B-001',
                status: 'active',
                startsAt: now()->subMonth(),
                expiresAt: now()->addYear(),
                creator: $platformUsers['content_manager'],
                print: false,
                download: false
            );


        /*
        |--------------------------------------------------------------------------
        | Expired Licence
        |--------------------------------------------------------------------------
        */

        $licenses['expired'] =
            $this->license(
                school: $schoolA,
                book: $books['unlicensed'],
                publisher: $publishers['highland'],
                author: null,
                number: 'DEMO-LIC-A-EXPIRED',
                status: 'expired',
                startsAt: now()->subYears(2),
                expiresAt: now()->subYear(),
                creator: $platformUsers['content_manager'],
                print: false,
                download: false
            );


        /*
        |--------------------------------------------------------------------------
        | Pending Licence
        |--------------------------------------------------------------------------
        */

        $licenses['pending'] =
            $this->license(
                school: $schoolB,
                book: $books['guide'],
                publisher: $publishers['highland'],
                author: null,
                number: 'DEMO-LIC-B-PENDING',
                status: 'pending',
                startsAt: now(),
                expiresAt: now()->addYear(),
                creator: $platformUsers['content_manager'],
                print: false,
                download: false
            );

        return $licenses;
    }


    /*
    |--------------------------------------------------------------------------
    | Book → Class
    |--------------------------------------------------------------------------
    */

    private function assignBooksToClasses(
        array $books,
        array $schoolA,
        array $schoolB,
        array $schoolAUsers,
        array $schoolBUsers
    ): void {
        /*
         * Form 1 sees River.
         */

        $books['river']
            ->classes()
            ->syncWithoutDetaching([
                $schoolA['formOne']->id => [
                    'assigned_by' =>
                        $schoolAUsers[
                            'teacherOne'
                        ]->id,

                    'available_from' =>
                        now()->subWeek(),

                    'available_until' =>
                        now()->addMonths(3),
                ],
            ]);


        /*
         * Form 2 sees Stage.
         */

        $books['stage']
            ->classes()
            ->syncWithoutDetaching([
                $schoolA['formTwo']->id => [
                    'assigned_by' =>
                        $schoolAUsers[
                            'teacherOne'
                        ]->id,

                    'available_from' =>
                        now()->subWeek(),

                    'available_until' =>
                        now()->addMonths(3),
                ],
            ]);


        /*
         * Study guide available to both.
         */

        $books['guide']
            ->classes()
            ->syncWithoutDetaching([
                $schoolA['formOne']->id => [
                    'assigned_by' =>
                        $schoolAUsers[
                            'teacherOne'
                        ]->id,

                    'available_from' =>
                        now(),

                    'available_until' =>
                        null,
                ],

                $schoolA['formTwo']->id => [
                    'assigned_by' =>
                        $schoolAUsers[
                            'teacherTwo'
                        ]->id,

                    'available_from' =>
                        now(),

                    'available_until' =>
                        null,
                ],
            ]);


        /*
         * Print/download reference assigned to Form 1.
         */

        $books['openReference']
            ->classes()
            ->syncWithoutDetaching([
                $schoolA['formOne']->id => [
                    'assigned_by' =>
                        $schoolAUsers[
                            'teacherOne'
                        ]->id,

                    'available_from' =>
                        now(),

                    'available_until' =>
                        null,
                ],
            ]);


        /*
         * School B book.
         */

        $books['unlicensed']
            ->classes()
            ->syncWithoutDetaching([
                $schoolB['formOne']->id => [
                    'assigned_by' =>
                        $schoolBUsers[
                            'teacher'
                        ]->id,

                    'available_from' =>
                        now(),

                    'available_until' =>
                        null,
                ],
            ]);


        /*
         * Voices deliberately NOT assigned.
         *
         * Students must request access.
         */
    }


    /*
    |--------------------------------------------------------------------------
    | Borrowings & Bookmarks
    |--------------------------------------------------------------------------
    */

    private function createBorrowingsAndBookmarks(
        School $school,
        array $books,
        array $users
    ): void {
        BookBorrowing::query()
            ->updateOrCreate(
                [
                    'book_id' =>
                        $books['river']->id,

                    'user_id' =>
                        $users[
                            'studentOne'
                        ]->id,

                    'school_id' =>
                        $school->id,

                    'status' =>
                        'borrowed',
                ],
                [
                    'borrowed_at' =>
                        now()->subDays(2),

                    'due_at' =>
                        now()->addDays(12),

                    'returned_at' =>
                        null,
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | Historical Returned Loan
        |--------------------------------------------------------------------------
        */

        BookBorrowing::query()
            ->updateOrCreate(
                [
                    'book_id' =>
                        $books['guide']->id,

                    'user_id' =>
                        $users[
                            'studentTwo'
                        ]->id,

                    'school_id' =>
                        $school->id,

                    'status' =>
                        'returned',
                ],
                [
                    'borrowed_at' =>
                        now()->subDays(30),

                    'due_at' =>
                        now()->subDays(16),

                    'returned_at' =>
                        now()->subDays(20),
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | Bookmark
        |--------------------------------------------------------------------------
        */

        BookBookmark::query()
            ->updateOrCreate(
                [
                    'book_id' =>
                        $books['river']->id,

                    'user_id' =>
                        $users[
                            'studentOne'
                        ]->id,

                    'page' =>
                        1,
                ],
                [
                    'label' =>
                        'Introduction',

                    'note' =>
                        'Demo bookmark created by the seed data.',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Access Requests
    |--------------------------------------------------------------------------
    */

    private function createAccessRequests(
        School $school,
        array $books,
        array $users
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Pending
        |--------------------------------------------------------------------------
        */

        BookAccessRequest::query()
            ->updateOrCreate(
                [
                    'book_id' =>
                        $books['voices']->id,

                    'student_id' =>
                        $users[
                            'studentOne'
                        ]->id,

                    'school_id' =>
                        $school->id,
                ],
                [
                    'teacher_id' =>
                        $users[
                            'teacherOne'
                        ]->id,

                    'reason' =>
                        'I would like to read this poetry collection for additional literature practice.',

                    'status' =>
                        'pending',

                    'reviewed_at' =>
                        null,

                    'expires_at' =>
                        null,
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | Approved
        |--------------------------------------------------------------------------
        */

        BookAccessRequest::query()
            ->updateOrCreate(
                [
                    'book_id' =>
                        $books['voices']->id,

                    'student_id' =>
                        $users[
                            'studentTwo'
                        ]->id,

                    'school_id' =>
                        $school->id,
                ],
                [
                    'teacher_id' =>
                        $users[
                            'teacherOne'
                        ]->id,

                    'reason' =>
                        'Additional poetry revision.',

                    'status' =>
                        'approved',

                    'reviewed_at' =>
                        now()->subDay(),

                    'expires_at' =>
                        now()->addMonth(),
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | Rejected
        |--------------------------------------------------------------------------
        */

        BookAccessRequest::query()
            ->updateOrCreate(
                [
                    'book_id' =>
                        $books['voices']->id,

                    'student_id' =>
                        $users[
                            'studentThree'
                        ]->id,

                    'school_id' =>
                        $school->id,
                ],
                [
                    'teacher_id' =>
                        $users[
                            'teacherOne'
                        ]->id,

                    'reason' =>
                        'General reading request.',

                    'status' =>
                        'rejected',

                    'reviewed_at' =>
                        now()->subDays(2),

                    'expires_at' =>
                        null,
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Create User
    |--------------------------------------------------------------------------
    */

    private function user(
        string $name,
        string $email,
        string $role
    ): User {
        $user = User::query()
            ->updateOrCreate(
                [
                    'email' =>
                        $email,
                ],
                [
                    'name' =>
                        $name,

                    'phone' =>
                        null,

                    'password' =>
                        Hash::make(
                            $this->password
                        ),

                    'status' =>
                        'active',
                ]
            );

        $user->syncRoles([
            $role,
        ]);

        return $user;
    }


    /*
    |--------------------------------------------------------------------------
    | School User
    |--------------------------------------------------------------------------
    */

    private function schoolUser(
        School $school,
        string $name,
        string $email,
        string $role,
        string $reference
    ): User {
        $user =
            $this->user(
                $name,
                $email,
                $role
            );

        $school
            ->users()
            ->syncWithoutDetaching([
                $user->id => [
                    'role' =>
                        $role,

                    'status' =>
                        'active',

                    'reference_number' =>
                        $reference,
                ],
            ]);

        return $user;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Licence
    |--------------------------------------------------------------------------
    */

    private function license(
        School $school,
        Book $book,
        ?Publisher $publisher,
        ?Author $author,
        string $number,
        string $status,
        $startsAt,
        $expiresAt,
        User $creator,
        bool $print,
        bool $download
    ): SchoolBookLicense {
        return SchoolBookLicense::query()
            ->updateOrCreate(
                [
                    'license_number' =>
                        $number,
                ],
                [
                    'school_id' =>
                        $school->id,

                    'book_id' =>
                        $book->id,

                    'publisher_id' =>
                        $publisher?->id,

                    'author_id' =>
                        $author?->id,

                    'license_type' =>
                        'lease',

                    'starts_at' =>
                        $startsAt,

                    'expires_at' =>
                        $expiresAt,

                    'seat_limit' =>
                        500,

                    'concurrent_reader_limit' =>
                        100,

                    'allow_student_reading' =>
                        true,

                    'allow_teacher_reading' =>
                        true,

                    'allow_teacher_assignment' =>
                        true,

                    'allow_student_borrowing' =>
                        true,

                    'allow_print' =>
                        $print,

                    'allow_download' =>
                        $download,

                    'status' =>
                        $status,

                    'price_minor' =>
                        500000,

                    'currency' =>
                        'KES',

                    'terms' =>
                        'Demo institutional licence generated for LiteraHub testing.',

                    'notes' =>
                        'Seeded demo licence.',

                    'created_by' =>
                        $creator->id,
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Generate Valid Demo PDF
    |--------------------------------------------------------------------------
    |
    | Produces a minimal one-page PDF that PDF.js can open.
    |
    */

    private function createDemoPdf(
        string $filename,
        string $title,
        string $body
    ): string {
        $path =
            'library/books/'
            . $filename;

        $pdf =
            $this->generatePdf(
                $title,
                $body
            );

        Storage::disk('local')
            ->put(
                $path,
                $pdf
            );

        return $path;
    }


    /*
    |--------------------------------------------------------------------------
    | Minimal PDF Generator
    |--------------------------------------------------------------------------
    */

    private function generatePdf(
        string $title,
        string $body
    ): string {
        $title =
            $this->escapePdfText(
                $title
            );

        $body =
            $this->escapePdfText(
                $body
            );

        $stream = implode("\n", [
            'BT',
            '/F1 20 Tf',
            '72 720 Td',
            "({$title}) Tj",
            '0 -40 Td',
            '/F1 11 Tf',
            "({$body}) Tj",
            '0 -40 Td',
            '(LiteraHub Demo Library Document) Tj',
            'ET',
        ]);

        $objects = [];

        $objects[1] =
            '<< /Type /Catalog /Pages 2 0 R >>';

        $objects[2] =
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';

        $objects[3] =
            '<< /Type /Page '
            . '/Parent 2 0 R '
            . '/MediaBox [0 0 612 792] '
            . '/Resources << '
            . '/Font << /F1 5 0 R >> '
            . '>> '
            . '/Contents 4 0 R '
            . '>>';

        $objects[4] =
            '<< /Length '
            . strlen($stream)
            . " >>\n"
            . "stream\n"
            . $stream
            . "\nendstream";

        $objects[5] =
            '<< /Type /Font '
            . '/Subtype /Type1 '
            . '/BaseFont /Helvetica '
            . '>>';


        $pdf =
            "%PDF-1.4\n";

        $offsets = [
            0 => 0,
        ];

        foreach ($objects as $number => $object) {
            $offsets[$number] =
                strlen($pdf);

            $pdf .=
                $number
                . " 0 obj\n"
                . $object
                . "\nendobj\n";
        }

        $xrefOffset =
            strlen($pdf);

        $pdf .=
            "xref\n"
            . "0 6\n"
            . "0000000000 65535 f \n";

        for (
            $i = 1;
            $i <= 5;
            $i++
        ) {
            $pdf .=
                sprintf(
                    "%010d 00000 n \n",
                    $offsets[$i]
                );
        }

        $pdf .=
            "trailer\n"
            . "<< /Size 6 /Root 1 0 R >>\n"
            . "startxref\n"
            . $xrefOffset
            . "\n%%EOF";

        return $pdf;
    }


    private function escapePdfText(
        string $value
    ): string {
        $value =
            str_replace(
                '\\',
                '\\\\',
                $value
            );

        $value =
            str_replace(
                '(',
                '\\(',
                $value
            );

        return str_replace(
            ')',
            '\\)',
            $value
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Demo Accounts
    |--------------------------------------------------------------------------
    */

    private function printDemoAccounts(): void
    {
        $this->command?->newLine();

        $this->command?->info(
            'LiteraHub demo data created successfully.'
        );

        $this->command?->newLine();

        $this->command?->table(
            [
                'Role',
                'Email',
                'Password',
            ],
            [
                [
                    'Super Admin',
                    'superadmin@literahub.test',
                    $this->password,
                ],

                [
                    'Platform Admin',
                    'admin@literahub.test',
                    $this->password,
                ],

                [
                    'Content Manager',
                    'content@literahub.test',
                    $this->password,
                ],

                [
                    'Finance',
                    'finance@literahub.test',
                    $this->password,
                ],

                [
                    'Author',
                    'grace.author@literahub.test',
                    $this->password,
                ],

                [
                    'Author',
                    'daniel.author@literahub.test',
                    $this->password,
                ],

                [
                    'School Admin',
                    'schooladmin@literahub.test',
                    $this->password,
                ],

                [
                    'Teacher',
                    'teacher1@literahub.test',
                    $this->password,
                ],

                [
                    'Teacher',
                    'teacher2@literahub.test',
                    $this->password,
                ],

                [
                    'Student',
                    'student1@literahub.test',
                    $this->password,
                ],

                [
                    'Student',
                    'student2@literahub.test',
                    $this->password,
                ],

                [
                    'Student',
                    'student3@literahub.test',
                    $this->password,
                ],

                [
                    'School B Admin',
                    'greenfield.admin@literahub.test',
                    $this->password,
                ],

                [
                    'School B Student',
                    'greenfield.student@literahub.test',
                    $this->password,
                ],

                [
                    'Individual',
                    'reader@literahub.test',
                    $this->password,
                ],
            ]
        );
    }
}