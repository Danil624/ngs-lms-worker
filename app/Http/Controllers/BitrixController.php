<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BitrixController extends Controller
{
    public function entry(Request $request)
    {
        Log::info('Bitrix LMS: вход в приложение', [
            'method' => $request->method(),
            'domain' => $request->query('DOMAIN')
                ?? $request->input('DOMAIN'),
            'has_auth_id' => $request->filled('AUTH_ID'),
            'has_application_token' =>
                $request->filled('APPLICATION_TOKEN'),
        ]);

        $domain = $request->query('DOMAIN')
            ?? $request->input('DOMAIN');

        $authId = $request->input('AUTH_ID');

        if (!$domain || !$authId) {
            return response(
                'Приложение нужно открывать из Bitrix24.',
                400
            );
        }

        $domain = preg_replace(
            '#^https?://#',
            '',
            $domain
        );

        Log::info('Bitrix LMS: перед user.current');

        $startedAt = microtime(true);

        try {

            $response = Http::connectTimeout(5)
                ->timeout(10)
                ->asForm()
                ->post(
                    "https://{$domain}/rest/user.current.json",
                    [
                        'auth' => $authId,
                    ]
                );

            Log::info('Bitrix LMS: user.current получен', [
                'status' => $response->status(),
                'seconds' => round(
                    microtime(true) - $startedAt,
                    2
                ),
                'has_result' => !empty(
                    $response->json('result')
                ),
                'error' => $response->json('error'),
            ]);

        } catch (\Throwable $e) {

            Log::error('Bitrix LMS: ошибка user.current', [
                'message' => $e->getMessage(),
            ]);

            return response(
                'Ошибка соединения с Bitrix24 REST API',
                502
            );
        }

        if (!$response->successful()) {
            return response(
                'Bitrix24 REST API вернул ошибку.',
                500
            );
        }

        $bitrixUser = $response->json('result');

        if (!$bitrixUser || empty($bitrixUser['ID'])) {
            return response(
                'Не удалось определить пользователя Bitrix24.',
                500
            );
        }

        $user = User::where(
            'bitrix_user_id',
            $bitrixUser['ID']
        )->first();

        if (!$user) {

            $email = $bitrixUser['EMAIL'] ?? null;

            if ($email) {
                $user = User::where(
                    'email',
                    $email
                )->first();
            }
        }

        if (!$user) {
            $user = new User();

            $user->password = Hash::make(
                Str::random(40)
            );

            $user->role = 'employee';
        }

        $user->bitrix_user_id =
            (int) $bitrixUser['ID'];

        $user->name =
            $bitrixUser['NAME']
            ?? 'Пользователь';

        $user->last_name =
            $bitrixUser['LAST_NAME']
            ?? null;

        $user->email =
            $bitrixUser['EMAIL']
            ?? 'bitrix_' .
                $bitrixUser['ID'] .
                '@local.invalid';

        $user->position =
            $bitrixUser['WORK_POSITION']
            ?? null;

        $departments =
            $bitrixUser['UF_DEPARTMENT']
            ?? [];

        $user->department_id =
            $departments[0]
            ?? null;

        if (!$user->role) {
            $user->role = 'employee';
        }

        $user->save();

        Log::info('Bitrix LMS: пользователь сохранён', [
            'local_user_id' => $user->id,
            'bitrix_user_id' => $user->bitrix_user_id,
        ]);

        session([
            'lms_user_id' => $user->id,
            'bitrix_domain' => $domain,
            'bitrix_auth_id' => encrypt($authId),
        ]);

        Log::info(
            'Bitrix LMS: выполняем переход в мои курсы'
        );

        return redirect()
            ->route('my.courses');
    }


    public function syncUsers(Request $request)
    {
        Log::info(
            'Bitrix LMS: старт синхронизации сотрудников'
        );

        $domain = session('bitrix_domain');
        $encryptedAuthId =
            session('bitrix_auth_id');

        if (!$domain || !$encryptedAuthId) {

            Log::warning(
                'Bitrix LMS: отсутствует Bitrix-сессия'
            );

            return back()->with(
                'error',
                'Нет активной сессии Bitrix24. Откройте приложение заново.'
            );
        }

        try {

            $authId =
                decrypt($encryptedAuthId);

        } catch (\Throwable $e) {

            Log::error(
                'Bitrix LMS: ошибка расшифровки токена',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return back()->with(
                'error',
                'Не удалось прочитать токен Bitrix24. Откройте приложение заново.'
            );
        }

        $created = 0;
        $updated = 0;
        $start = 0;

        /*
         * Генерируем пароль один раз.
         * В LMS пароль для Bitrix-пользователей
         * фактически не используется.
         */
        $defaultPassword =
            Hash::make(Str::random(40));

        try {

            do {

                Log::info(
                    'Bitrix LMS: запрос user.get',
                    [
                        'start' => $start,
                    ]
                );

                $startedAt = microtime(true);

                $response = Http::connectTimeout(5)
                    ->timeout(20)
                    ->asForm()
                    ->post(
                        "https://{$domain}/rest/user.get.json",
                        [
                            'auth' => $authId,

                            'FILTER' => [
                                'ACTIVE' => true,
                                'USER_TYPE' =>
                                    'employee',
                            ],

                            'start' => $start,
                        ]
                    );

                $bitrixUsers =
                    $response->json('result')
                    ?? [];

                Log::info(
                    'Bitrix LMS: ответ user.get',
                    [
                        'start' => $start,
                        'status' =>
                            $response->status(),
                        'count' =>
                            count($bitrixUsers),
                        'seconds' => round(
                            microtime(true)
                                - $startedAt,
                            2
                        ),
                        'error' =>
                            $response->json(
                                'error'
                            ),
                    ]
                );

                if (
                    !$response->successful()
                    || $response->json('error')
                ) {

                    Log::error(
                        'Bitrix LMS: ошибка user.get',
                        [
                            'status' =>
                                $response->status(),
                            'error' =>
                                $response->json(
                                    'error'
                                ),
                            'description' =>
                                $response->json(
                                    'error_description'
                                ),
                        ]
                    );

                    return back()->with(
                        'error',
                        'Bitrix24 вернул ошибку при получении сотрудников.'
                    );
                }

                foreach (
                    $bitrixUsers
                    as $bitrixUser
                ) {

                    if (
                        empty(
                            $bitrixUser['ID']
                        )
                    ) {
                        continue;
                    }

                    $bitrixId =
                        (int) $bitrixUser['ID'];

                    $email =
                        $bitrixUser['EMAIL']
                        ?? null;

                    $user = User::where(
                        'bitrix_user_id',
                        $bitrixId
                    )->first();

                    /*
                     * Если пользователь уже был
                     * создан вручную — связываем
                     * его с Bitrix по email.
                     */
                    if (
                        !$user
                        && $email
                    ) {

                        $user = User::where(
                            'email',
                            $email
                        )->first();
                    }

                    $isNew = !$user;

                    if ($isNew) {

                        $user = new User();

                        $user->password =
                            $defaultPassword;

                        $user->role =
                            'employee';
                    }

                    $user->bitrix_user_id =
                        $bitrixId;

                    $user->name =
                        $bitrixUser['NAME']
                        ?? 'Пользователь';

                    $user->last_name =
                        $bitrixUser[
                            'LAST_NAME'
                        ]
                        ?? null;

                    $user->email =
                        $email
                        ?: "bitrix_{$bitrixId}@local.invalid";

                    $user->position =
                        $bitrixUser[
                            'WORK_POSITION'
                        ]
                        ?? null;

                    $departments =
                        $bitrixUser[
                            'UF_DEPARTMENT'
                        ]
                        ?? [];

                    $user->department_id =
                        $departments[0]
                        ?? null;

                    if (!$user->role) {
                        $user->role =
                            'employee';
                    }

                    $user->save();

                    if ($isNew) {
                        $created++;
                    } else {
                        $updated++;
                    }
                }

                /*
                 * Bitrix24 отдаёт максимум
                 * 50 пользователей за запрос.
                 */
                $start += 50;

            } while (
                count($bitrixUsers) === 50
            );

        } catch (\Throwable $e) {

            Log::error(
                'Bitrix LMS: исключение синхронизации',
                [
                    'message' =>
                        $e->getMessage(),
                    'start' => $start,
                ]
            );

            return back()->with(
                'error',
                'Ошибка синхронизации сотрудников: '
                . $e->getMessage()
            );
        }

        Log::info(
            'Bitrix LMS: синхронизация завершена',
            [
                'created' => $created,
                'updated' => $updated,
            ]
        );

        return back()->with(
            'success',
            "Сотрудники синхронизированы. Добавлено: {$created}, обновлено: {$updated}."
        );
    }
}