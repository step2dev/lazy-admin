<?php

namespace Step2dev\LazyAdmin\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Step2dev\LazyAdmin\Authorization\AuthorizationManager;

class AccessController extends Controller
{
    public function __construct(private readonly AuthorizationManager $authorization) {}

    public function __invoke(): View
    {
        $user = Auth::guard((string) config('lazy.auth.guard', 'web'))->user();

        abort_unless(
            $user && (
                ! config('lazy.admin.permissions.enforce', true)
                || $user->can('roles.view')
                || $user->can('permissions.view')
            ),
            403
        );

        return view('lazy::access.index', [
            'roles' => $this->authorization->roles()->load('permissions'),
            'permissions' => $this->authorization->permissions()->load('roles'),
        ]);
    }
}
