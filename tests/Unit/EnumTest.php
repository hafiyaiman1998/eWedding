<?php

namespace Tests\Unit;

use App\Enums\AnalyticEventType;
use App\Enums\ApprovalStatus;
use App\Enums\AttendanceStatus;
use App\Enums\GiftStatus;
use App\Enums\UserType;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function test_user_type_values(): void
    {
        $this->assertSame(['admin', 'user'], UserType::values());
        $this->assertSame(UserType::Admin, UserType::from('admin'));
        $this->assertSame(UserType::User, UserType::from('user'));
    }

    public function test_approval_status_values(): void
    {
        $this->assertSame(['pending', 'approved', 'rejected'], ApprovalStatus::values());

        foreach (ApprovalStatus::values() as $value) {
            $this->assertSame($value, ApprovalStatus::from($value)->value);
        }
    }

    public function test_gift_status_values(): void
    {
        $this->assertSame(['pending', 'paid', 'failed', 'cancelled'], GiftStatus::values());

        foreach (GiftStatus::values() as $value) {
            $this->assertSame($value, GiftStatus::from($value)->value);
        }
    }

    public function test_attendance_status_values(): void
    {
        $this->assertSame(['yes', 'no'], AttendanceStatus::values());

        foreach (AttendanceStatus::values() as $value) {
            $this->assertSame($value, AttendanceStatus::from($value)->value);
        }
    }

    public function test_analytic_event_type_values(): void
    {
        $this->assertSame(['view', 'share', 'rsvp_yes', 'rsvp_no'], AnalyticEventType::values());

        foreach (AnalyticEventType::values() as $value) {
            $this->assertSame($value, AnalyticEventType::from($value)->value);
        }
    }

    public function test_labels_are_non_empty_for_all_cases(): void
    {
        foreach (
            [
                UserType::cases(),
                ApprovalStatus::cases(),
                GiftStatus::cases(),
                AttendanceStatus::cases(),
                AnalyticEventType::cases(),
            ] as $cases
        ) {
            foreach ($cases as $case) {
                $this->assertNotEmpty($case->label());
            }
        }
    }
}
