
   <?php
   //src/storeMe/entities/smusers.php
    /**
     * @Entity
     * @Table(name="sm_users")
     */
    class smuser
    {
        /**@Id @Column(type="integer", name="sm_userssmeid") @GeneratedValue **/
        private $id;
        /** @Column(type="integer",name="sm_userssmid") **/
        private $storemeid;
        /** @Column(type="string",name="sm_usersemail") **/
        private $email;
        /** @Column(type="datetime",name="sm_usersdatecreated") **/
        private $datecreated;
        /** @Column(type="datetime",name="sm_usersdateupdated") **/
        private $dateupdated;

        public function getId()
        {
            return $this->id;
        }
        public function setEmail($em){
            $this->email=$em;
        }
        public function getEmail($em){
            return $this->email;
        }

    }
      