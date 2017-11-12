<?php


interface StoredMeAttachments 
{

  /*Create attachements under a storedMe Record*/
    public function createAttachements($sm,$attachements);
   /*Delete a attachements under a storedMe Record*/
    public function deleteAttachements($sm,$attachements);
   /*Update a attachements under a storedMe Record*/
    public function updateAttachements($sm,$attachements,$updates);
   /*Get attachementss under a storedMe Record*/
    public function getAttachements($sm);

}


?>