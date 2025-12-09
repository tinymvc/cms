<?php

namespace Cms\Http\Controllers;

use App\Http\Controllers\Controller;
use Cms\Models\Post;
use Cms\Modules\Bread\Form;
use Cms\Modules\Bread\Table;
use Cms\Modules\CustomPostType;
use Cms\Services\Dashboard;
use Spark\Http\Request;

class PostController extends Controller
{
    private CustomPostType $postType;

    public function __construct(Request $request, Dashboard $dashboard)
    {
        $slug = str($request->getPath())
            ->remove(dashboard_prefix())
            ->trim('/');

        $postType = $dashboard->getPostType($slug->explode('/')->first());
        if (!$postType) {
            abort(404); // Post type not found
        }

        $this->postType = $postType;
    }

    public function index()
    {
        $table = Table::make(new Post);
        $table->column('id')->sortable()->width('80px');
        $table->column('title')->sortable()->searchable();
        $table->column('author_id')->label('Author')->sortable();
        $table->column('status')->sortable()->badge([
            'published' => 'bg-green-500',
            'draft' => 'bg-gray-500',
            'pending' => 'bg-yellow-500',
            'trash' => 'bg-red-500',
        ]);
        $table->column('published_at')->label('Published')->date('M d, Y')->sortable();
        $table->column('created_at')->label('Created')->datetime('M d, Y H:i')->sortable();

        return $table->statusFilter()
            ->searchable()
            ->searchPlaceholder('Search posts...')
            ->defaultSort('id', 'desc')
            ->editAction(fn($post) => admin_url($this->postType->getId() . '/' . $post->id . '/edit'))
            ->deleteAction(fn($post) => admin_url($this->postType->getId() . '/' . $post->id . '/delete'))
            ->bulkDeleteAction()
            ->perPage(15)
            ->render();
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        $post = new Post();
        $form = $this->buildForm($post);

        $form->action(admin_url($this->postType->getId()))
            ->method('POST')
            ->submitLabel('Publish');

        return $form->render();
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        $post = new Post();
        $form = $this->buildForm($post);

        // Validate and save
        $result = $form->save($request);

        if ($result === false) {
            // Validation failed - re-render form with errors
            return $form->render();
        }

        // Success - redirect to index
        return redirect(admin_url($this->postType->getId()))
            ->with('success', 'Post created successfully!');
    }

    /**
     * Show the form for editing a post
     */
    public function edit(int $id)
    {
        $post = Post::findOrFail($id);
        $form = $this->buildForm($post);

        $form->action(admin_url($this->postType->getId() . '/' . $id))
            ->method('PUT')
            ->submitLabel('Update');

        // Load meta data into fields
        $metaTitle = get_post_meta($post->id, 'meta_title', true);
        $metaDescription = get_post_meta($post->id, 'meta_description', true);

        if ($metaTitle) {
            $form->getField('meta_title')?->value($metaTitle);
        }
        if ($metaDescription) {
            $form->getField('meta_description')?->value($metaDescription);
        }

        return $form->render();
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, int $id)
    {
        $post = Post::findOrFail($id);
        $form = $this->buildForm($post);

        // Validate and save
        $result = $form->save($request);

        if ($result === false) {
            // Validation failed - re-render form with errors
            return $form->render();
        }

        // Success - redirect to index
        return redirect(admin_url($this->postType->getId()))
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post
     */
    public function destroy(int $id)
    {
        $post = Post::findOrFail($id);
        $post->remove();

        return redirect(admin_url($this->postType->getId()))
            ->with('success', 'Post deleted successfully!');
    }

    /**
     * Build form with all fields (reusable for create and edit)
     */
    private function buildForm(Post $post): Form
    {
        $form = Form::make($post);

        $form->fillable([
            'author_id',
            'title',
            'slug',
            'image',
            'excerpt',
            'content',
            'type',
            'status',
            'published_at',
            'scheduled_at',
        ]);

        // Add tabs
        $form->tab('general', 'General');
        $form->tab('meta', 'Meta Data');
        $form->tab('advanced', 'Advanced');

        // General tab fields
        $form->text('title')
            ->label('Title')
            ->placeholder('Enter post title')
            ->rules('required|min:3|max:250')
            ->tab('general')
            ->columnSpan(12);

        $form->text('slug')
            ->label('Slug')
            ->placeholder('post-url-slug')
            ->helperText('Leave empty to auto-generate from title')
            ->rules('nullable|alpha_dash|max:250')
            ->tab('general')
            ->columnSpan(12);

        $form->richEditor('content')
            ->label('Content')
            ->rules('nullable')
            ->tab('general')
            ->columnSpan(12);

        $form->textarea('excerpt')
            ->label('Excerpt')
            ->placeholder('Brief description...')
            ->rules('nullable|max:255')
            ->tab('general')
            ->columnSpan(12);

        $form->image('image')
            ->label('Featured Image')
            ->rules('nullable|image')
            ->tab('general')
            ->columnSpan(6);

        $form->select('status')
            ->label('Status')
            ->options([
                'draft' => 'Draft',
                'pending' => 'Pending Review',
                'published' => 'Published',
            ])
            ->default('draft')
            ->rules('required|in:draft,pending,published')
            ->tab('general')
            ->columnSpan(6);

        // Meta tab fields
        $form->text('meta_title')
            ->label('Meta Title')
            ->placeholder('SEO title')
            ->rules('nullable|max:60')
            ->tab('meta')
            ->columnSpan(12);

        $form->textarea('meta_description')
            ->label('Meta Description')
            ->placeholder('SEO description')
            ->rules('nullable|max:160')
            ->tab('meta')
            ->columnSpan(12);

        // Advanced tab fields
        $form->datetime('published_at')
            ->label('Publish Date')
            ->helperText('Schedule post for future publication')
            ->rules('nullable|date')
            ->tab('advanced')
            ->columnSpan(6);

        $form->datetime('scheduled_at')
            ->label('Scheduled At')
            ->rules('nullable|date')
            ->tab('advanced')
            ->columnSpan(6);

        // Set callbacks
        $form->beforeSave(function ($data, $model) {
            // Auto-generate slug if empty
            if (empty($data['slug']) && !empty($data['title'])) {
                $data['slug'] = str($data['title'])->slug()->toString();
            }

            // Set author_id for new posts
            if (!$model->exists() && !isset($data['author_id'])) {
                $data['author_id'] = auth()->user()->id ?? 1;
            }

            // Set type to post type ID
            $data['type'] = $this->postType->getId();

            return $data;
        });

        $form->afterSave(function ($model, $data) {
            // Handle meta fields
            if (isset($data['meta_title'])) {
                update_post_meta($model->id, 'meta_title', $data['meta_title']);
            }
            if (isset($data['meta_description'])) {
                update_post_meta($model->id, 'meta_description', $data['meta_description']);
            }
        });

        return $form;
    }
}