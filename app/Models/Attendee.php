<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;

class Attendee extends BaseModel
{
    private const SELECT_FIELDS = 'id, name, phone, nid, created_at';

    // Create Attendee
    public function createAttendee(array $data): bool
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO attendees (name, phone, nid, created_at) 
                VALUES (:name, :phone, :nid, :created_at)
            ');
            
            return $stmt->execute([
                ':name' => $data['name'],
                ':phone' => $data['phone'],
                ':nid' => $data['nid'],
                ':created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log("Create Attendee Error: " . $e->getMessage());
            throw new Exception('Failed to create attendee');
        }
    }

    // Update Attendee
    public function update($id, $data)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE attendees SET name = :name, phone = :phone, nid = :nid, updated_at = :updated_at WHERE id = :id
            ');
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':phone' => $data['phone'],
                ':nid' => $data['nid'],
                ':updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\PDOException $e) {
            error_log('Update Attendee Error: ' . $e->getMessage());
            return false;
        }
    }

    // Delete Attendee
    public function delete($id)
    {
        try {
            // Delete Attendee from event_attendees table
            $stmt = $this->pdo->prepare('DELETE FROM event_attendees WHERE attendee_id = :attendee_id');
            $stmt->execute([':attendee_id' => $id]);

            // Delete Attendee from attendees table
            $stmt = $this->pdo->prepare('DELETE FROM attendees WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete Attendee Error: " . $e->getMessage());
            return false;
        }
    }

    // Register Attendee for an Event
    public function registerAttendee(array $data): array
    {
        $this->pdo->beginTransaction();

        try {
            // Get Attendee by NID
            $attendee = $this->getAttendeeByNid($data['nid']);
            // Create New Attendee if not found
            $attendeeId = $attendee ? $attendee['id'] : $this->createNewAttendee($data);

            // Create Event Registration
            $this->createEventRegistration($data['event_id'], $attendeeId);

            $this->pdo->commit();
            return ['success' => true, 'attendee_id' => $attendeeId];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Registration Error: " . $e->getMessage());
            throw new Exception('Registration failed');
        }
    }

    // Create New Attendee
    private function createNewAttendee(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO attendees (name, phone, nid, created_at) 
            VALUES (:name, :phone, :nid, :created_at)
        ');
        
        $stmt->execute([
            ':name' => $data['name'],
            ':phone' => $data['phone'],
            ':nid' => $data['nid'],
            ':created_at' => date('Y-m-d H:i:s')
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    // Create Event Registration
    private function createEventRegistration(int $eventId, int $attendeeId): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO event_attendees (event_id, attendee_id, registration_date) 
            VALUES (:event_id, :attendee_id, :registration_date)
        ');

        $stmt->execute([
            ':event_id' => $eventId,
            ':attendee_id' => $attendeeId,
            ':registration_date' => date('Y-m-d H:i:s')
        ]);
    }

    // Get Total Records
    public function getTotalRecords(string $filterQuery, array $params): int
    {
        try {
            $query = "SELECT COUNT(*) as total FROM attendees $filterQuery";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting total records: " . $e->getMessage());
            throw new Exception('Failed to get total records');
        }
    }

    // Get Attendees
    public function getAttendees(string $filterQuery, array $params, int $offset, int $limit): array
    {
        try {
            $query = "SELECT " . self::SELECT_FIELDS . " FROM attendees $filterQuery LIMIT :offset, :limit";
            $stmt = $this->pdo->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching attendees: " . $e->getMessage());
            throw new Exception('Failed to fetch attendees');
        }
    }

    // Get Attendee by NID
    public function getAttendeeByNid(string $nid): ?array
    {
        $stmt = $this->pdo->prepare('SELECT ' . self::SELECT_FIELDS . ' FROM attendees WHERE nid = :nid');
        $stmt->execute([':nid' => $nid]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Get Event Attendees
    public function getEventAttendees($eventId)
    {
        $stmt = $this->pdo->prepare('
            SELECT a.*, ea.registration_date
            FROM attendees a 
            JOIN event_attendees ea ON a.id = ea.attendee_id 
            WHERE ea.event_id = :event_id
        ');
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll();
    }

    // Get Formatted Event Attendees
    public function getFormattedEventAttendees($eventId)
    {
        $attendees = $this->getEventAttendees($eventId);
        return array_map(function($attendee) {
            return ['name' => $attendee['name'], 'phone' => $attendee['phone'], 'nid' => $attendee['nid'], 'registration_date' => $attendee['registration_date']];
        }, $attendees);
    }

    // Check Duplicate Registration
    public function checkDuplicateRegistration(int $eventId, string $nid): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) 
            FROM event_attendees ea 
            JOIN attendees a ON ea.attendee_id = a.id 
            WHERE ea.event_id = :event_id AND a.nid = :nid
        ');
        $stmt->execute([':event_id' => $eventId, ':nid' => $nid]);
        return (bool) $stmt->fetchColumn();
    }

    // Get Attendee by ID
    public function getAttendeeById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT ' . self::SELECT_FIELDS . ' FROM attendees WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Add Existing Attendee
    public function addExistingAttendee($eventId, $attendeeId)
    {
        $this->pdo->beginTransaction();

        try {
            // Create event registration
            $stmt = $this->pdo->prepare('
                INSERT INTO event_attendees (event_id, attendee_id, registration_date) 
                VALUES (:event_id, :attendee_id, :registration_date)
            ');

            $result = $stmt->execute([
                ':event_id' => $eventId,
                ':attendee_id' => $attendeeId,
                ':registration_date' => date('Y-m-d H:i:s')
            ]);

            if (!$result) {
                throw new \Exception('Failed to create event registration');
            }

            $this->pdo->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log('Add Existing Attendee Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Get Available Attendees
    public function getAvailableAttendees($eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT ' . self::SELECT_FIELDS . ' FROM attendees 
            WHERE id NOT IN (
                SELECT attendee_id FROM event_attendees WHERE event_id = :event_id
            )
            ORDER BY name ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}