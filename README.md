# Event Management Application Documentation 

**Test User Credentials**:
- **Email**: techamin90@gmail.com
- **Password**: 297974

## Table of Contents
1. [Introduction](#introduction)
2. [Setup Instructions](#setup-instructions)
3. [Usage Instructions](#usage-instructions)
   - [Homepage](#homepage)
   - [Registration](#registration)
   - [Login](#login)
   - [Dashboard](#dashboard)
   - [Events](#events)
   - [Attendees](#attendees)
   - [Logout](#logout)

## Introduction
This Event Management Application allows users to manage events and attendees efficiently. Users can register, log in, create events, and manage attendees with various functionalities. The application includes security features such as input sanitization, and rate limiting to enhance user experience and protect against common vulnerabilities.

## Setup Instructions
1. **Clone the Repository**: 
   Clone the repository to your local machine using:
   ```bash
   git clone https://github.com/AL4M1N/event.git
   ```

2. **Database Setup**:
   - Import the SQL file into your MySQL database. Ensure the database is named `event_management`.
   - Update the database connection details in `app/config/database.php`:
     ```php
     // Example configuration
     'host' => 'localhost',
     'dbname' => 'event_management',
     'username' => 'your_username',
     'password' => 'your_password',
     ```


3. **Install Dependencies**:
   - Ensure you have Composer installed and run:
   ```bash
   composer install
   ```

4. **Start the Server**:
   - Use a local server (like XAMPP, MAMP, or built-in PHP server) and create a virtual host to run the application.

## Usage Instructions

### Homepage
- Navigate to the root URL (`/`) to access the homepage.
- Users can choose to log in or register from this page.

### Registration
- Access the registration page at `/register`.
- Fill in the required fields:
  - **Name**: Must be more than 3 characters.
  - **Email**: Must be unique and in a valid format.
  - **Password**: Must match the confirmation password.
- If validation fails, error messages will be displayed below each field.
- Upon successful registration, the password will be hashed and stored securely.
- Duplicate registration attempts will be handled gracefully, providing clear error messages.

### Login
- Navigate to `/login` to access the login page.
- Enter the registered email and password.
- If the credentials are incorrect, an error message will be displayed.
- The application implements rate limiting, allowing a maximum of 5 login attempts to prevent brute-force attacks.
- If the maximum attempts are reached, the user will be temporarily locked out.

### Dashboard
- After logging in, users will be redirected to the dashboard at `/dashboard`.
- The dashboard displays a welcome message and a menu with four items: Dashboard, Events, Attendees, and Logout.

### Events
- Access the events page a `/events`.
- Click the "+ Add Event" button to register a new event.
- Events are displayed with pagination (2 per page).
- Each event card includes:
  - **Event Name**: Displayed on the left.
  - **Action Buttons**:
    1. **Edit**: Opens the edit event page.
    2. **Delete**: Prompts for confirmation before deleting the event and its attendees.
    3. **CSV**: Generates a CSV file with event details and attendees.
    4. **Add Attendee**: Opens a modal to add attendees to the event.
- The modal allows:
  - Selecting existing attendees from a dropdown.
  - The dropdown will be populated with attendees who are not already registered for the event.
  - Registering new attendees with validation for name, phone number, and NID.
  - If the event has reached maximum capacity, the user will be informed that the event has reached maximum capacity.
  - If a new attendee is registered but the event has reached maximum capacity, the user will be informed that the attendee has been registered successfully but the event has reached maximum capacity.
- Duplicate registrations for events will be checked, and appropriate error messages will be displayed.
- **Filter Section**: Users can filter events by:
  - **Event Name**
  - **Date**
  - **Minimum Capacity**
  - **Sort By** (options: date, name, capacity)
  - **Order** (options: ascending and descending)
  - A reset button is available to clear filters and show all results.

- **Add/Edit Event**: 
  - Users can add a new event by providing:
    - **Event Name**: Must be more than 3 characters.
    - **Description**: Must be more than 10 characters.
    - **Date**: Must be a valid date, can't be in the past.
    - **Capacity**: Must be a positive number.
  - Validation will ensure that all fields are filled out correctly before submission.
- **API Endpoint**: Fetch Event Details
  - **Endpoint**: `/api/events/{id}`
  - **Method**: GET
  - **Description**: This endpoint retrieves the details of a specific event by its ID.
  - **Response Format**:
    ```json
    {
        "name": "Tech Innovators Conference 2025",
        "description": "Join us for a groundbreaking event where the brightest minds in technology come together to discuss the latest advancements, share innovative ideas, and network with industry leaders. Featuring keynote speakers, panel discussions, and hands-on workshops.",
        "date": "2025-02-08",
        "capacity": 6,
        "attendees_count": 5
    }
    ```

### Attendees
- Navigate to `/attendees` to manage attendees.
- Click the "+ Add Attendee" button to register a new attendee.
- Attendees are displayed with pagination (5 per page).
- Use the filter section to search attendees by name, phone, or NID.
- A table displays all attendees with pagination, showing serial, name, phone, and NID.
- Action buttons include:
  - **Edit**: Redirects to the edit page for the selected attendee.
  - **Delete**: Prompts for confirmation before deletion.
- **Add/Edit Attendee**:
  - Users can register a new attendee by providing:
    - **Name**: Must be more than 3 characters.
    - **Phone Number**: Must be 11 digits and start with '01'.
    - **NID**: Must be a numeric value between 10-17 digits.
  - Validation will ensure that all fields are filled out correctly before submission.
- Input sanitization is implemented to ensure that all user inputs are safe and secure.

### Logout
- Click the "Logout" button to log out of the application.
- After logging out, users cannot access any links that were available post-login.
- The application includes session timeout functionality to enhance security, redirecting users to the login page if their session has expired.

