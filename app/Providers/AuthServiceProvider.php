<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Publication;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /**
         * Gate untuk menentukan siapa yang boleh mengelola sebuah publikasi (misal: menugaskan reviewer).
         * Hanya user yang membuat publikasi yang diizinkan.
         */
        Gate::define('manage-publication', function (User $user, Publication $publication) {
            return $user->id === $publication->creator_id;
        });

        /**
         * Gate untuk menentukan siapa yang boleh melihat dan mereview sebuah publikasi.
         * Diizinkan jika user adalah pembuatnya ATAU merupakan salah satu reviewer yang ditugaskan.
         */
        Gate::define('view-publication', function (User $user, Publication $publication) {
            // Cek jika user adalah pembuatnya
            if ($user->id === $publication->creator_id) {
                return true;
            }

            // Cek jika user ada di dalam daftar reviewer
            return $publication->reviewers->contains($user);
        });

        // Gate untuk mengizinkan update komentar
        Gate::define('update-comment', function (User $user, Comment $comment) {
            return $user->id === $comment->user_id;
        });

        // Gate untuk mengizinkan hapus komentar
        Gate::define('delete-comment', function (User $user, Comment $comment) {
            return $user->id === $comment->user_id;
        });
    }
}
