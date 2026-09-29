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

        /*
         * DOMAIN Bitrix может передать
         * в query string.
         */
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

        /*
         * Получаем текущего пользователя Bitrix24
         */
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

            Log::info(
                'Bitrix LMS: user.current получен',
                [
                    'status' => $response->status(),

                    'seconds' => round(
                        microtime(true) - $startedAt,
                        2
                    ),

                    'has_result' => !empty(
                        $response->json('result')
                    ),

                    'error' => $response->json('error'),
                ]
            );

        } catch (\Throwable $e) {

            Log::error(
                'Bitrix LMS: ошибка user.current',
                [
                    'message' => $e->getMessage(),
                ]
            );

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

        if (
            !$bitrixUser ||
            empty($bitrixUser['ID'])
        ) {
            return response(
                'Не удалось определить пользователя Bitrix24.',
                500
            );
        }

        /*
         * Ищем пользователя LMS
         * по ID пользователя Bitrix24
         */
        $user = User::where(
            'bitrix_user_id',
            $bitrixUser['ID']
        )->first();

        if (!$user) {

            $user = new User();

            $user->bitrix_user_id =
                (int) $bitrixUser['ID'];

            /*
             * Пароль фактически не используется,
             * но поле в таблице обязательное.
             */
            $user->password = Hash::make(
                Str::random(40)
            );
        }

        /*
         * Обновляем данные сотрудника
         */
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

        Log::info(
            'Bitrix LMS: пользователь сохранён',
            [
                'local_user_id' => $user->id,
                'bitrix_user_id' =>
                    $user->bitrix_user_id,
            ]
        );

        /*
         * Запоминаем пользователя LMS
         * в Laravel-сессии
         */
        session([
            'lms_user_id' => $user->id,
            'bitrix_domain' => $domain,
        ]);

        Log::info(
            'Bitrix LMS: выполняем переход в мои курсы'
        );

        return redirect()
            ->route('my.courses');
    }
}