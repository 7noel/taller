<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppMessage;
use App\Models\Brand;
use App\Models\CompanySetting;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\Part;
use App\Models\PartOrder;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleRelationship;
use App\Services\ReminderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WhatsAppReminderTest extends TestCase
{
    use DatabaseTruncation;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // La DB de testing ya está migrada (patrón del proyecto, ver AppointmentTest).
        RefreshDatabaseState::$migrated = true;
    }

    public static function tearDownAfterClass(): void
    {
        RefreshDatabaseState::$migrated = false;

        parent::tearDownAfterClass();
    }

    private Establishment $establishment;
    private User $advisor;
    private Party $owner;
    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        // Hora fija para no depender de la hora real del runner.
        Carbon::setTestNow(Carbon::parse('2026-09-01 09:00:00'));

        $brand = Brand::create(['name' => 'TOYOTA']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);

        $this->establishment = Establishment::create([
            'name' => 'Taller Central',
            'address' => 'Av. Principal 123',
            'phone' => '123456789',
            'email' => 'contacto@taller.com',
            'code' => 'TC001',
        ]);

        $this->advisor = User::factory()->create([
            'establishment_id' => $this->establishment->id,
            'phone' => '987654321',
        ]);

        $this->owner = Party::create([
            'document_type' => '1',
            'document_number' => '12345678',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'mobile' => '999111222',
        ]);

        $this->vehicle = Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'plate' => 'ABC123',
        ]);

        VehicleRelationship::create([
            'vehicle_id' => $this->vehicle->id,
            'party_id' => $this->owner->id,
            'role' => 'owner',
        ]);

        CompanySetting::create([
            'reminder_enabled' => true,
            'reminder_hour' => '09:00',
            'reminder_technical_review_enabled' => true,
            'reminder_technical_review_days' => 10,
            'reminder_maintenance_enabled' => true,
            'reminder_maintenance_days' => 7,
            'reminder_part_order_enabled' => true,
            'reminder_part_milestones' => '25,20,17,15,10,5',
            'reminder_estimate_enabled' => true,
            'reminder_estimate_every_days' => 3,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    protected function createEstimate(array $overrides = []): Estimate
    {
        return Estimate::create(array_merge([
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->owner->id,
            'advisor_id' => $this->advisor->id,
            'establishment_id' => $this->establishment->id,
            'service_type' => 'siniestro',
            'status' => 'sent_insurance',
            'document_sn' => 'PRE01-000001',
            'created_by' => $this->advisor->id,
            'updated_by' => $this->advisor->id,
        ], $overrides));
    }

    protected function createPartOrder(array $overrides = []): PartOrder
    {
        $estimate = $this->createEstimate();
        $part = Part::create(['name' => 'Parachoques', 'sku' => 'SKU-PARACHOQUES']);

        return PartOrder::create(array_merge([
            'part_id' => $part->id,
            'estimate_id' => $estimate->id,
            'provider_id' => $this->owner->id,
            'quantity' => 1,
            'status' => 'ordered',
            'expected_delivery' => '2026-09-26', // hoy + 25 → hito 25
        ], $overrides));
    }

    public function test_technical_review_reminder_dispatches(): void
    {
        Queue::fake();

        $this->vehicle->update(['technical_review_date' => '2026-09-11']); // hoy + 10

        $result = app(ReminderService::class)->process();

        $this->assertSame(1, $result['sent']);

        Queue::assertPushed(SendWhatsAppMessage::class, function ($job) {
            return $job->phone === '999111222'
                && str_contains($job->message, 'ABC123')
                && $job->reminderLogId !== null;
        });

        $this->assertDatabaseHas('reminder_logs', [
            'type' => 'technical_review',
            'target_type' => 'vehicle',
            'target_id' => $this->vehicle->id,
            'trigger_date' => '2026-09-01',
            'recipient_type' => 'client',
            'status' => 'pending',
        ]);
    }

    public function test_maintenance_reminder_dispatches(): void
    {
        Queue::fake();

        $this->vehicle->update(['next_maintenance_date' => '2026-09-08']); // hoy + 7

        $result = app(ReminderService::class)->process();

        $this->assertSame(1, $result['sent']);

        Queue::assertPushed(SendWhatsAppMessage::class, fn ($job) => $job->phone === '999111222');

        $this->assertDatabaseHas('reminder_logs', [
            'type' => 'maintenance',
            'target_type' => 'vehicle',
            'trigger_date' => '2026-09-01',
        ]);
    }

    public function test_part_order_milestone_dispatches_to_advisor(): void
    {
        Queue::fake();

        $this->createPartOrder(); // expected_delivery hoy + 25 → hito 25

        $result = app(ReminderService::class)->process();

        $this->assertSame(1, $result['sent']);

        Queue::assertPushed(SendWhatsAppMessage::class, function ($job) {
            return $job->phone === '987654321'
                && str_contains($job->message, 'Parachoques')
                && str_contains($job->message, '25 días');
        });

        $this->assertDatabaseHas('reminder_logs', [
            'type' => 'part_order',
            'target_type' => 'part_order',
            'trigger_date' => '2026-09-01',
            'recipient_type' => 'advisor',
        ]);
    }

    public function test_part_order_other_milestone_dispatches(): void
    {
        Queue::fake();

        $this->createPartOrder(['expected_delivery' => '2026-09-21']); // hoy + 20 → hito 20

        $result = app(ReminderService::class)->process();

        $this->assertSame(1, $result['sent']);

        Queue::assertPushed(SendWhatsAppMessage::class, fn ($job) => str_contains($job->message, '20 días'));
    }

    public function test_part_order_received_does_not_dispatch(): void
    {
        Queue::fake();

        $this->createPartOrder(['status' => 'received']);

        $result = app(ReminderService::class)->process();

        $this->assertSame(0, $result['sent']);

        Queue::assertNothingPushed();
    }

    public function test_estimate_waiting_reminder_dispatches_by_cadence(): void
    {
        Queue::fake();

        $this->createEstimate(['last_sent_at' => '2026-08-29 10:00:00']); // 3 días de espera

        $result = app(ReminderService::class)->process();

        $this->assertSame(1, $result['sent']);

        Queue::assertPushed(SendWhatsAppMessage::class, fn ($job) => $job->phone === '987654321');

        $this->assertDatabaseHas('reminder_logs', [
            'type' => 'estimate',
            'target_type' => 'estimate',
            'trigger_date' => '2026-09-01',
            'recipient_type' => 'advisor',
        ]);
    }

    public function test_estimate_not_due_does_not_dispatch(): void
    {
        Queue::fake();

        $this->createEstimate(['last_sent_at' => '2026-08-31 10:00:00']); // 1 día → aún no

        $result = app(ReminderService::class)->process();

        $this->assertSame(0, $result['sent']);

        Queue::assertNothingPushed();
    }

    public function test_before_send_hour_does_not_dispatch(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-01 08:00:00'));

        Queue::fake();

        $this->vehicle->update(['technical_review_date' => '2026-09-11']); // trigger hoy, pero antes de las 09:00

        $result = app(ReminderService::class)->process();

        $this->assertSame(0, $result['sent']);

        Queue::assertNothingPushed();

        $this->assertDatabaseCount('reminder_logs', 0);
    }

    public function test_process_is_idempotent(): void
    {
        Queue::fake();

        $this->vehicle->update(['technical_review_date' => '2026-09-11']);

        app(ReminderService::class)->process();
        app(ReminderService::class)->process();

        Queue::assertPushed(SendWhatsAppMessage::class, 1);

        $this->assertDatabaseCount('reminder_logs', 1);
    }

    public function test_disabled_type_does_not_dispatch(): void
    {
        Queue::fake();

        CompanySetting::query()->update(['reminder_technical_review_enabled' => false]);

        $this->vehicle->update(['technical_review_date' => '2026-09-11']);

        $result = app(ReminderService::class)->process();

        $this->assertSame(0, $result['sent']);

        Queue::assertNothingPushed();

        $this->assertDatabaseCount('reminder_logs', 0);
    }

    public function test_without_phone_is_skipped(): void
    {
        Queue::fake();

        $this->owner->update(['mobile' => null, 'phone' => null]);

        $this->vehicle->update(['technical_review_date' => '2026-09-11']);

        $result = app(ReminderService::class)->process();

        $this->assertSame(0, $result['sent']);

        Queue::assertNothingPushed();

        $this->assertDatabaseCount('reminder_logs', 0);
    }
}
