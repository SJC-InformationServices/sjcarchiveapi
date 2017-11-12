
   <?php
    /**
     * @Entity
     * @Table(name="sm_groups")
     */
    class smgroup
    {
        /**@Id @Column(type="integer", name="sm_groupssmeid") */
        private $id;
        /** @Column(type="integer",name="sm_groupssmid") */
        private $storemeid;
        /** @Column(type="string",name="sm_groupsemail") */
        private $email;
        /** @Column(type="datetime",name="sm_groupsdatecreated") */
        private $datecreated;
        /** @Column(type="datetime",name="sm_groupsdateupdated") */
        private $dateupdated;
    }