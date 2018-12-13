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
- user:
    - user.id, INT, PK
    - user.name, VARCHAR(255)
    - user.email, VARCHAR(320)
    - user.hash, VARCHAR(60), result of password_hash using BCRYPT
- room:
    - room.id, INT, PK
    - room.name, VARCHAR(255)
- booking:
    - booking.id, INT, PK
    - user.id#, FK
    - room.id#, FK
    - booking.start, DATETIME
    - booking.end, DATETIME

### TODO
- [ ] Backend:
    - [ ] Create a Laravel project.
- [ ] Backend/Model:
    - [ ] User:
        - [ ] Create migration (create table + insert test user);
        - [ ] Implement user creation;
        - [ ] Implement user authentification.
    - [ ] Room:
        - [ ] Create migration (create table + insert rooms);
    - [ ] Booking:
        - [ ] Create migration (create table);
        - [ ] Implement booking creation/modification:
            - [ ] Block meeting collisions for the same room;
            - [ ] Block meeting collisions for the same user (overridable).
- [ ] Backend/API:
    - [ ] User:
        - [ ] POST /register: implement user creation;
        - [ ] POST /login: implement user authentification;
        - [ ] Implement user authentification middleware.
    - [ ] Booking: (restricted to authenticated users)
        - [ ] GET /bookings: implement booking listing for a user;
        - [ ] POST /bookings: implement booking creation;
        - [ ] GET /bookings/{id}: implement booking viewing;
        - [ ] PUT /bookings/{id}: implement booking modification (user.id must match booking.user.id);
        - [ ] DELETE /bookings/{id}: implement booking deletion (user.id must match booking.user.id);
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
