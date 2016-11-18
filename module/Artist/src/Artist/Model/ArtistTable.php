<?php

namespace Artist\Model;

 use Zend\Db\TableGateway\TableGateway;

 class ArtistTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getArtist($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveArtist(Artist $artist)
     {
         $data = array(
             'name' => $artist->name,
             'description'  => $artist->description,
         );

         $id = (int) $artist->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getArtist($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Artist id does not exist');
             }
         }
     }

     public function deleteArtist($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }