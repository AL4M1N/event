<?php

namespace App\Controllers\Admin;

use App\Models\Attendee;
use App\Models\Event;
use App\Helpers\Helper;

class AttendeesController
{
    private $attendeeModel;
    private $eventModel;

    // Pagination and Validation
    private const PER_PAGE = 5;
    private const MINIMUM_NAME_LENGTH = 3;
    private const PHONE_PATTERN = '/^01[0-9]{9}$/';
    private const NID_PATTERN = '/^[0-9]{10,17}$/';

    public function __construct(Attendee $attendeeModel, ?Event $eventModel = null)
    {
        $this->attendeeModel = $attendeeModel;
        $this->eventModel = $eventModel;
    }

    // Attendees View
    public function index()
    {
        try {
            // Get Current Page
            $currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $offset = ($currentPage - 1) * self::PER_PAGE;
            
            // Build Search Filters
            $filters = $this->buildSearchFilters();
            $filterQuery = $filters['query'];
            $params = $filters['params'];

            // Get Total Records
            $totalRecords = $this->attendeeModel->getTotalRecords($filterQuery, $params);
            $totalPages = ceil($totalRecords / self::PER_PAGE);

            // Get Attendees
            $attendees = $this->attendeeModel->getAttendees($filterQuery, $params, $offset, self::PER_PAGE);
            $perPage = self::PER_PAGE;

            // Return View
            return Helper::view('backend/attendees/index.php', 
                compact('attendees', 'totalPages', 'currentPage', 'perPage')
            );
        } catch (\Exception $e) {
            // Handle Error
            error_log("Error: " . $e->getMessage());
            return $this->handleError();
        }
    }

    // Create Attendee View
    public function showCreateAttendeeForm()
    {
        return Helper::view('backend/attendees/create.php');
    }

    // Create Attendee
    public function store()
    {
        // Check if the request method is POST
        if (!Helper::isPostRequest()) {
            return Helper::view('backend/attendees/create.php', [
                'error' => 'Invalid method!'
            ]);
        }

        // Sanitize and validate the form data
        $formData = $this->sanitizeAttendeeData($_POST);
        $errors = $this->validateAttendeeData($formData);

        // If there are errors, return the form with errors
        if (!empty($errors)) {
            return Helper::view('backend/attendees/create.php', [
                'errors' => $errors, 
                'formData' => $formData, 
                'error' => 'Please correct the errors below.'
            ]);
        }

        // Check if the NID already exists
        if ($this->attendeeModel->getAttendeeByNid($formData['nid'])) {
            return Helper::view('backend/attendees/create.php', [
                'error' => 'An attendee with this NID already exists.',
                'formData' => $formData
            ]);
        }

        // Create the attendee
        try {
            $result = $this->attendeeModel->createAttendee($formData);
            if ($result) {
                return Helper::view('backend/attendees/create.php', [
                    'success' => 'Attendee created successfully! <a href="' . Helper::baseUrl('attendees') . '">View Attendees</a>'
                ]);
            }
        } catch (\Exception $e) {
            // Handle Error
            return Helper::view('backend/attendees/create.php', [
                'error' => 'An error occurred while saving the attendee.',
                'formData' => $formData
            ]);
        }
    }

    // Edit Attendee View
    public function showEditAttendeeForm($id)
    {
        $attendee = $this->attendeeModel->getAttendeeById($id);
        return Helper::view('backend/attendees/edit.php', compact('attendee'));
    }

