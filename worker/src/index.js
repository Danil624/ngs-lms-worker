const ORIGIN_URL = 'https://kamaz-scrypt.taile47694.ts.net';

export default {
    async fetch(request) {

        const incoming = new URL(request.url);

        // Проверка самого Worker
        if (incoming.pathname === '/__worker_health') {

            return Response.json({
                ok: true,
                service: 'ngs-lms'
            });

        }


        const target = new URL(
            incoming.pathname + incoming.search,
            ORIGIN_URL
        );


        const headers = new Headers(
            request.headers
        );


        headers.delete('host');
        headers.delete('connection');

        headers.set(
            'accept-encoding',
            'identity'
        );

        headers.set(
            'x-forwarded-host',
            incoming.host
        );

        headers.set(
            'x-forwarded-proto',
            'https'
        );


        const clientIp =
            request.headers.get(
                'cf-connecting-ip'
            );

        if (clientIp) {

            headers.set(
                'x-forwarded-for',
                clientIp
            );

        }


        const options = {

            method: request.method,

            headers,

            redirect: 'manual',

        };


        if (
            request.method !== 'GET'
            &&
            request.method !== 'HEAD'
        ) {

            options.body = request.body;

        }


        try {

            const response = await fetch(
                target.toString(),
                options
            );


            const responseHeaders =
                new Headers(
                    response.headers
                );


            responseHeaders.delete(
                'content-length'
            );

            responseHeaders.delete(
                'transfer-encoding'
            );

            responseHeaders.delete(
                'connection'
            );


            // Исправляем редиректы Laravel,
            // чтобы пользователь оставался
            // на workers.dev

            const location =
                responseHeaders.get(
                    'location'
                );


            if (location) {

                try {

                    const redirect =
                        new URL(
                            location,
                            ORIGIN_URL
                        );


                    const origin =
                        new URL(
                            ORIGIN_URL
                        );


                    if (
                       redirect.hostname
                        ===
                        origin.hostname
                    ) {

                        redirect.protocol =
                            incoming.protocol;

                        redirect.host =
                            incoming.host;


                        responseHeaders.set(
                            'location',
                            redirect.toString()
                        );

                    }

                } catch (e) {}

            }


            return new Response(
                response.body,
                {
                    status:
                        response.status,

                    statusText:
                        response.statusText,

                    headers:
                        responseHeaders
                }
            );

        } catch (error) {

            return Response.json(
                {
                    ok: false,
                    error:
                        'Laravel server unavailable',
                    message:
                        String(error)
                },
                {
                    status: 502
                }
            );

        }

    }
};