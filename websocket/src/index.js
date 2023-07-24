import "dotenv/config";
import { Server } from "socket.io";
import { createServer } from "http";
import { createAdapter } from "@socket.io/redis-streams-adapter";
import { createClient } from "redis";
import { json, default as express } from "express";
import got from "got";

// Connect to redis
const redis = createClient({url: process.env.REDIS_URI});
await redis.connect();

// Setup Socket.io
const app = express();
const http = createServer(app);
const io = new Server(http, {
  adapter: createAdapter(redis),
  connectionStateRecovery: {
    // The backup duration of the sessions and the packets
    maxDisconnectionDuration: 2 * 60 * 1000,
    // Whether to skip middlewares upon successful recovery
    skipMiddlewares: true,
  }
});

// An internal REST endpoint so Laravel can communicate with us
app.post('/api/internal/emit', json(), (req, res) => {
  const {room, event, params} = req.body;

  io.to(room).emit(event, ...params);
  res.json({success: true});
});

// Wait for connections
io.on("connection", socket => {
  // Handles events of users joining a room
  socket.on('room:join', (name, room, callback) => {
    socket.join(room);

    // Broadcast to everyone in the room that a new person joined the chat
    io.to(room).emit('room:joined', name, room);
    callback({success: true});
  });

  // Handles events of users sending messages to everyone in a room
  socket.on('room:broadcast', (name, room, message, callback) => {
    // Broadcast the message to everyone in the room except the sender
    socket.to(room).emit('room:message', name, room, message);
    callback({success: true});
  });

  // Handles sending arbitrary payloads to Laravel's internal REST endpoint
  socket.on('php:invoke', (payload, callback) => {
    got.post(process.env.PHP_INTERNAL_URI, {json: payload})
      .json()
      .then((result) => callback(result))
      .catch((error) => callback({error}));
  });
});

http.listen(Number(process.env.PORT), '0.0.0.0', () => {
  console.log(`Socket.io started on http://0.0.0.0:${process.env.PORT}`);
});