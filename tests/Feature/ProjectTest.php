<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_guest_cannot_create_a_project()
    {
        $category = factory(Category::class)->create();
        $data = [
            'title'       => $this->faker->sentence,
            'category_id' => $category->id,
            'description' => $this->faker->paragraph,
            'visible'     => $this->faker->boolean($chanceOfGettingTrue = 80),
            'order'       => $this->faker->randomDigit,
            'status'      => $this->faker->randomElement([
                'unknown',
                'open',
                'scheduled',
                'in_development',
                'completed',
                'cancelled',
            ]),
        ];

        $response = $this->json('POST', route('api.projects.store'), $data);
        $response->assertStatus(401);
    }

    public function test_user_cannot_create_a_project()
    {
        $this->loginAsUser();

        $category = factory(Category::class)->create();
        $data = [
            'title'       => $this->faker->sentence,
            'category_id' => $category->id,
            'description' => $this->faker->paragraph,
            'visible'     => $this->faker->boolean($chanceOfGettingTrue = 80),
            'order'       => $this->faker->randomDigit,
            'status'      => $this->faker->randomElement([
                'unknown',
                'open',
                'scheduled',
                'in_development',
                'completed',
                'cancelled',
            ]),
        ];

        $response = $this->json('POST', route('api.projects.store'), $data);
        $response->assertStatus(403);
    }

    public function test_admin_can_create_a_project()
    {
        $this->loginAsAdmin();

        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 2)->create();
        $data = [
            'title'       => $this->faker->sentence,
            'category_id' => $category->id,
            'description' => $this->faker->paragraph,
            'visible'     => $this->faker->boolean($chanceOfGettingTrue = 80),
            'order'       => $this->faker->randomDigit,
            'status'      => $this->faker->randomElement([
                'unknown',
                'open',
                'scheduled',
                'in_development',
                'completed',
                'cancelled',
            ]),
            'tags'        => $tags->pluck('id'),
        ];

        $response = $this->json('POST', route('api.projects.store'), $data);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'id',
            'title',
            'slug',
            'category',
            'description',
            'visible',
            'order',
            'status',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_guest_cannot_edit_a_project()
    {
        $project = factory(Project::class)->create();

        $data = [
            'title' => $this->faker->sentence,
        ];

        $response = $this->json('PUT', route('api.projects.update', $project), $data);
        $response->assertStatus(401);
    }

    public function test_user_cannot_edit_a_project()
    {
        $this->loginAsUser();

        $project = factory(Project::class)->create();

        $data = [
            'title' => $this->faker->sentence,
        ];

        $response = $this->json('PUT', route('api.projects.update', $project), $data);
        $response->assertStatus(403);
    }

    public function test_admin_can_edit_a_project()
    {
        $this->loginAsAdmin();

        $project = factory(Project::class)->create();
        $tags = factory(Tag::class, 2)->create();
        $data = [
            'title' => $this->faker->sentence,
            'tags'  => $tags->pluck('id'),
        ];

        $response = $this->json('PUT', route('api.projects.update', $project), $data);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'id',
            'title',
            'slug',
            'category',
            'description',
            'visible',
            'order',
            'status',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_guest_cannot_delete_a_project()
    {
        $project = factory(Project::class)->create();

        $response = $this->json('DELETE', route('api.projects.destroy', $project));
        $response->assertStatus(401);
    }

    public function test_user_cannot_delete_a_project()
    {
        $this->loginAsUser();

        $project = factory(Project::class)->create();

        $response = $this->json('DELETE', route('api.projects.destroy', $project));
        $response->assertStatus(403);
    }

    public function test_admin_can_delete_a_project()
    {
        $this->loginAsAdmin();

        $project = factory(Project::class)->create();
        $response = $this->json('DELETE', route('api.projects.destroy', $project));
        $response->assertSuccessful();
    }

    public function test_admin_can_restore_a_category()
    {
        $this->loginAsAdmin();
        $project = factory(Project::class)->states('softDeleted')->create();
        $response = $this->json('PUT', route('api.projects.restore', $project));
        $response->assertSuccessful();
    }

    public function test_admin_can_delete_permanently_a_category()
    {
        $this->loginAsAdmin();
        $project = factory(Project::class)->states('softDeleted')->create();
        $response = $this->json('PUT', route('api.projects.delete-permanently', $project));
        $response->assertSuccessful();
        $this->assertNull($project->fresh());
    }

    public function test_get_projects()
    {
        factory(Project::class, 5)->create();
        $response = $this->json('GET', route('api.projects.index'));
        $response->assertSuccessful();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'slug',
                'category',
                'description',
                'visible',
                'order',
                'status',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_get_project()
    {
        $project = factory(Project::class)->create();

        $response = $this->json('GET', route('api.projects.show', $project));
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'id',
            'title',
            'slug',
            'category',
            'description',
            'visible',
            'order',
            'status',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_add_and_remove_tag_from_project()
    {
        $project = factory(Project::class)->create();
        $tag = factory(Tag::class)->create();
        $project->tags()->attach($tag);
        $this->assertTrue($project->hasTag($tag));
        $project->tags()->detach($tag);
        $this->assertFalse($project->hasTag($tag));
    }
}
