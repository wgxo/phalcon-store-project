<?php
declare(strict_types=1);


use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class PollsOptionsController extends ControllerBase
{
    /**
     * Index action
     * @param $poll_id
     */
    public function indexAction($poll_id)
    {
        $this->setPollFields($poll_id);
    }

    /**
     * Searches for polls_options
     */
    public function searchAction()
    {
        $numberPage = $this->request->getQuery('page', 'int', 1);
        $parameters = Criteria::fromInput($this->di, 'PollsOptions', $_GET)->getParams();
        $parameters['order'] = "id";

        $poll_id = $this->request->getQuery('poll_id', 'int', 1);

        $paginator = new Paginator(
            [
                'model' => 'PollsOptions',
                'parameters' => $parameters,
                'limit' => 10,
                'page' => $numberPage,
            ]
        );

        $paginate = $paginator->paginate();

        if (0 === $paginate->getTotalItems()) {
            $this->flash->notice("The search did not find any polls_options");

            $this->dispatcher->forward([
                "controller" => "polls_options",
                "action" => "new",
                'params' => [
                    'poll_id' => $poll_id,
                ]
            ]);

            return;
        }

        $this->setPollFields($poll_id);
        $this->view->page = $paginate;
    }

    /**
     * Displays the creation form
     */
    public function newAction($poll_id)
    {
        $this->setPollFields($poll_id);
        $this->tag::setDefault("number_votes", 0);
    }

    /**
     * Edits a polls_option
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $polls_option = PollsOptions::findFirstByid($id);
            if (!$polls_option) {
                $this->flash->error("polls_option was not found");

                $this->dispatcher->forward([
                    'controller' => "polls_options",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $polls_option->id;

            $this->tag::setDefault("id", $polls_option->id);
            $this->tag::setDefault("name", $polls_option->name);
            $this->tag::setDefault("number_votes", $polls_option->number_votes);
            $this->tag::setDefault("poll_id", $polls_option->poll_id);

        }
    }

    /**
     * Creates a new polls_option
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

        $polls_option = new PollsOptions();
        $polls_option->name = $this->request->getPost("name", "string");
        $polls_option->number_votes = $this->request->getPost("number_votes", "int");
        $polls_option->poll_id = $this->request->getPost("poll_id", "int");

        if (!$polls_option->save()) {
            foreach ($polls_option->getMessages() as $message) {
                $this->flash->error(print_r($message, true));
            }
        } else {
            $this->flash->success("polls_option was created successfully");
        }

        $this->tag::setDefault("name", '');

        $this->dispatcher->forward([
            'controller' => "polls_options",
            'action' => 'new',
            'params' => [
                'poll_id' => $polls_option->poll_id,
            ]
        ]);
    }

    /**
     * Saves a polls_option edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "polls_options",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $polls_option = PollsOptions::findFirstByid($id);

        if (!$polls_option) {
            $this->flash->error("polls_option does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "polls_options",
                'action' => 'index'
            ]);

            return;
        }

        $polls_option->name = $this->request->getPost("name", "string");
        $polls_option->number_votes = $this->request->getPost("number_votes", "int");
        $polls_option->poll_id = $this->request->getPost("poll_id", "int");


        if (!$polls_option->save()) {

            foreach ($polls_option->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "polls_options",
                'action' => 'edit',
                'params' => [$polls_option->id]
            ]);

            return;
        }

        $this->flash->success("polls_option was updated successfully");

        $this->dispatcher->forward([
            'controller' => "polls_options",
            'action' => 'index'
        ]);
    }

    /**
     * Increments a polls_option number of votes
     * @param $id
     */
    public function voteAction($id)
    {
        $polls_option = PollsOptions::findFirstByid($id);

        if (!$polls_option) {
            $this->flash->error("polls_option does not exist " . $id);
            $this->dispatcher->forward([
                'controller' => "index",
                'action' => 'index'
            ]);

            return;
        }

        $polls_option->number_votes++;

        if (!$polls_option->save()) {
            foreach ($polls_option->getMessages() as $message) {
                $this->flash->error($message);
            }
        } else {
            $this->flash->success("polls_option was voted successfully");
        }

        $this->dispatcher->forward([
            'controller' => "polls",
            'action' => 'view',
            'params' => [
                'id' => $polls_option->poll_id,
            ]
        ]);
    }

    /**
     * Deletes a polls_option
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $polls_option = PollsOptions::findFirstByid($id);
        if (!$polls_option) {
            $this->flash->error("polls_option was not found");

            $this->dispatcher->forward([
                'controller' => "polls_options",
                'action' => 'index'
            ]);

            return;
        }

        if (!$polls_option->delete()) {

            foreach ($polls_option->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "polls_options",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("polls_option was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "polls_options",
            'action' => "index"
        ]);
    }

    /**
     * @param $poll_id
     */
    public function setPollFields($poll_id): void
    {
        $poll = Polls::findFirstByid($poll_id);
        if (!$poll) {
            $this->flash->error("poll was not found");

            $this->dispatcher->forward([
                'controller' => "polls",
                'action' => 'index'
            ]);

            return;
        }

        $this->view->poll_id = $poll->id;
        $this->view->question = $poll->question;

        $this->tag::setDefault("poll_id", $poll->id);
        $this->tag::setDefault("question", $poll->question);
    }
}
