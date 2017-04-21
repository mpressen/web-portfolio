<?php

namespace App\Models;
use App\Database\Database;
use \PDO;

class Model{

    protected $pdo = false;
    protected $table;

    public function __construct() {
        if ($this->pdo === false){
            $database = new Database();
            $this->pdo = $database->getPdo();
        }
    }

    public function query($statement, $class) {
        try{
            $req = $this->pdo->query($statement);
            $datas = $req->fetchAll(PDO::FETCH_CLASS, $class);
            return ($datas);
        }
        catch (PDOException $e){
    		die ("ERREUR PDO dans ". $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage());
    	}
    }

    public function getTable() {
        return ($this->table);
    }

    public function execute($statement, $attributes, $class, $one = false)
    {
        $req = $this->pdo->prepare($statement);
        $req->setFetchMode(PDO::FETCH_CLASS, $class);
        try{
            if ($req->execute($attributes)) {
                if ($one)
                    $data = $req->fetch();
                else
                    $data = $req->fetchAll();
                return ($data);
            }
            else
                return(false);
        }
        catch (PDOException $e){
    		die ("ERREUR PDO dans ". $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage());
    	}
    }

    public function update($id, $field, $value)
	{
        $statement =  "UPDATE ".$this->table." ";
        $statement .= "SET ".$field." = ? WHERE id = ?;";
        $attributes = array($value, $id);
        $req = $this->pdo->prepare($statement);
        $req->execute($attributes);
	}
}