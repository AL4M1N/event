<?php

namespace App\Controllers\Admin;

use App\Models\Event;
use App\Helpers\Helper;


class EventsController
{
    private $eventModel;

    //Pagination and Validation
    private const PER_PAGE = 2;
    private const MIN_NAME_LENGTH = 3;
    private const MAX_NAME_LENGTH = 50;
    private const MIN_DESCRIPTION_LENGTH = 10;

    public function __construct(?Event $eventModel = null)
    {
        $this->eventModel = $eventModel;
    }

    // Events View
    public function index()
    {
        try {
            // Get Current Page
            $currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
            $offset = ($currentPage - 1) * self::PER_PAGE;
            
            // Build Search Filters 
            $filters = $this->buildSearchFilters();
            list($sortColumn, $sortOrder) = $this->getSortingParams();

            // Get Total Records
            $totalRecords = $this->eventModel->getTotalFilteredRecords($filters);
            $totalPages = ceil($totalRecords / self::PER_PAGE);
            $perPage = self::PER_PAGE;
            
            // Get Filtered Events
            $events = $this->eventModel->getFilteredEvents(
                $filters,
                $offset,
                self::PER_PAGE,
                $sortColumn,
                $sortOrder
            );

            // Return View
            return Helper::view('backend/events/index.php', 
                compact('events', 'currentPage', 'totalPages', 'perPage', 'totalRecords'));
        } catch (\Exception $e) {
            // Handle Error
            error_log("Error: " . $e->getMessage());
            return $this->handleError();
        }
    }

    // Create Event View
    public function showEventForm()
    {
        return Helper::view('backend/events/create.php');
    }

    // Create Event
    public function store()
    {
        // Check if the request method is POST
        if (!Helper::isPostRequest()) {
            return Helper::view('backend/events/create.php');
        }

        // Sanitize the form data
        $formData = $this->sanitizeEventData($_POST);

        // Validate the form data
        $errors = $this->validateEventData($formData);

        //if there are errors, return the form with errors
        if (!empty($errors)) {
            return Helper::view('backend/events/create.php', [
                'error' => 'Please correct the errors below.',
                'errors' => $errors,
                'formData' => $formData
            ]);
        }

        // Create the event
        try {
            $result = $this->eventModel->createEvent($formData);
            if ($result) {
                return Helper::view('backend/events/create.php', [
                    'success' => 'Event created successfully! <a href="' . Helper::baseUrl('events') . '">View Events</a>'
                ]);
            }
        } catch (\Exception $e) {
            // Handle Error
            return Helper::view('backend/events/create.php', [
                'error' => 'An error occurred while saving the event.',
                'formData' => $formData
            ]);
        }
    }

    // Edit Event View
    public function showEditEventForm($id)
    {
        $event = $this->eventModel->getEventById($id);
        return Helper::view('backend/events/edit.php', compact('event'));
    }

    // Update Event
    public function update($id)
    {
        // Check if the request method is POST
        if (!Helper::isPostRequest()) {
            return Helper::view('backend/events/edit.php', [
                'error' => 'Invalid method!'
            ]);
        }

        // Sanitize the form data
        $formData = $this->sanitizeEventData($_POST);
        $errors = $this->validateEventData($formData);
        $event = $this->eventModel->getEventById($id);
        $error = null;
        $success = null;

        //if there are errors, return the form with errors
        if (!empty($errors)) {
            $error = 'Please correct the errors below.';
            return Helper::view('backend/events/edit.php', 
                compact('error', 'errors', 'event', 'formData')
            );
        }

        // Update the event
        $result = $this->eventModel->update($id, $formData);

        //if the event is updated successfully, return the form with success message
        if ($result) {
            $success = 'Event updated successfully! <a href="' . Helper::baseUrl('events') . '">View Events</a>';
        } else {
            $error = 'An error occurred while updating the event.';
        }

        // Return the form with success or error message
        return Helper::view('backend/events/edit.php', 
            compact('success', 'error', 'event', 'formData')
        );
    }

    // Delete Event
    public function delete($id)
    {
        $this->eventModel->delete($id);
        
        // Get Current Page
        $currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;

        // Get Total Records
        $totalRecords = $this->eventModel->getAllEvents();
        $totalRecords = count($totalRecords);
        $totalPages = ceil($totalRecords / self::PER_PAGE);

        // If the current page is greater than the total pages, set the current page to the total pages
        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
        }

