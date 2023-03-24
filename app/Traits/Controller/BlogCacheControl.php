<?php

namespace App\Traits\Controller;

use App\Models\Blog;
use App\Models\Gallery;
use App\Models\Image;
use Illuminate\Support\Facades\Cache;

trait BlogCacheControl
{
    private static $cache_blogs = "xs-blogs-caches";
    private static $cache_only_trashed_blogs = "xs-only-trashed-blogs-caches";
    private static $cache_blog_thumbs = "xs-blog-thumb-caches";
    private static $cache_blog_gallery_images = "xs-blog-gallery-images-caches";
    private static $cache_timeout = 60 * 60 * 24 * 15;

     /**
     * Cache the entire blogs data
     */
    private function cacheBlogs()
    {
        if (!Cache::has(self::$cache_blogs)) {
            Cache::remember(self::$cache_blogs, self::$cache_timeout, function () {
                return Blog::withoutTrashed()->orderByDesc('id')->get();
            });
        }
    }

    /**
     * Forget & Cache the entire blogs data
     */
    private function reCachedBlogs()
    {
        Cache::forget(self::$cache_blogs);

        $this->cacheBlogs();
    }

    /**
     * Cache the only trashed blogs data
     */
    private function cacheOnlyTrashedBlogs()
    {
        if (!Cache::has(self::$cache_only_trashed_blogs)) {
            Cache::add(self::$cache_only_trashed_blogs, Blog::onlyTrashed()->orderByDesc('deleted_at')->get(), self::$cache_timeout);
        }
    }

    /**
     * Forget & Cache only trashed blogs data
     */
    private function reCachedOnlyTrashedBlogs()
    {
        Cache::forget(self::$cache_only_trashed_blogs);

        $this->cacheOnlyTrashedBlogs();
    }

    /**
     * Cache the entire blogs thumbnails
     */
    private function cacheThumbnails()
    {
        if (!Cache::has(self::$cache_blog_thumbs)) {
            Cache::remember(self::$cache_blog_thumbs, self::$cache_timeout, function () {
                return Image::whereHasMorph(
                    'imagable',
                    Blog::class
                )->get();
            });
        }
    }

    /**
     * Forget & Cache the entire blogs thumbnails
     */
    private function reCachedThumbnails()
    {
        Cache::forget(self::$cache_blog_thumbs);

        $this->cacheThumbnails();
    }

    /**
     * Cache the entire blogs gallery images
     */
    private function cacheGalleryImages()
    {
        if (!Cache::has(self::$cache_blog_gallery_images)) {
            Cache::remember(self::$cache_blog_gallery_images, self::$cache_timeout, function () {
                return Image::whereHasMorph(
                    'imagable',
                    Gallery::class
                )->get();
            });
        }
    }

    /**
     * Forget & Cache the entire blogs gallery images
     */
    private function reCachedGalleryImages()
    {
        Cache::forget(self::$cache_blog_gallery_images);

        $this->cacheGalleryImages();
    }
}
