<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthService
{
    protected TokenService $tokens;
    protected UserRepositoryInterface $users;
    protected AuditLogRepositoryInterface $audit;
    private Otp $otp;

    public function __construct(
        TokenService $tokens,
        UserRepositoryInterface $users,
        AuditLogRepositoryInterface $audit
    ) {
        $this->users = $users;
        $this->tokens = $tokens;
        $this->audit = $audit;
        $this->otp = new Otp();
    }

    public function register(array $data, $role = null)
    {
        if ($role && !Role::where('name', $role)->exists()) {
            return ['user' => null, 'message' => 'Invalid role', 'code' => 422];
        }

        $user = $this->users->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $roleModel = Role::findByName($role, 'api');
        $user->assignRole($roleModel);

        $tokens = $this->tokens->createTokens($user);
        $user->notify(new EmailVerificationNotification());

        $this->audit->log(
            user_id: $user->id ?? null,
            module: 'users',
            action: 'register',
            description: 'تم إنشاء مستخدم جديد',
            old: null,
            new: $user->toArray()
        );


        return [
            'user'   => $this->formatUser($user),
            'tokens' => $tokens,
            'message' => 'Registered successfully',
            'code' => 200,
        ];
    }

    public function login(array $data, string $requiredRole): array
    {
        $user = $this->users->findByEmail($data['email']);

        $this->audit->log(
            user_id:$user->id ?? null,
            module: 'users',
            action: 'login_attempt',
            description: 'محاولة تسجيل دخول',
            old: null,
            new: ['email' => $data['email']]
        );

        if (!$user) {
            return [
                'user' => null,
                'message' => 'Invalid email or password',
                'code' => 401
            ];
        }

        if ($user->locked_until && now()->lt($user->locked_until)) {
            return [
                'user' => null,
                'message' => 'Your account is locked until ' . $user->locked_until,
                'code' => 423
            ];
        }

        if (!Hash::check($data['password'], $user->password)) {
            $this->users->increaseFailedAttempts($user);

            if ($user->failed_attempts >= 5) {
                $this->users->lock($user, 15);
                return [
                    'user' => null,
                    'message' => 'Your account has been temporarily locked due to too many failed login attempts.',
                    'code' => 423,
                ];
            }

            return [
                'user' => null,
                'message' => 'Invalid email or password',
                'code' => 401
            ];
        }

        $this->users->resetFailedAttempts($user);

        if (!$user->hasRole($requiredRole)) {
            return ['user' => null, 'message' => 'Forbidden for this role', 'code' => 403];
        }

        if (!$user->hasVerifiedEmail()) {
            return ['user' => false, 'message' => 'Please verify your email first', 'code' => 401];
        }

        $tokens = $this->tokens->createTokens($user);

        $this->audit->log(
            user_id: $user->id ?? null,
            module: 'users',
            action: 'login_success',
            description: 'تم تسجيل الدخول بنجاح',
            old: null,
            new: ['user_id' => $user->id]
        );

        return [
            'user'   => $this->formatUser($user),
            'tokens' => $tokens,
            'message' => 'Logged in successfully',
            'code' => 200
        ];
    }

    public function logout(string $token)
    {
        $user = auth('api')->user();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'code' => 401
            ];
        }

        $this->tokens->revokeUserTokens($user);

        $this->audit->log(
            user_id: $user->id ?? null,
            module: 'users',
            action: 'logout',
            description: 'تم تسجيل الخروج',
            old: null,
            new: ['user_id' => $user->id]
        );

        return [
            'success' => true,
            'message' => 'Logged out successfully',
            'code' => 200
        ];
    }

    public function emailVerification(array $data): array
    {
        $otp2 = $this->otp->validate($data['email'], $data['otp']);

        if (!$otp2->status) {
            return ['user' => null, 'message' => $otp2->message, 'code' => 401];
        }

        $user = $this->users->findByEmail($data['email']);
        if (!$user) {
            return ['user' => null, 'message' => 'user not found', 'code' => 404];
        }

        $user->update(['email_verified_at' => now()]);

        $this->audit->log(
            user_id: $user->id ?? null,
            module: 'users',
            action: 'email_verification',
            description: 'تم التحقق من البريد الإلكتروني',
            old: null,
            new: ['user_id' => $user->id, 'email_verified_at' => $user->email_verified_at]
        );

        return ['user' => true, 'message' => 'email verification successfully', 'code' => 200];
    }

    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'roles' => $user->roles->pluck('name')->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
        ];
    }
}
