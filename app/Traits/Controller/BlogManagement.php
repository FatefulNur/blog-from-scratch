<?php

namespace App\Traits\Controller;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait BlogManagement
{
    /**
     * validating blog request data
     */
    private function validatedRequests(Request $request)
    {
        return $request->validate([
            'title' => 'required|min:5',
            'short_desc' => 'nullable',
            'description' => 'required|min:20'
        ]);
    }

    /**
     * check and store in images database
     */
    private function processForThumnailStoring(Request $request, $blog)
    {
        if ($request->hasFile('thumbnail')) {

            $request->validate([
                'thumbnail' => 'mimes:png,jpg,jpeg,gif|max:2048'
            ]);

            $t_name = $request->input('t_name');
            $t_caption = $request->input('t_caption');
            $t_details = $request->input('t_details');

            $thumbnail = $request->file('thumbnail');
            $thumbnail_path = '/uploads/thumbnail';
            $thumbnail_name = $thumbnail->hashName($thumbnail_path);

            $thumbnail->move(public_path('uploads/thumbnail'), $thumbnail_name);

            $blog->image()->create([
                'name' => $t_name,
                'caption' => $t_caption,
                'details' => $t_details,
                'path' => $thumbnail_name
            ]);

            $this->reCachedThumbnails();

            session()->flash('image', 'image created');
        }

        return;
    }

    /**
     * Create and storing gallery of a blog
     */
    private function processForGalleryStoring(Request $request, $blog)
    {
        if ($request->hasFile('galleries')) {

            $request->validate([
                'galleries.*' => 'mimes:png,jpg,jpeg,gif|max:2048'
            ]);

            $g_name = $request->input('g_name');
            $g_caption = $request->input('g_caption');
            $g_details = $request->input('g_details');

            $galleries = $request->file('galleries');
            $gallery_path = '/uploads/gallery';

            $store_gallery = $blog->gallery()->create([
                'user_id' => $request->input('user_id')
            ]);

            foreach ($galleries as $gallery) {
                $gallery_name = $gallery->hashName($gallery_path);

                $gallery->move(public_path('uploads/gallery'), $gallery_name);

                $store_gallery->images()->create([
                    'name' => $g_name,
                    'caption' => $g_caption,
                    'details' => $g_details,
                    'path' => $gallery_name
                ]);
            }

            $this->reCachedGalleryImages();

            session()->flash('gallery', 'gallery created');
        }

        return;
    }

    /**
     * Updating thumbnail of a blog
     */
    private function processForThumbnailUpdting(Request $request, $blog)
    {

        if ($blog->image()->get()->isNotEmpty()) {

            if ($request->hasFile('thumbnail')) {

                $request->validate([
                    'thumbnail' => 'mimes:png,jpg,jpeg,gif|max:2048'
                ]);


                $t_name = $request->input('t_name');
                $t_caption = $request->input('t_caption');
                $t_details = $request->input('t_details');

                $thumbnail = $request->file('thumbnail');
                $thumbnail_path = '/uploads/thumbnail';

                if (file_exists(public_path($blog->image->path))) {
                    File::delete(public_path($blog->image->path));
                }

                $thumbnail_name = $thumbnail->hashName($thumbnail_path);
                $thumbnail->move(public_path('uploads/thumbnail'), $thumbnail_name);
                $blog->image()->update([
                    'name' => $t_name,
                    'caption' => $t_caption,
                    'details' => $t_details,
                    'path' => $thumbnail_name
                ]);

                $this->reCachedThumbnails();
            }

            session()->flash('image', 'image updated');
        } else {
            $this->processForThumnailStoring($request, $blog);
        }


        return;
    }

    /**
     * Updating gallery of a blog
     */
    private function processForGalleryUpdting(Request $request, $blog)
    {
        if ($blog->gallery->images->isNotEmpty()) {
            if ($request->hasFile('galleries')) {

                $request->validate([
                    'galleries.*' => 'mimes:png,jpg,jpeg,gif|max:2048'
                ]);

                $g_name = $request->input('g_name');
                $g_caption = $request->input('g_caption');
                $g_details = $request->input('g_details');

                $galleries = $request->file('galleries');
                $gallery_path = '/uploads/gallery';


                // removing all previous images from storage
                $blog->gallery->images->each(function ($item) {
                    if (!file_exists(public_path($item->path))) {
                        return;
                    }
                    File::delete(public_path($item->path));
                });

                $images = [];

                // looping through each uploads
                foreach ($galleries as $key => $gallery) {

                    $gallery_name = $gallery->hashName($gallery_path);
                    $gallery->move(public_path('uploads/gallery'), $gallery_name);

                    // loading images before submit to database
                    array_push($images, $gallery_name);

                    // fetching current database gallery images
                    $id = $blog->gallery->images()->where('id', optional(request('ids'))[$key])->first();

                    /**
                     * processing for remaining ids that should be deleted if uploads image
                     * are less than the database images in order to avoid null image printed
                     * issues inside html document...
                     */
                    // count for offset
                    $count = ($key + 1);
                    // couting database images
                    $total_images = $blog->gallery->images->count();
                    // offset for remaining ids
                    $offset = ($count <= $total_images) ? $count : $total_images;
                    // collect remaining ids
                    $remnantIds = array_slice(request('ids'), $offset);

                    // update or create image base on collected ids
                    $blog->gallery->images()->updateOrCreate(
                        ['id' => optional($id)->id],
                        [
                            'name' => $g_name,
                            'caption' => $g_caption,
                            'details' => $g_details,
                            'path' => $images[$key]
                        ]
                    );
                }

                // deleting remaining images
                if(!empty($remnantIds)) {
                    $blog->gallery->images->whereIn('id', $remnantIds)->each->delete();
                }

                $this->reCachedGalleryImages();

                session()->flash('gallery', 'gallery updated');
            }
        } else {
            $this->processForGalleryStoring($request, $blog);
        }


        return;
    }

    /**
     * Deleting thumbnail when taking action of empty trash
     */
    private function processForDeletingImageFromBin(Collection $blog)
    {
        if (!empty($blog)) {
            $blog->each(function ($item) {
                if (empty($item->image)) {
                    return;
                }

                if (file_exists(public_path($item->image->path))) {
                    File::delete(public_path($item->image->path));
                }

                return $item->image()->delete();
            });

            session()->flash('image', 'image deleted');
        }

        return;
    }

    /**
     * Deleting gallery when taking action of empty trash
     */
    private function processForDeletingGalleryFromBin(Collection $blog)
    {
        if (!empty($blog)) {
            $blog->each(function ($blog) {
                if ($blog->gallery->images->isEmpty()) {
                    return;
                }

                $blog->gallery->images->each(function ($item) {
                    if (!file_exists(public_path($item->path))) {
                        return;
                    }
                    File::delete(public_path($item->path));
                });

                return $blog->gallery->images->each->delete();
            });

            $blog->each(function ($item) {
                return $item->gallery()->delete();
            });

            session()->flash('gallery', 'gallery deleted');
        }

        return;
    }
}
