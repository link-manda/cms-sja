<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user terautentikasi untuk keperluan testing
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_projects_routes(): void
    {
        $this->get(route('projects.index'))->assertRedirect(route('login'));
        $this->get(route('projects.create'))->assertRedirect(route('login'));
        $this->post(route('projects.store'), [])->assertRedirect(route('login'));
    }

    public function test_guest_can_view_public_project_case_study(): void
    {
        $project = Project::factory()->create([
            'title' => 'Ocean Residence',
            'slug' => 'ocean-residence',
            'location' => 'Pecatu, Bali',
            'description' => 'A stunning ocean residence case study details.',
            'image' => 'villa.jpg',
            'status' => 'Ongoing',
        ]);

        $response = $this->get(route('public.projects.show', $project->slug));

        $response->assertStatus(200);
        $response->assertSee('Ocean Residence');
        $response->assertSee('Pecatu, Bali');
        $response->assertSee('A stunning ocean residence case study details.');
    }

    public function test_invalid_slug_returns_404(): void
    {
        $response = $this->get('/case-study/non-existent-slug');
        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_view_projects_list(): void
    {
        $project = Project::factory()->create([
            'title' => 'Villa Canggu',
            'slug' => 'villa-canggu',
            'location' => 'Bali',
            'description' => 'A beautiful tropical villa.',
            'image' => 'villa.jpg',
            'status' => 'Ongoing',
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee('Villa Canggu');
        $response->assertSee('Bali');
    }

    public function test_authenticated_user_can_view_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('projects.create'));

        $response->assertStatus(200);
        $response->assertSee('Project Name');
    }

    public function test_authenticated_user_can_store_project(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('project.jpg');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Modern Residence',
            'slug' => 'modern-residence',
            'category_id' => $category->id,
            'location' => 'Jakarta',
            'description' => 'Minimalist modern townhouse.',
            'image' => $image,
            'status' => 'Ongoing',
            'meta_title' => 'Modern Residence SEO',
            'meta_description' => 'Modern Residence description SEO',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'title' => 'Modern Residence',
            'slug' => 'modern-residence',
            'category_id' => $category->id,
            'location' => 'Jakarta',
        ]);

        // Ambil project yang berhasil dibuat
        $project = Project::first();
        $this->assertNotNull($project->image);
        Storage::disk('public')->assertExists('projects/'.$project->image);
    }

    public function test_authenticated_user_can_view_edit_form(): void
    {
        $project = Project::factory()->create([
            'title' => 'Office Building',
            'slug' => 'office-building',
            'location' => 'Surabaya',
            'description' => 'Commercial high-rise.',
            'image' => 'office.jpg',
            'status' => 'Completed',
        ]);

        $response = $this->actingAs($this->user)->get(route('projects.edit', $project->id));

        $response->assertStatus(200);
        $response->assertSee('Office Building');
    }

    public function test_authenticated_user_can_update_project(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create([
            'title' => 'Old House',
            'slug' => 'old-house',
            'location' => 'Bandung',
            'description' => 'Retro house.',
            'image' => 'old.jpg',
            'status' => 'Ongoing',
        ]);

        $newImage = UploadedFile::fake()->image('new_house.jpg');
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user)->put(route('projects.update', $project->id), [
            'title' => 'Renovated House',
            'slug' => 'renovated-house',
            'category_id' => $category->id,
            'location' => 'Bandung',
            'description' => 'Modern renovated house.',
            'image' => $newImage,
            'status' => 'Completed',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Renovated House',
            'slug' => 'renovated-house',
            'category_id' => $category->id,
            'status' => 'Completed',
        ]);

        $project->refresh();
        Storage::disk('public')->assertExists('projects/'.$project->image);
        // Gambar lama old.jpg harus terhapus dari disk
        Storage::disk('public')->assertMissing('projects/old.jpg');
    }

    public function test_authenticated_user_can_delete_project(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create([
            'title' => 'Temporary Project',
            'slug' => 'temporary-project',
            'location' => 'Depok',
            'description' => 'To be deleted.',
            'image' => 'temp.jpg',
            'status' => 'Ongoing',
        ]);

        // Buat file palsu di public storage untuk menguji penghapusan
        Storage::disk('public')->put('projects/temp.jpg', 'fake content');

        $response = $this->actingAs($this->user)->delete(route('projects.destroy', $project->id));

        $response->assertRedirect(route('projects.index'));
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        Storage::disk('public')->assertExists('projects/temp.jpg');
    }

    public function test_roi_estimation_is_required_when_property_is_for_sale_or_investment(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        // 1. Property type is required if is_for_sale_or_rent is true
        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Property Without Type',
            'slug' => 'property-without-type',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'A project test description.',
            'image' => UploadedFile::fake()->image('thumb.jpg'),
            'status' => 'Ongoing',
            'is_for_sale_or_rent' => '1',
            'property_type' => '',
        ]);
        $response->assertSessionHasErrors(['property_type']);

        // 2. Invalid property_type (outside Rent, Sale, Investment) is rejected
        $responseInvalid = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Property With Invalid Type',
            'slug' => 'property-with-invalid-type',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'A project test description.',
            'image' => UploadedFile::fake()->image('thumb.jpg'),
            'status' => 'Ongoing',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Commercial',
        ]);
        $responseInvalid->assertSessionHasErrors(['property_type']);

        // 3. Sale without roi_estimation is rejected on store
        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Sale Project Without ROI',
            'slug' => 'sale-project-without-roi',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'A project test description.',
            'image' => UploadedFile::fake()->image('thumb.jpg'),
            'status' => 'Ongoing',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Sale',
            'price' => 500000000,
            'roi_estimation' => '',
        ]);
        $response->assertSessionHasErrors(['roi_estimation']);

        // 4. Investment without roi_estimation is rejected on store
        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Investment Project Without ROI',
            'slug' => 'investment-project-without-roi',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'A project test description.',
            'image' => UploadedFile::fake()->image('thumb.jpg'),
            'status' => 'Ongoing',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Investment',
            'price' => 1200000000,
            'roi_estimation' => '',
        ]);
        $response->assertSessionHasErrors(['roi_estimation']);

        // 5. Update validation: updating to Sale without roi_estimation fails validation
        $existingProject = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Existing Ongoing Project',
            'slug' => 'existing-ongoing-project',
            'is_for_sale_or_rent' => false,
        ]);

        $responseUpdate = $this->actingAs($this->user)->put(route('projects.update', $existingProject->id), [
            'title' => 'Existing Ongoing Project Updated',
            'slug' => 'existing-ongoing-project',
            'category_id' => $category->id,
            'location' => 'Bali',
            'description' => 'Updated description.',
            'status' => 'Ongoing',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Sale',
            'roi_estimation' => '',
        ]);
        $responseUpdate->assertSessionHasErrors(['roi_estimation']);
    }

    public function test_happy_path_store_and_update_for_sale_and_investment(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        // 1. Happy path store for Sale
        $responseSale = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Canggu Villa Sale',
            'slug' => 'canggu-villa-sale',
            'category_id' => $category->id,
            'location' => 'Canggu, Bali',
            'description' => 'Freehold luxury villa.',
            'image' => UploadedFile::fake()->image('villa.jpg'),
            'status' => 'Completed',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Sale',
            'price' => 7500000000,
            'roi_estimation' => 'Estimated annual ROI 12.5%.',
        ]);

        $responseSale->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'slug' => 'canggu-villa-sale',
            'property_type' => 'Sale',
            'price' => 7500000000,
            'roi_estimation' => 'Estimated annual ROI 12.5%.',
            'is_for_sale_or_rent' => true,
        ]);

        // 2. Happy path update for Investment
        $saleProject = Project::where('slug', 'canggu-villa-sale')->first();
        $responseUpdate = $this->actingAs($this->user)->put(route('projects.update', $saleProject->id), [
            'title' => 'Canggu Commercial Hub',
            'slug' => 'canggu-villa-sale',
            'category_id' => $category->id,
            'location' => 'Canggu, Bali',
            'description' => 'Converted to commercial boutique suites.',
            'status' => 'Completed',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Investment',
            'price' => 12000000000,
            'roi_estimation' => 'Commercial yield 15% with 5-year ROI payback.',
        ]);

        $responseUpdate->assertRedirect(route('projects.index'));
        $saleProject->refresh();
        $this->assertEquals('Investment', $saleProject->property_type);
        $this->assertEquals(12000000000, (int) $saleProject->price);
        $this->assertEquals('Commercial yield 15% with 5-year ROI payback.', $saleProject->roi_estimation);
        $this->assertTrue((bool) $saleProject->is_for_sale_or_rent);
    }

    public function test_roi_estimation_is_optional_and_nulled_when_property_is_for_rent(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        // 1. Rent without ROI is accepted
        $response = $this->actingAs($this->user)->post(route('projects.store'), [
            'title' => 'Kos-kosan Sanur',
            'slug' => 'kos-kosan-sanur',
            'category_id' => $category->id,
            'location' => 'Sanur, Bali',
            'description' => 'Kamar kos fully furnished AC & Wi-Fi.',
            'image' => UploadedFile::fake()->image('kos.jpg'),
            'status' => 'Ongoing',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Rent',
            'price' => 2500000,
            'roi_estimation' => '',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'slug' => 'kos-kosan-sanur',
            'property_type' => 'Rent',
            'price' => 2500000,
            'roi_estimation' => null,
        ]);

        // 2. Even if roi_estimation string is passed for Rent, Service layer nullifies it
        $project = Project::where('slug', 'kos-kosan-sanur')->first();
        $this->actingAs($this->user)->put(route('projects.update', $project->id), [
            'title' => 'Kos-kosan Sanur Renovated',
            'slug' => 'kos-kosan-sanur',
            'category_id' => $category->id,
            'location' => 'Sanur, Bali',
            'description' => 'Kamar kos fully furnished updated.',
            'status' => 'Completed',
            'is_for_sale_or_rent' => '1',
            'property_type' => 'Rent',
            'price' => 3000000,
            'roi_estimation' => 'Unwanted ROI text',
        ]);

        $project->refresh();
        $this->assertEquals('Rent', $project->property_type);
        $this->assertNull($project->roi_estimation);
    }

    public function test_disabling_property_promotion_nullifies_all_offer_fields(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Luxury Villa For Sale',
            'slug' => 'luxury-villa-for-sale',
            'is_for_sale_or_rent' => true,
            'property_type' => 'Sale',
            'price' => 8500000000,
            'roi_estimation' => 'Estimated 14% annual yield',
        ]);

        // Disable promotion by sending is_for_sale_or_rent as 0 alongside dirty stale fields
        $response = $this->actingAs($this->user)->put(route('projects.update', $project->id), [
            'title' => 'Luxury Villa Standard Portfolio',
            'slug' => 'luxury-villa-for-sale',
            'category_id' => $category->id,
            'location' => 'Canggu, Bali',
            'description' => 'Standard portfolio display without commercial pricing.',
            'status' => 'Completed',
            'is_for_sale_or_rent' => '0',
            'property_type' => 'Sale',
            'price' => 8500000000,
            'roi_estimation' => 'Stale residual ROI text',
        ]);

        $response->assertRedirect(route('projects.index'));
        $project->refresh();
        $this->assertFalse((bool) $project->is_for_sale_or_rent);
        $this->assertNull($project->property_type);
        $this->assertNull($project->price);
        $this->assertNull($project->roi_estimation);
    }

    public function test_public_case_study_displays_correct_offer_type_and_roi_visibility(): void
    {
        $category = Category::factory()->create();

        // 1. Rent Project (Kos-kosan) with non-null ROI in DB to verify view actively suppresses it
        $rentProject = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Modern Boarding House',
            'slug' => 'modern-boarding-house',
            'is_for_sale_or_rent' => true,
            'property_type' => 'Rent',
            'price' => 2000000,
            'roi_estimation' => 'Secret ROI that must be suppressed for Rent',
        ]);

        $responseRent = $this->get(route('public.projects.show', $rentProject->slug));
        $responseRent->assertStatus(200);
        $responseRent->assertSee('Rental Property Offering');
        $responseRent->assertSee('For Rent');
        $responseRent->assertSee('Inquire Rental Availability');
        $responseRent->assertSee(urlencode('interested in renting / boarding room'));
        $responseRent->assertDontSee('Projected ROI & Feasibility');
        $responseRent->assertDontSee('Secret ROI that must be suppressed for Rent');

        // 2. Sale Project
        $saleProject = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Hillside Villa Canggu',
            'slug' => 'hillside-villa-canggu',
            'is_for_sale_or_rent' => true,
            'property_type' => 'Sale',
            'price' => 4500000000,
            'roi_estimation' => 'Estimated capital gain 15% within 3 years.',
        ]);

        $responseSale = $this->get(route('public.projects.show', $saleProject->slug));
        $responseSale->assertStatus(200);
        $responseSale->assertSee('Property For Sale');
        $responseSale->assertSee('For Sale');
        $responseSale->assertSee('Inquire Property Purchase');
        $responseSale->assertSee(urlencode('interested in purchasing property'));
        $responseSale->assertSee('Projected ROI & Feasibility');
        $responseSale->assertSee('Estimated capital gain 15% within 3 years.');

        // 3. Investment Project
        $invProject = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Seminyak Boutique Hotel',
            'slug' => 'seminyak-boutique-hotel',
            'is_for_sale_or_rent' => true,
            'property_type' => 'Investment',
            'price' => 15000000000,
            'roi_estimation' => 'Estimated annual ROI 11.5% with 6-year payback period.',
        ]);

        $responseInv = $this->get(route('public.projects.show', $invProject->slug));
        $responseInv->assertStatus(200);
        $responseInv->assertSee('Commercial & Property Investment');
        $responseInv->assertSee('Investment Opportunity');
        $responseInv->assertSee('Inquire Investment Specifications');
        $responseInv->assertSee(urlencode('interested in the property investment opportunity'));
        $responseInv->assertSee('Projected ROI & Feasibility');
        $responseInv->assertSee('Estimated annual ROI 11.5% with 6-year payback period.');

        // 4. Promoted Project with Null Price renders Price on Request without deprecation warnings
        $nullPriceProject = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Private Exclusive Estate',
            'slug' => 'private-exclusive-estate',
            'is_for_sale_or_rent' => true,
            'property_type' => 'Sale',
            'price' => null,
            'roi_estimation' => 'Exclusive yield details upon private NDA.',
        ]);

        $responseNullPrice = $this->get(route('public.projects.show', $nullPriceProject->slug));
        $responseNullPrice->assertStatus(200);
        $responseNullPrice->assertSee('Price on Request');

        // 5. Standard Portfolio Project (Promotion disabled) completely suppresses offer card
        $portfolioProject = Project::factory()->create([
            'category_id' => $category->id,
            'title' => 'Standard Client Residence',
            'slug' => 'standard-client-residence',
            'is_for_sale_or_rent' => false,
            'property_type' => null,
            'price' => null,
            'roi_estimation' => null,
        ]);

        $responsePortfolio = $this->get(route('public.projects.show', $portfolioProject->slug));
        $responsePortfolio->assertStatus(200);
        $responsePortfolio->assertDontSee('Pricing Valuation');
        $responsePortfolio->assertDontSee('Inquire Property Purchase');
    }
}
