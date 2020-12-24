<?php

use Phalcon\Di\DiInterface;

class Polls extends Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $question;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->keepSnapshots(true);
        $this->setSchema("store");
        $this->setSource("polls");
        $this->hasMany('id', 'PollsOptions', 'poll_id', ['alias' => 'Options']);
        $this->addBehavior(
            new Blameable(
//                [
//                    'userCallback' => function (DiInterface $di) {
//                        return 'wgarcia';
//                    },
//                ]
            )
        );
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Polls[]|Polls|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Polls|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
