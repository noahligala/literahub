<?php

namespace App\Enums;

enum UserRole: string
{
    case PlatformAdmin = 'platform_admin';
    case Author = 'author';
    case ContentManager = 'content_manager';
    case SchoolAdmin = 'school_admin';
    case Teacher = 'teacher';
    case Student = 'student';
    case Guardian = 'guardian';
}
