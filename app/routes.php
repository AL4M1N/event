<?php

use App\Controllers\Auth\{RegisterController, LoginController};
use App\Controllers\Admin\{DashboardController, EventsController, AttendeesController};
use App\Controllers\IndexController;
use App\Models\{User, Event, Attendee};
use App\Middleware\AuthMiddleware;

$auth = new AuthMiddleware();

$routes = [
    // Auth & Dashboard Routes
    '/' => function () use ($pdo) {
        (new IndexController($pdo))->index();
    },
    '/register' => function () use ($pdo) {
        (new RegisterController(new User($pdo)))->index();
    },
    '/login' => function () use ($pdo) {
        (new LoginController(new User($pdo)))->index();
    },
    '/logout' => function () use ($pdo) {
        (new LoginController(new User($pdo)))->logout();
    },
    '/dashboard' => function () use ($pdo, $auth) {
        $auth->handle();
        (new DashboardController($pdo))->index();
    },

    // Events Routes
    '/events' => function () use ($pdo, $auth) {
        $auth->handle();
        $eventModel = new Event($pdo);
        (new EventsController($eventModel, $pdo))->index();
    },
    '/events/new' => function () use($auth) {
        $auth->handle();
        (new EventsController())->showEventForm();
    },
    '/events/store' => function () use ($pdo, $auth) {
        $auth->handle();
        $eventModel = new Event($pdo);
        (new EventsController($eventModel))->store();
    },
    '/events/edit/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $eventModel = new Event($pdo);
        (new EventsController($eventModel))->showEditEventForm($id);
    },
    '/events/update/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $eventModel = new Event($pdo);
        (new EventsController($eventModel))->update($id);
    },
    '/events/delete/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $eventModel = new Event($pdo);
        (new EventsController($eventModel))->delete($id);
    },
    '/events/export/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $eventModel = new Event($pdo);
        (new EventsController($eventModel))->exportEventCsv($id);
    },
    '/api/events/{id}' => function ($id) use ($pdo) {
        $eventModel = new Event($pdo);
        (new EventsController($eventModel))->getEventDetailsApi($id);
    },

    // Attendee Routes
    '/attendees' => function () use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        (new AttendeesController($attendeeModel))->index();
    },
    '/attendees/create' => function () use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        (new AttendeesController($attendeeModel))->showCreateAttendeeForm();
    },
    '/attendees/store' => function () use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        (new AttendeesController($attendeeModel))->store();
    },
    '/attendees/edit/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        (new AttendeesController($attendeeModel))->showEditAttendeeForm($id);
    },
    '/attendees/update/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        (new AttendeesController($attendeeModel))->update($id);
    },
    '/attendees/delete/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        (new AttendeesController($attendeeModel))->delete($id);
    },
    '/attendees/register/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        $eventModel = new Event($pdo);
        (new AttendeesController($attendeeModel, $eventModel))->registerAttendee($id);
    },
    '/attendees/list-available/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        (new AttendeesController($attendeeModel))->listAvailableAttendees($id);
    },
    '/attendees/add-existing/{id}' => function ($id) use ($pdo, $auth) {
        $auth->handle();
        $attendeeModel = new Attendee($pdo);
        $eventModel = new Event($pdo);
        (new AttendeesController($attendeeModel, $eventModel))->addExistingAttendee($id);
    },
];
