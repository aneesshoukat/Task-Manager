<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\CsrfService;
use App\Services\TaskService;
use App\Services\ValidationService;

class TaskController extends Controller
{
    private TaskService $taskService;
    private ValidationService $validationService;
    private CsrfService $csrfService;

    public function __construct()
    {
        $this->taskService = new TaskService();
        $this->validationService = new ValidationService();
        $this->csrfService = new CsrfService();
    }

    public function index(Request $request): void
    {
        $userId = $request->userId();
        $filters = [
            'status' => $request->get('status', ''),
            'priority' => $request->get('priority', ''),
            'search' => $request->get('search', ''),
            'sort' => $request->get('sort', 'latest'),
            'page' => (int) $request->get('page', 1),
            'limit' => 20,
            'from' => $request->get('from', ''),
            'to' => $request->get('to', ''),
        ];

        $result = $this->taskService->getUserTasks($userId, $filters);

        $this->render('tasks/index', [
            'tasks' => $result['items'],
            'total' => $result['total'],
            'page' => $result['page'],
            'totalPages' => $result['total_pages'],
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, array $params): void
    {
        $userId = $request->userId();
        $task = $this->taskService->getTask((int) $params['id'], $userId);

        if (!$task) {
            Response::status(404);
            $this->render('errors/404');
            return;
        }

        $this->render('tasks/show', ['task' => $task]);
    }

    public function create(Request $request): void
    {
        $this->csrfService->generate();
        $this->render('tasks/create');
    }

    public function store(Request $request): void
    {
        $data = $request->only(['title', 'description', 'priority', 'due_date']);

        $errors = $this->validationService->validateTask($data);
        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            Response::redirect('/tasks/create');
            return;
        }

        $this->taskService->createTask($request->userId(), $data);

        Session::flash('success', 'Task created successfully.');
        Response::redirect('/tasks');
    }

    public function edit(Request $request, array $params): void
    {
        $userId = $request->userId();
        $task = $this->taskService->getTask((int) $params['id'], $userId);

        if (!$task) {
            Response::status(404);
            $this->render('errors/404');
            return;
        }

        $this->csrfService->generate();
        $this->render('tasks/edit', ['task' => $task]);
    }

    public function update(Request $request, array $params): void
    {
        $data = $request->only(['title', 'description', 'priority', 'status', 'due_date']);

        $errors = $this->validationService->validateTask($data);
        if ($errors) {
            Session::flash('errors', $errors);
            Response::redirect('/tasks/' . $params['id'] . '/edit');
            return;
        }

        $updated = $this->taskService->updateTask((int) $params['id'], $request->userId(), $data);

        if (!$updated) {
            Session::flash('error', 'Task not found.');
            Response::redirect('/tasks');
            return;
        }

        Session::flash('success', 'Task updated successfully.');
        Response::redirect('/tasks');
    }

    public function destroy(Request $request, array $params): void
    {
        $deleted = $this->taskService->deleteTask((int) $params['id'], $request->userId());

        Session::flash($deleted ? 'success' : 'error', $deleted ? 'Task deleted.' : 'Task not found.');
        Response::redirect('/tasks');
    }

    public function complete(Request $request, array $params): void
    {
        $completed = $this->taskService->completeTask((int) $params['id'], $request->userId());

        Session::flash($completed ? 'success' : 'error', $completed ? 'Task completed.' : 'Task not found.');
        Response::redirect('/tasks');
    }

    public function restore(Request $request, array $params): void
    {
        $restored = $this->taskService->restoreTask((int) $params['id'], $request->userId());

        Session::flash($restored ? 'success' : 'error', $restored ? 'Task restored.' : 'Task not found.');
        Response::redirect('/tasks');
    }

    public function trashed(Request $request): void
    {
        $tasks = $this->taskService->getTrashedTasks($request->userId());
        $this->render('tasks/trashed', ['tasks' => $tasks]);
    }

    public function exportCsv(Request $request): void
    {
        $csv = $this->taskService->exportToCsv($request->userId());

        Response::header('Content-Type', 'text/csv');
        Response::header('Content-Disposition', 'attachment; filename="tasks.csv"');
        echo $csv;
        exit;
    }

    public function importCsv(Request $request): void
    {
        if (!$request->hasFile('csv_file')) {
            Session::flash('error', 'Please select a CSV file.');
            Response::redirect('/tasks');
            return;
        }

        $file = $request->file('csv_file');
        $result = $this->taskService->importFromCsv($request->userId(), $file['tmp_name']);

        Session::flash('success', "Imported {$result['imported']} tasks.");
        Response::redirect('/tasks');
    }
}