    // Update Attendee
    public function update($id)
    {
        // Check if the request method is POST
        if (!Helper::isPostRequest()) {
            return Helper::view('backend/attendees/edit.php', [
                'error' => 'Invalid method!'
            ]);
        }

        // Sanitize and validate the form data
        $formData = $this->sanitizeAttendeeData($_POST);
        $errors = $this->validateAttendeeData($formData);
        $attendee = $this->attendeeModel->getAttendeeById($id);
        $error = null;
        $success = null;

        // If there are errors, return the form with errors
        if (!empty($errors)) {
            $error = 'Please correct the errors below.';
            return Helper::view('backend/attendees/edit.php', 
                compact('error', 'errors', 'attendee', 'formData')
            );
        }

        $result = $this->attendeeModel->update($id, $formData);

        // If the update is successful, return the form with success message
        if ($result) {
            $success = 'Attendee updated successfully! <a href="' . Helper::baseUrl('attendees') . '">View Attendees</a>';
        } else {
            $error = 'An error occurred while updating the attendee.';
        }

        // Return the form with success or error message
        return Helper::view('backend/attendees/edit.php', 
            compact('success', 'error', 'attendee', 'formData')
        );
    }

    // Delete Attendee and redirect to same page Attendees view using pagination
    public function delete($id)
    {
        // Delete the attendee
        $this->attendeeModel->delete($id);

        // Get Current Page
        $currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;

        // Get Total Records
        $totalRecords = $this->attendeeModel->getTotalRecords('', []);

        // Get Total Pages
        $totalPages = ceil($totalRecords / self::PER_PAGE);

        // If the current page is greater than the total pages, redirect to the last page
        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
        }

