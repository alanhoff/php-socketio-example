<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Chat</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css">

        <style>
            html,
            body {
                height: 100%;
            }

            .form-signin {
                max-width: 330px;
                padding: 1rem;
            }

            .form-signin .form-floating:focus-within {
                z-index: 2;
            }

            .form-signin input[id="name"] {
                margin-bottom: -1px;
                border-bottom-right-radius: 0;
                border-bottom-left-radius: 0;
            }

            .form-signin input[id="room"] {
                margin-bottom: 20px;
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        </style>
    </head>
    <body class="d-flex align-items-center py-4 bg-body-tertiary">
        <main class="form-signin w-100 m-auto">
            <form>
                <h1 class="h3 mb-3 fw-normal">Chat da Gambiarra</h1>

                <div class="form-floating">
                    <input type="text" class="form-control" id="name" placeholder="">
                    <label for="name">Nome</label>
                </div>
                <div class="form-floating">
                    <input type="text" class="form-control" id="room" placeholder="">
                    <label for="room">Sala</label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Entrar</button>
            </form>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            const name = document.querySelector('#name');
            const room = document.querySelector('#room');
            const form = document.querySelector('form');

            form.addEventListener('submit', event => {
                event.preventDefault();

                const query = new URLSearchParams();
                query.append('name', name.value);
                query.append('room', room.value);

                window.location = `/?${query.toString()}`;
            });
        </script>
    </body>
</html>
