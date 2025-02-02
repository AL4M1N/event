<?php

namespace App\Models;

use PDO;
use Exception;
use PDOException;

class Event extends BaseModel
{
    // Select Fields
    private const SELECT_FIELDS = 'id, name, description, date, capacity, created_at';
    // Default Sort Column
    private const DEFAULT_SORT_COLUMN = 'date';
    // Default Sort Order
    private const DEFAULT_SORT_ORDER = 'DESC';

    // Get All Events
    public function getAllEvents(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM events');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Create Event
    public function createEvent(array $data): bool
    {
        try {
            $sql = 'INSERT INTO events (name, description, date, capacity, created_at) 
                    VALUES (:name, :description, :date, :capacity, :created_at)';
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'],
                ':description' => $data['description'],
                ':date' => $data['date'],
                ':capacity' => $data['capacity'],
                ':created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (PDOException $e) {
            error_log("Create Event Error: " . $e->getMessage());
            throw new Exception('Failed to create event');
        }
    }

    // Get Event by ID
    public function getEventById(int $id): ?array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT ' . self::SELECT_FIELDS . ' FROM events WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Get Event Error: " . $e->getMessage());
            throw new Exception('Failed to fetch event');
        }
    }

    // Update Event
    public function update(int $id, array $data): bool
    {
        try {
            $sql = 'UPDATE events 
                    SET name = :name, 
                        description = :description, 
                        date = :date, 
                        capacity = :capacity,
                        updated_at = :updated_at
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':description' => $data['description'],
                ':date' => $data['date'],
                ':capacity' => $data['capacity'],
                ':updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log("Update Event Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete Event
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM event_attendees WHERE event_id = :event_id');
            $stmt->execute([':event_id' => $id]);

            $stmt = $this->pdo->prepare('DELETE FROM events WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete Event Error: " . $e->getMessage());
            return false;
        }
    }

    // Get Event Attendees for Export
    public function getEventAttendeesForExport(int $eventId): array
    {
        try {
            $sql = 'SELECT 
                        a.name,
                        a.phone,
                        a.nid,
                        ea.registration_date
                    FROM attendees a
                    JOIN event_attendees ea ON a.id = ea.attendee_id
                    WHERE ea.event_id = :event_id
                    ORDER BY ea.registration_date ASC';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':event_id' => $eventId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Export Attendees Error: " . $e->getMessage());
            throw new Exception('Failed to export attendees');
        }
    }

    // Get Attendees Count
    public function getAttendeesCount(int $eventId): int
    {
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM event_attendees WHERE event_id = :event_id');
            $stmt->execute([':event_id' => $eventId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Get Attendees Count Error: " . $e->getMessage());
            return 0;
        }
    }

    // Get Total Filtered Records
    public function getTotalFilteredRecords(array $filters): int
    {
        try {
            $sql = "SELECT COUNT(DISTINCT e.id) as total 
                    FROM events e 
                    LEFT JOIN event_attendees ea ON e.id = ea.event_id
                    LEFT JOIN attendees a ON ea.attendee_id = a.id
                    WHERE 1=1";
            
            list($sql, $params) = $this->buildFilterQuery($sql, $filters);
            
            $stmt = $this->pdo->prepare($sql);
            $this->bindFilterParams($stmt, $params);
            $stmt->execute();
            
            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            error_log("Error getting total filtered records: " . $e->getMessage());
            throw new Exception('Failed to get total records');
        }
    }

    // Get Filtered Events
    public function getFilteredEvents(
        array $filters, 
        int $offset, 
        int $perPage, 
        string $sortColumn = self::DEFAULT_SORT_COLUMN, 
        string $sortOrder = self::DEFAULT_SORT_ORDER
    ): array {
        try {
            $sql = "SELECT e.*, 
                    (SELECT COUNT(*) FROM event_attendees ea WHERE ea.event_id = e.id) as attendee_count,
                    GROUP_CONCAT(CONCAT(a.name, '|', a.phone, '|', a.nid) SEPARATOR ';;') as attendees
                    FROM events e
                    LEFT JOIN event_attendees ea ON e.id = ea.event_id
                    LEFT JOIN attendees a ON ea.attendee_id = a.id
                    WHERE 1=1";
            
            list($sql, $params) = $this->buildFilterQuery($sql, $filters);
            
            $sql .= " GROUP BY e.id ORDER BY {$sortColumn} {$sortOrder} LIMIT :offset, :per_page";
            
            $stmt = $this->pdo->prepare($sql);
            
            $this->bindFilterParams($stmt, $params);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':per_page', $perPage, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching filtered events: " . $e->getMessage());
            throw new Exception('Failed to fetch events');
        }
    }

    // Build Filter Query
    private function buildFilterQuery(string $sql, array $filters): array
    {
        $params = [];
        foreach ($filters as $key => $filter) {
            $sql .= " " . $filter[0];
            $params[':' . $key] = $filter[1];
        }
        return [$sql, $params];
    }

    // Bind Filter Params
    private function bindFilterParams(\PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    }
}