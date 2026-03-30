<?php

use App\Models\Branch;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudyLevel;
use App\Models\StudyTrack;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function makeStudentEnrollmentManagerUser(): User
{
    Role::findOrCreate('المشرف العام', 'web');
    Role::findOrCreate('السكرتيرة', 'web');
    Role::findOrCreate('المعلم', 'web');

    foreach (['student-enrollments.view', 'student-enrollments.create', 'student-enrollments.update', 'student-enrollments.delete'] as $permissionName) {
        Permission::findOrCreate($permissionName, 'web');
    }

    $role = Role::findByName('المشرف العام', 'web');
    $role->syncPermissions(['student-enrollments.view', 'student-enrollments.create', 'student-enrollments.update', 'student-enrollments.delete']);

    $user = User::factory()->create();
    $user->assignRole('المشرف العام');

    return $user;
}

function makeEnrollmentReadyGroup(string $name = 'حلقة تجريبية'): Group
{
    $branch = Branch::factory()->create();
    $teacher = User::factory()->create();
    $teacher->assignRole('المعلم');
    $studyLevel = StudyLevel::factory()->create();
    $studyTrack = StudyTrack::factory()->create();

    return Group::factory()->create([
        'name' => $name,
        'branch_id' => $branch->id,
        'teacher_id' => $teacher->id,
        'study_level_id' => $studyLevel->id,
        'study_track_id' => $studyTrack->id,
    ]);
}

it('يعرض صفحة فهرس تسجيلات الطلاب للمستخدم المخول', function () {
    $user = makeStudentEnrollmentManagerUser();

    $this->actingAs($user)
        ->get(route('admin.student-enrollments.index'))
        ->assertOk()
        ->assertSee('تسجيل الطلاب في الحلقات - الفهرس');
});

it('يسجل طالبًا في حلقة بنجاح', function () {
    $user = makeStudentEnrollmentManagerUser();
    $student = Student::factory()->create();
    $group = makeEnrollmentReadyGroup('حلقة التحفيظ الأولى');

    $response = $this->actingAs($user)->post(route('admin.student-enrollments.store'), [
        'student_id' => $student->id,
        'group_id' => $group->id,
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.student-enrollments.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('student_enrollments', [
        'student_id' => $student->id,
        'group_id' => $group->id,
        'status' => 'active',
    ]);
});

it('ينقل الطالب من حلقة إلى حلقة أخرى مع حفظ السجل السابق', function () {
    $user = makeStudentEnrollmentManagerUser();
    $student = Student::factory()->create();
    $groupOne = makeEnrollmentReadyGroup('حلقة أ');
    $groupTwo = makeEnrollmentReadyGroup('حلقة ب');

    $enrollment = StudentEnrollment::factory()->create([
        'student_id' => $student->id,
        'group_id' => $groupOne->id,
        'status' => 'active',
    ]);

    $response = $this->actingAs($user)->put(route('admin.student-enrollments.update', $enrollment), [
        'group_id' => $groupTwo->id,
        'status' => 'active',
    ]);

    $response
        ->assertRedirect(route('admin.student-enrollments.show', $student->id))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('student_enrollments', [
        'id' => $enrollment->id,
        'status' => 'transferred',
    ]);

    $this->assertDatabaseHas('student_enrollments', [
        'student_id' => $student->id,
        'group_id' => $groupTwo->id,
        'status' => 'active',
    ]);
});

it('يعرض سجل الطالب الحالي والسابق', function () {
    $user = makeStudentEnrollmentManagerUser();
    $student = Student::factory()->create();
    $groupOne = makeEnrollmentReadyGroup('حلقة سجل 1');
    $groupTwo = makeEnrollmentReadyGroup('حلقة سجل 2');

    StudentEnrollment::factory()->create([
        'student_id' => $student->id,
        'group_id' => $groupOne->id,
        'status' => 'transferred',
    ]);

    StudentEnrollment::factory()->create([
        'student_id' => $student->id,
        'group_id' => $groupTwo->id,
        'status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('admin.student-enrollments.show', $student->id))
        ->assertOk()
        ->assertSee('حلقة سجل 1')
        ->assertSee('حلقة سجل 2');
});

