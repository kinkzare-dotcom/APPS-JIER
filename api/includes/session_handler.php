<?php
/**
 * Database-backed Session Handler
 * Ensures sessions work across separate Vercel Lambdas.
 */

class DatabaseSessionHandler implements SessionHandlerInterface
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string
    {
        $stmt = $this->mysqli->prepare("SELECT data FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['data'];
        }
        return '';
    }

    public function write($id, $data): bool
    {
        $timestamp = time();
        $stmt = $this->mysqli->prepare("REPLACE INTO sessions (id, data, timestamp) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $id, $data, $timestamp);
        return $stmt->execute();
    }

    public function destroy($id): bool
    {
        $stmt = $this->mysqli->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function gc($maxlifetime): int
    {
        $old = time() - $maxlifetime;
        $stmt = $this->mysqli->prepare("DELETE FROM sessions WHERE timestamp < ?");
        $stmt->bind_param("i", $old);
        if ($stmt->execute()) {
            return $this->mysqli->affected_rows;
        }
        return 0;
    }
}

function setup_db_sessions($mysqli)
{
    $handler = new DatabaseSessionHandler($mysqli);
    session_set_save_handler($handler, true);
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}
