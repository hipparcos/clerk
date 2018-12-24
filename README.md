# clerk

clerk is a room booking management system.
It's an exercise to demonstrate my abilities to develop a modern web app.
The goal is to implement all the requiremnts with a balance between functionality,
code quality and smplicity.
The code must be testable.

## Background
The ACME company is currently facing a problem. The company increased its size and now
they have 150 employees on the same building. Those employees are distributed in many
different sectors within the building and all of them share the same 4 meeting rooms: Tatooine,
Goldenrod, Gotham and Duckburg. You are a developer that recently joined the company and
found some spare-time to develop a small system that might help ACME and its workforce.
So far, that's the list of requirements you came up with during your coffee breaks. They are
listed in order of priority.

## Requirements
- Basic user Registration + Login;
- From any page in the system, user can create a new booking, the form should contain:
    - Desired Meeting Room;
    - Start Date/Time;
    - Length of Booking (in minutes).
- The record should be saved with start date/time + end date/time, calculated from the length of the booking;
- View Meeting Room bookings:
    - Current Date by default;
    - Change Date (input field DD/MM/YYYY);
    - Bookings are displayed on a Table containing:
        - Meeting Room Name;
        - Start date/time;
        - End date/time;
        - Name + email of person who created the entry.
- Block meeting collisions on the same room;
- User can edit / remove its own bookings;
- Warn meeting collisions for the same person.

## Tech stack
- Backend:
    - PHP/Laravel;
    - MariaDB.
- Frontend:
    - Vue.js;
    - Bulma.
- Deployment:
    - Docker.

## Implementation
### Model
- users:
    - users.id, INT, PK
    - users.name, VARCHAR(255)
    - users.email, VARCHAR(320)
    - users.passwort, VARCHAR(60), result of password_hash using BCRYPT
- rooms:
    - rooms.id, INT, PK
    - rooms.name, VARCHAR(255), UNIQUE
- bookings:
    - bookings.id, INT, PK
    - users.id#, FK, ON DELETE CASCADE
    - rooms.id#, FK, ON DELETE CASCADE
    - bookings.start, DATETIME
    - bookings.end, DATETIME

### TODO
- [x] Backend:
    - [x] Create a Laravel project.
- [x] Backend/Model:
    - [x] User: (use default Laravel authentification scaffolding)
        - [x] Create migration (create table + insert test user);
        - [x] Implement user creation;
        - [x] Implement user authentification.
    - [x] Room:
        - [x] Create migration (create table + insert rooms);
    - [x] Booking:
        - [x] Create migration (create table);
        - [x] Implement booking creation/modification:
            - [x] Block meeting collisions for the same room;
            - [x] Block meeting collisions for the same user (overridable).
- [x] Backend/API:
    - [x] User:
        - [x] POST /register: implement user creation;
        - [x] POST /login: implement user authentification (Passport);
        - [x] Implement user authentification middleware (Passport).
    - [x] Booking: (restricted to authenticated users)
        - [x] GET /bookings: implement booking listing for a given day (or today);
        - [x] POST /bookings: implement booking creation;
        - [x] GET /bookings/{id}: implement booking viewing;
        - [x] PUT /bookings/{id}: implement booking modification (user.id must match booking.user.id);
        - [x] DELETE /bookings/{id}: implement booking deletion (user.id must match booking.user.id);
- [ ] Frontend: (as it is a SPA, every form/table will be implemented in its own component)
    - [ ] Configure node.js, add required dependancies (vue.js, vue-router, bulma);
    - [ ] Create a base layout;
    - [ ] Configure vue-router;
    - [ ] /:
        - [ ] Create a timetable view of all bookings (obfuscated except for the one from the current user).
    - [ ] /register:
        - [ ] Create registration form;
        - [ ] Display registration errors.
    - [ ] /login:
        - [ ] Create login form;
        - [ ] Display login errors.
    - [ ] /bookings:
        - [ ] Create a table of bookings;
        - [ ] Add a control to delete a booking;
        - [ ] Add a control to modify a booking (in-place editing ?).
    - [ ] /bookings/new:
        - [ ] Create booking form.
- [ ] Deployment:
    - [ ] Create a Dockerfile to deploy the application.
