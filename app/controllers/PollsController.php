<?php
declare(strict_types=1);


use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model;


class PollsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        //
    }

    /**
     * Searches for polls
     */
    public function searchAction()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $parameters = Criteria::fromInput($this->di, 'Polls', $_GET)->getParams();
        $parameters['order'] = "id";

        $paginator = new Model(
            [
                'model' => 'Polls',
                'parameters' => $parameters,
                'limit' => 10,
                'page' => $numberPage,
            ]
        );

        $paginate = $paginator->paginate();

        if (0 === $paginate->getTotalItems()) {
            $this->flash->notice("The search did not find any polls");

            $this->dispatcher->forward([
                "controller" => "polls",
                "action" => "index"
            ]);

            return;
        }

        $this->view->page = $paginate;
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        //
    }

    /**
     * Displays the poll
     *
     * @param string $id
     */
    public function viewAction($id)
    {
        $poll = Polls::findFirstByid($id);
        if (!$poll) {
            $this->flash->error("poll was not found");

            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'index'
            ]);

            return;
        }

//        $options = PollsOptions::find([
//                'conditions' => "poll_id=:id:",
//                'bind' => [
//                    'id' => $poll->id
//                ]]
//        );

        $this->view->poll = $poll;
        $this->view->options = $poll->Options; // model hasMany alias
    }

    /**
     * Edits a poll
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $poll = Polls::findFirstByid($id);
            if (!$poll) {
                $this->flash->error("poll was not found");

                $this->dispatcher->forward([
                    'controller' => "polls",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $poll->id;

            $this->tag::setDefault("id", $poll->id);
            $this->tag::setDefault("question", $poll->question);

        }
    }

    /**
     * Creates a new poll
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "index",
                'action' => 'index'
            ]);

            return;
        }

        $poll = new Polls();
        $poll->question = $this->request->getPost("question", "string");


        if (!$poll->save()) {
            foreach ($poll->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("poll was created successfully");

        $this->dispatcher->forward([
            'controller' => "polls",
            'action' => 'view',
            'params' => [
                'id' => $poll->id,
            ]
        ]);
    }

    /**
     * Saves a poll edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $poll = Polls::findFirstByid($id);

        if (!$poll) {
            $this->flash->error("poll does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'index'
            ]);

            return;
        }

        $poll->question = $this->request->getPost("question", "string");


        if (!$poll->save()) {

            foreach ($poll->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'edit',
                'params' => [$poll->id]
            ]);

            return;
        }

        $this->flash->success("poll was updated successfully");

        $this->dispatcher->forward([
            'controller' => "polls",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a poll
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $poll = Polls::findFirstByid($id);
        if (!$poll) {
            $this->flash->error("poll was not found");

            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'index'
            ]);

            return;
        }

        if (!$poll->delete()) {

            foreach ($poll->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("poll was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "polls",
            'action' => "index"
        ]);
    }
}
