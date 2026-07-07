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
        $this->db->query("
            INSERT INTO feedback
            (email, onderwerp, bericht)
            VALUES
            (:email, :onderwerp, :bericht)
        ");
                                                                    //Velden contact pagina 
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':onderwerp', $data['onderwerp']); 
        $this->db->bind(':bericht', $data['bericht']);

        return $this->db->execute();
    }
    public function getAll()
    {
        $this->db->query("
        SELECT id, email, onderwerp, bericht, datum_aangemaakt
        FROM feedback
        ORDER BY datum_aangemaakt DESC
    ");

        return $this->db->resultSet();
    }
}