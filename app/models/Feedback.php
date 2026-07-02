<?php

class Feedback
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function create($data)
    {
        $this->db->query('
            INSERT INTO feedback
            (naam, email, onderwerp, bericht, is_actief, opmerking)
            VALUES
            (:naam, :email, :onderwerp, :bericht, :is_actief, :opmerking)
        ');

        $this->db->bind(':naam', $data['naam']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':onderwerp', $data['onderwerp']);
        $this->db->bind(':bericht', $data['bericht']);
        $this->db->bind(':is_actief', $data['is_actief']);
        $this->db->bind(':opmerking', $data['opmerking']);

        return $this->db->execute();
    }
}