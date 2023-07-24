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

            .card-body {
                max-width: 800px;
                height: 100%;
                max-height: 600px;
            }
        </style>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.socket.io/4.6.0/socket.io.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tsparticles-confetti@2.11.0/tsparticles.confetti.bundle.min.js"></script>
    </head>
    <body class="d-flex align-items-center bg-body-tertiary py-3">
        <div class="d-flex flex-column card-body m-auto">
            <ul class="list-group mb-3 flex-grow-1 bg-body py-2 overflow-y-auto" id="message-list">
                <li class="list-group-item border-0 py-0 mt-1" id="connecting">
                    <span>Connecting...
                        <small class="d-block text-body-secondary">
                            Please wait while we finish setting up your connection.
                        </small>
                    </span>
                </li>
            </ul>
            <form id="chat-form" class="d-flex flex-row">
                <input type="text" class="form-control" placeholder="Type your message here..." id="message-input" disabled>
                <button type="submit" class="btn btn-primary ms-3" disabled>Enviar</button>
                <button type="button" class="btn btn-success ms-3" disabled>🎉</button>
            </form>
        </div>
        <template id="message-template">
            <li class="list-group-item border-0 py-0 mt-1">
                <span>
                    %title%
                    <small class="d-block text-body-secondary">
                        %message%
                    </small>
                </span>
            </li>
        </template>
        <script type="text/javascript">
            // Common elements we'll be interacting with
            const template = document.querySelector('template#message-template');
            const messages = document.querySelector('#message-list');
            const input = document.querySelector('#message-input');
            const button = document.querySelector('button[type=submit]');
            const form = document.querySelector('form#chat-form');
            const party = document.querySelector('button[type=button]');

            // Renders a new message using a template as base
            function render(title, message) {
                const node = template.cloneNode(true);

                node.innerHTML = node.innerHTML
                    .replace('%title%', title)
                    .replace('%message%', message);

                messages.appendChild(node.content);
                messages.scroll({ top: messages.scrollHeight, behavior: "smooth"});
            }

            // Extracts auth info from query parameters and initiates
            // our socket.io client. You should probably implement
            // proper authentication here
            const query = new URLSearchParams(window.location.search);
            const auth = {name: query.get('name'), room: query.get('room')};
            const socket = io({path: '/ws'});

            // Renders inbound messages
            socket.on('room:message', (name, room, message) => {
                render(name, message);
            });

            // Renders a welcome message when someone joins the room
            socket.on('room:joined', (name, room) => {
                const title = `${name} connected`;
                const message = `User ${name} joined chat ${auth.room}. Say hello!`;

                render(title, message);
            });

            // Initiates party protocol
            socket.on('room:party', (name, room) => {
                const title = `It party time! 🎉`;
                const message = `User ${name} started a party on ${auth.room}.`;
                const rand = (min, max) => Math.random() * (max - min) + min;

                render(title, message);

                const interval = setInterval(() => {
                    confetti({
                        angle: rand(55, 125),
                        spread: rand(50, 70),
                        particleCount: rand(50, 100),
                        origin: { y: 0.6 },
                    });
                }, 1800);

                setTimeout(() => clearInterval(interval), 13000);
            });

            // Sends a message to Socket.io asking
            socket.emitWithAck('room:join', auth.name, auth.room).then(() => {
                // Cleanup the waiting msg
                const message = document.querySelector('li#connecting');
                message.parentNode.removeChild(message);

                // Enable inputs
                input.removeAttribute('disabled');
                button.removeAttribute('disabled');
                party.removeAttribute('disabled');
            });

            // Whenever our form gets submitted we'll emit an event to Socket.IO
            // asking for our message to be broadcasted to the entire room
            form.addEventListener('submit', async event => {
                event.preventDefault();

                // Renders our own message instantly
                const message = input.value;
                input.value = '';
                render(auth.name, message);

                // Sends an event to Socket.io instructing the server
                // to broadcast a message to all users connected to the
                // same room
                await socket.emitWithAck('room:broadcast', auth.name, auth.room, message);
            });

            // Sends a message to PHP through Socket.IO, then wait for the party
            party.addEventListener('click', async event => {
                event.preventDefault();

                const payload = {
                    room: auth.room,
                    event: 'room:party',
                    params: [auth.name, auth.room]
                };

                await socket.emitWithAck('php:invoke', payload);
            });
        </script>
    </body>
</html>