        header('Location: /events?page=' . $currentPage);
        exit();
    }

    // Export Event CSV
    public function exportEventCsv($id)
    {
        // Get the event details
        $event = $this->eventModel->getEventById($id);
        if (!$event) {
            header('Location: ' . base_url('events'));
            exit;
        }

        // Get the event attendees
        $attendees = $this->eventModel->getEventAttendeesForExport($id);
        
        // Set headers for CSV download
        $filename = 'event_' . $id . '_report_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for proper Excel encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write event details
        fputcsv($output, ['Event Details']);
        fputcsv($output, ['Name', $event['name']]);
        fputcsv($output, ['Description', $event['description']]);
        fputcsv($output, ['Date', $event['date']]);
        fputcsv($output, ['Capacity', $event['capacity']]);
        fputcsv($output, []);  // Empty line for spacing

        // Write attendees header
        fputcsv($output, ['Attendees List']);
        fputcsv($output, ['No.', 'Name', 'Phone', 'NID', 'Registration Date']);

        // Write attendees data
        $counter = 1;
        foreach ($attendees as $attendee) {
            fputcsv($output, [
                $counter++,
                $attendee['name'],
                $attendee['phone'],
                $attendee['nid'],
                $attendee['registration_date']
            ]);
        }

        // Close the output stream
        fclose($output);
        exit;
    }

    // Get Event Details API
    public function getEventDetailsApi($id)
    {
        $event = $this->eventModel->getEventById($id);
        
        // If the event is not found, return an error
        if (!$event) {
            Helper::jsonResponse(['error' => 'Event not found'], 404);
        }

        // Prepare the response
        $response = [
            'name' => $event['name'],
            'description' => $event['description'],
            'date' => $event['date'],
            'capacity' => $event['capacity'],
            'attendees_count' => $this->eventModel->getAttendeesCount($id)
        ];

        Helper::jsonResponse($response);
    }

    // Build Search Filters
    private function buildSearchFilters(): array
    {
        $filters = [];
        // Query for name
        if ($name = trim(filter_input(INPUT_GET, 'name'))) {
            $filters['name'] = ['AND e.name LIKE :name', "%{$name}%"];
        }
        // Query for date
        if ($date = trim(filter_input(INPUT_GET, 'date'))) {
            $filters['date'] = ['AND DATE(e.date) = :date', $date];
        }
        // Query for capacity
        if ($capacity = filter_input(INPUT_GET, 'capacity', FILTER_VALIDATE_INT)) {
            $filters['capacity'] = ['AND e.capacity >= :capacity', $capacity];
        }
        
        return $filters;
    }

    // Get Sorting Params
    private function getSortingParams()
    {
        // Define allowed sort columns
        $allowedSortColumns = ['name', 'date', 'capacity'];

        // Get the sort column and order
        $sortColumn = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns) ? $_GET['sort'] : 'date';
        $sortOrder = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';

        // Return the sort column and order
        return [$sortColumn, $sortOrder];
    }

    // Handle Error
    private function handleError()
    {
        return Helper::view('backend/events/index.php', [
            'error' => "An error occurred while fetching the events.",
            'events' => [],
            'current_page' => 1,
            'total_pages' => 1,
            'per_page' => 2,
            'total_records' => 0
        ]);
    }
    
    // Sanitize Event Data
    private function sanitizeEventData(array $data): array
    {
        return array_map('trim', [
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'date' => $data['date'] ?? '',
            'capacity' => $data['capacity'] ?? ''
        ]);
    }

    // Validate Event Data
    private function validateEventData(array $data): array
    {
        $errors = [];

        // Check if the name is between the minimum and maximum length
        if (strlen($data['name']) < self::MIN_NAME_LENGTH || strlen($data['name']) > self::MAX_NAME_LENGTH) {
            $errors['name'] = sprintf('Event name must be between %d and %d characters.', 
                self::MIN_NAME_LENGTH, self::MAX_NAME_LENGTH);
        }

        // Check if the description is at least the minimum length
        if (strlen($data['description']) < self::MIN_DESCRIPTION_LENGTH) {
            $errors['description'] = 'Description must be at least ' . self::MIN_DESCRIPTION_LENGTH . ' characters long.';
        }

        // Check if the capacity is a positive number
        if (!is_numeric($data['capacity']) || $data['capacity'] <= 0) {
            $errors['capacity'] = 'Capacity must be a positive number.';
        }

        // Check if the date is empty
        if (empty($data['date'])) {
            $errors['date'] = 'Please select a valid date.';
        } else {
            try {
                // Check if the date is a valid date
                $selectedDate = new \DateTime($data['date']);
                $today = new \DateTime();
                $today->setTime(0, 0, 0);
                
                if ($selectedDate < $today) {
                    $errors['date'] = 'Date cannot be in the past.';
                }
            } catch (\Exception $e) {
                $errors['date'] = 'Invalid date format.';
            }
        }

        return $errors;
    }
}
