<?php

namespace Tests\Feature;

use App\Models\JobOrder;
use App\Models\Tarif;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'teknisi@gajianarmn.com',
        ]);
    }

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_and_see_dashboard()
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_user_can_create_job_order_with_snapshot_rate()
    {
        $tarif = Tarif::create([
            'kategori' => 'Pemasangan EDC',
            'tarif_berhasil' => 15000,
            'tarif_gagal' => 10000,
        ]);

        $response = $this->actingAs($this->user)->post('/job-orders', [
            'tarif_id' => $tarif->id,
            'status' => 'berhasil',
            'tanggal' => now()->toDateString(),
            'catatan' => 'Test job',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('job_orders', [
            'tarif_id' => $tarif->id,
            'kategori' => 'Pemasangan EDC',
            'status' => 'berhasil',
            'tarif' => 15000,
            'tanggal' => now()->toDateString(),
        ]);

        // Change master tariff in tarifs table
        $tarif->update([
            'tarif_berhasil' => 20000,
        ]);

        // Assert job_orders snapshot remains unchanged (15000)
        $jobOrder = JobOrder::first();
        $this->assertEquals(15000, $jobOrder->tarif);
    }

    public function test_user_can_export_csv()
    {
        $tarif = Tarif::create([
            'kategori' => 'Init',
            'tarif_berhasil' => 15000,
            'tarif_gagal' => 10000,
        ]);

        JobOrder::create([
            'tarif_id' => $tarif->id,
            'kategori' => $tarif->kategori,
            'status' => 'berhasil',
            'tarif' => 15000,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->user)->get('/export/csv');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }
}