        // Redirect to the same page Attendees view using pagination
        header('Location: /attendees?page=' . $currentPage);
        exit();
    }

    // Register Attendee
    public function registerAttendee($eventId)
    {
        // Check if the request method is POST
        if (!Helper::isPostRequest()) {
            return Helper::jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
        }

        // Validate the event registration
        try {
            $formData = $this->validateEventRegistration($eventId, $_POST);
            $result = $this->attendeeModel->registerAttendee($formData);
            
            // If the registration is successful, return the attendee data
            if ($result['success']) {
                $newAttendee = $this->attendeeModel->getAttendeeById($result['attendee_id']);
                return Helper::jsonResponse([
                    'success' => true,
                    'message' => 'Registration successful!',
                    'attendee' => $this->formatAttendeeResponse($newAttendee)
                ]);
            }
        } catch (\Exception $e) {
            // Handle Error
            return Helper::jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    // Validate Event Registration
    private function validateEventRegistration($eventId, array $data): array
    {
        // Sanitize the data
        $formData = $this->sanitizeAttendeeData($data);
        $formData['event_id'] = $eventId;

        // Validate the attendee data
        $errors = $this->validateAttendeeData($formData);
        if (!empty($errors)) {
            throw new \Exception('Validation failed');
        }

        // Check if the attendee is already registered for the event
        if ($this->attendeeModel->checkDuplicateRegistration($eventId, $formData['nid'])) {
            throw new \Exception('Already registered for this event');
        }

        // Get the event details
        $event = $this->eventModel->getEventById($eventId);
        $attendees = $this->attendeeModel->getEventAttendees($eventId);
        if (count($attendees) >= $event['capacity']) {
            throw new \Exception('Attendee registered successfully but the event has reached maximum capacity.');
        }

        return $formData;
    }

    // Add Existing Attendee from event add attendees list
    public function addExistingAttendee($eventId)
    {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::jsonResponse(['success' => false, 'message' => 'Invalid request method']);
            return;
        }

        // Get the attendee ID from the request
        $json = json_decode(file_get_contents('php://input'), true);
        $attendeeId = $json['attendee_id'] ?? null;

        // If the attendee ID is not provided, return an error
        if (!$attendeeId) {
            Helper::jsonResponse(['success' => false, 'message' => 'Attendee ID is required']);
            return;
        }

        // Get the event details
        $event = $this->eventModel->getEventById($eventId);
        if (!$event) {
            Helper::jsonResponse(['success' => false, 'message' => 'Event not found']);
            return;
        }

        // Get the current attendees count for the event
        $currentAttendees = $this->attendeeModel->getEventAttendees($eventId);
        if (count($currentAttendees) >= $event['capacity']) {
            Helper::jsonResponse([
                'success' => false,
                'message' => 'Sorry, this event has reached its maximum capacity.'
            ]);
            return;
        }

        // Get the attendee details
        $attendee = $this->attendeeModel->getAttendeeById($attendeeId);
        if (!$attendee) {
            Helper::jsonResponse(['success' => false, 'message' => 'Attendee not found']);
            return;
        }

        // Check if the attendee is already registered for the event
        if ($this->attendeeModel->checkDuplicateRegistration($eventId, $attendee['nid'])) {
            Helper::jsonResponse([
                'success' => false,
                'message' => 'This attendee is already registered for this event'
            ]);
            return;
        }

        // Add the attendee to the event
        $result = $this->attendeeModel->addExistingAttendee($eventId, $attendeeId);

        // If the addition is successful, return the attendee data
        if ($result['success']) {
            $attendee = $this->attendeeModel->getAttendeeById($attendeeId);
            Helper::jsonResponse([
                'success' => true,
                'message' => 'Attendee added successfully',
                'attendee' => [
                    'name' => $attendee['name'],
                    'phone' => $attendee['phone'],
                    'nid' => $attendee['nid']
                ]
            ]);
        } else {
            // If the addition fails, return an error
            Helper::jsonResponse([
                'success' => false,
                'message' => $result['error'] ?? 'Failed to add attendee'
            ]);
        }
    }

    // Sanitize Attendee Data
    private function sanitizeAttendeeData(array $data): array
    {
        return array_map('trim', [
            'name' => $data['name'] ?? '',
            'phone' => $data['phone'] ?? '',
            'nid' => $data['nid'] ?? ''
        ]);
    }

    // Format Attendee Response
    private function formatAttendeeResponse(array $attendee): array
    {
        return [
            'name' => $attendee['name'],
            'phone' => $attendee['phone'],
            'nid' => $attendee['nid'],
        ];
    }

    // Validate Attendee Data
    private function validateAttendeeData($data)
    {
        $errors = [];

        // Check if the name is at least the minimum length
        if (strlen($data['name']) < self::MINIMUM_NAME_LENGTH) {
            $errors['name'] = 'Name must be at least ' . self::MINIMUM_NAME_LENGTH . ' characters long.';
        }

        // Check if the phone number is valid
        if (!preg_match(self::PHONE_PATTERN, $data['phone'])) {
            $errors['phone'] = 'Phone number must start with 01 and be exactly 11 digits.';
        }

        // Check if the NID is valid
        if (!preg_match(self::NID_PATTERN, $data['nid'])) {
            $errors['nid'] = 'Please enter a valid NID number (10-17 digits).';
        }

        return $errors;
    }


    // Handle Error
    public function handleError()
    {
        return Helper::view('backend/attendees/index.php', [
            'error' => 'An error occurred while fetching attendees.',
            'attendees' => [],
            'totalPages' => 1,
            'currentPage' => 1,
            'perPage' => self::PER_PAGE
        ]);
    }

    // Attendees List for Event Add Attendees List
    public function listAvailableAttendees($eventId)
    {
        try {
            // Get the available attendees for the event
            $attendees = $this->attendeeModel->getAvailableAttendees($eventId);
            Helper::jsonResponse([
                'success' => true,
                'data' => $attendees
            ]);
        } catch (\Exception $e) {
            Helper::jsonResponse([
                'success' => false,
                'message' => 'Failed to fetch available attendees'
            ]);
        }
    }

    // Build Search Filters
    private function buildSearchFilters(): array
    {
        // Build Search Filters
        $searchFields = ['name', 'phone', 'nid'];
        $filters = [];
        $params = [];

        // Loop through the search fields
        foreach ($searchFields as $field) {
            if ($value = trim(filter_input(INPUT_GET, $field))) {
                $filters[] = "$field LIKE :$field";
                $params[":$field"] = "%{$value}%";
            }
        }
        
        // Return the filters and params
        return [
            'query' => $filters ? 'WHERE ' . implode(' AND ', $filters) : '',
            'params' => $params
        ];
    }
}
